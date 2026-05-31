<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Model;
use Core\Request;
use Core\Response;
use Core\Session;
use Core\Uuid;
use PDO;

class SessionController
{
    public function index(): void
    {
        $db = new Model();
        $sessions = $db->table('evaluation_sessions')
            ->select('*')
            ->orderBy('created_at', 'DESC')
            ->get();

        require_once __DIR__ . '/../../views/sessions/index.php';
    }

    public function create(): void
    {
        $db = new Model();
        $criteria = $db->table('criteria')
            ->select('*')
            ->orderBy('criteria_code', 'ASC')
            ->get();

        $suppliers = $db->table('suppliers')
            ->select('*')
            ->orderBy('company_name', 'ASC')
            ->get();

        require_once __DIR__ . '/../../views/sessions/create.php';
    }

    public function store(): void
    {
        $request = new Request();
        $userId = Session::get('user_id');
        if (!$userId) {
            Response::redirect('/login');
        }

        $title       = trim($request->post('title', ''));
        $description = trim($request->post('description', ''));
        $startDate   = trim($request->post('start_date', ''));
        $endDate     = trim($request->post('end_date', ''));
        $criteriaIds = $request->post('criteria_ids', []);
        $weights     = $request->post('weights', []);
        $supplierIds = $request->post('supplier_ids', []);

        if (!is_array($criteriaIds)) {
            $criteriaIds = [];
        }
        if (!is_array($weights)) {
            $weights = [];
        }
        if (!is_array($supplierIds)) {
            $supplierIds = [];
        }

        $errors = [];
        if ($title === '') {
            $errors[] = 'Nama sesi wajib diisi.';
        }
        if ($startDate === '' || $endDate === '') {
            $errors[] = 'Tanggal mulai dan tanggal selesai wajib diisi.';
        }
        if (empty($criteriaIds)) {
            $errors[] = 'Minimal pilih satu kriteria.';
        }
        if (empty($supplierIds)) {
            $errors[] = 'Minimal pilih satu supplier.';
        }

        foreach ($criteriaIds as $cid) {
            $w = isset($weights[$cid]) ? (float) $weights[$cid] : 0;
            if ($w <= 0) {
                $errors[] = 'Bobot kriteria harus lebih dari 0.';
                break;
            }
        }

        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            Session::setFlash('old', $request->all());
            Response::redirect('/sessions/create');
        }

        $db = new Model();
        $userExists = $db->table('users')->where('id', '=', $userId)->first();
        if (!$userExists) {
            Session::destroy();
            Session::setFlash('error', 'Data pengguna tidak ditemukan, silakan login ulang.');
            Response::redirect('/login');
        }

        $sessionId = Uuid::generate();
        $sessionCode = 'SES-' . strtoupper(substr(uniqid(), -6));

        $db->table('evaluation_sessions')->insert([
            'id'          => $sessionId,
            'session_code'=> $sessionCode,
            'title'       => $title,
            'description' => $description ?: null,
            'start_date'  => $startDate,
            'end_date'    => $endDate,
            'status'      => 'draft',
            'created_by'  => $userId,
        ]);

        foreach ($criteriaIds as $cid) {
            $cid = trim($cid);
            if ($cid === '' || !Uuid::isValid($cid)) {
                continue;
            }
            $w = isset($weights[$cid]) ? (float) $weights[$cid] : 0;
            $db->table('session_criteria')->insert([
                'id'          => Uuid::generate(),
                'session_id'  => $sessionId,
                'criteria_id' => $cid,
                'weight'      => $w,
            ]);
        }

        foreach ($supplierIds as $sid) {
            $sid = trim($sid);
            if ($sid === '' || !Uuid::isValid($sid)) {
                continue;
            }
            $db->table('session_suppliers')->insert([
                'id'          => Uuid::generate(),
                'session_id'  => $sessionId,
                'supplier_id' => $sid,
            ]);
        }

        Session::setFlash('success', 'Sesi penilaian berhasil dibuat.');
        Response::redirect('/sessions');
    }

    public function destroy(): void
    {
        $request = new Request();
        $id = trim($request->post('id', ''));

        if ($id === '' || !Uuid::isValid($id)) {
            Session::setFlash('error', 'ID sesi tidak valid.');
            Response::redirect('/sessions');
        }

        $db = new Model();
        $session = $db->table('evaluation_sessions')->where('id', '=', $id)->first();
        if (!$session) {
            Session::setFlash('error', 'Sesi tidak ditemukan.');
            Response::redirect('/sessions');
        }

        $db->table('evaluation_sessions')->where('id', '=', $id)->delete();

        Session::setFlash('success', 'Sesi penilaian berhasil dihapus.');
        Response::redirect('/sessions');
    }

    public function show(): void
    {
        $request = new Request();
        $sessionId = trim($request->get('id', ''));

        if ($sessionId === '' || !Uuid::isValid($sessionId)) {
            Session::setFlash('error', 'ID sesi tidak valid.');
            Response::redirect('/sessions');
        }

        $db = new Model();
        $session = $db->table('evaluation_sessions')->where('id', '=', $sessionId)->first();
        if (!$session) {
            Session::setFlash('error', 'Sesi tidak ditemukan.');
            Response::redirect('/sessions');
        }

        $dbConn = \Core\Database::connection();

        $stmt = $dbConn->prepare("
            SELECT sc.criteria_id, sc.weight, c.criteria_code, c.criteria_name, c.type
            FROM session_criteria sc
            JOIN criteria c ON sc.criteria_id = c.id
            WHERE sc.session_id = ?
            ORDER BY c.criteria_code ASC
        ");
        $stmt->execute([$sessionId]);
        $criteria = $stmt->fetchAll();

        $stmt = $dbConn->prepare("
            SELECT ss.supplier_id, s.supplier_code, s.company_name, s.pic_name
            FROM session_suppliers ss
            JOIN suppliers s ON ss.supplier_id = s.id
            WHERE ss.session_id = ?
            ORDER BY s.company_name ASC
        ");
        $stmt->execute([$sessionId]);
        $suppliers = $stmt->fetchAll();

        $stmt = $dbConn->prepare("
            SELECT supplier_id, criteria_id, raw_value
            FROM session_evaluations
            WHERE session_id = ?
        ");
        $stmt->execute([$sessionId]);
        $evaluations = $stmt->fetchAll();

        $matrix = [];
        foreach ($evaluations as $e) {
            $matrix[$e['supplier_id']][$e['criteria_id']] = $e['raw_value'];
        }

        require_once __DIR__ . '/../../views/sessions/input.php';
    }

    public function saveMatrix(): void
    {
        $request = new Request();
        $sessionId = trim($request->post('session_id', ''));
        $matrix = $request->post('matrix', []);

        if ($sessionId === '' || !Uuid::isValid($sessionId)) {
            Session::setFlash('error', 'ID sesi tidak valid.');
            Response::redirect('/sessions');
        }

        if (!is_array($matrix) || empty($matrix)) {
            Session::setFlash('error', 'Matriks nilai tidak boleh kosong.');
            Response::redirect('/sessions/show?id=' . $sessionId);
        }

        $db = new Model();
        $db->table('session_evaluations')->where('session_id', '=', $sessionId)->delete();

        foreach ($matrix as $supplierId => $criteriaValues) {
            if (!is_array($criteriaValues)) {
                continue;
            }
            foreach ($criteriaValues as $criteriaId => $rawValue) {
                $rawValue = trim((string) $rawValue);
                if ($rawValue === '') {
                    continue;
                }
                $db->table('session_evaluations')->insert([
                    'id' => Uuid::generate(),
                    'session_id' => $sessionId,
                    'supplier_id' => $supplierId,
                    'criteria_id' => $criteriaId,
                    'raw_value' => (float) $rawValue,
                ]);
            }
        }

        $db->table('evaluation_sessions')
            ->where('id', '=', $sessionId)
            ->update(['status' => 'in_progress']);

        Session::setFlash('success', 'Matriks nilai berhasil disimpan.');
        Response::redirect('/sessions/result?id=' . $sessionId);
    }

    public function complete(): void
    {
        $request = new Request();
        $id = trim($request->post('id', ''));

        if ($id === '' || !Uuid::isValid($id)) {
            Session::setFlash('error', 'ID sesi tidak valid.');
            Response::redirect('/sessions');
        }

        $db = new Model();
        $session = $db->table('evaluation_sessions')->where('id', '=', $id)->first();
        if (!$session) {
            Session::setFlash('error', 'Sesi tidak ditemukan.');
            Response::redirect('/sessions');
        }

        $db->table('evaluation_sessions')
            ->where('id', '=', $id)
            ->update(['status' => 'completed']);

        Session::setFlash('success', 'Sesi evaluasi berhasil diselesaikan dan dikunci.');
        Response::redirect('/sessions');
    }

    public function calculate(): void
    {
        $request = new Request();
        $sessionId = trim($request->get('id', ''));

        if ($sessionId === '' || !Uuid::isValid($sessionId)) {
            Session::setFlash('error', 'ID sesi tidak valid.');
            Response::redirect('/sessions');
        }

        $db = new Model();
        $session = $db->table('evaluation_sessions')->where('id', '=', $sessionId)->first();
        if (!$session) {
            Session::setFlash('error', 'Sesi tidak ditemukan.');
            Response::redirect('/sessions');
        }

        $dbConn = \Core\Database::connection();

        $stmt = $dbConn->prepare("
            SELECT sc.criteria_id, sc.weight, c.criteria_code, c.criteria_name, c.type
            FROM session_criteria sc
            JOIN criteria c ON sc.criteria_id = c.id
            WHERE sc.session_id = ?
            ORDER BY c.criteria_code ASC
        ");
        $stmt->execute([$sessionId]);
        $criteria = $stmt->fetchAll();

        $stmt = $dbConn->prepare("
            SELECT ss.supplier_id, s.supplier_code, s.company_name
            FROM session_suppliers ss
            JOIN suppliers s ON ss.supplier_id = s.id
            WHERE ss.session_id = ?
            ORDER BY s.company_name ASC
        ");
        $stmt->execute([$sessionId]);
        $suppliers = $stmt->fetchAll();

        if (empty($criteria) || empty($suppliers)) {
            Session::setFlash('error', 'Data kriteria atau supplier tidak ditemukan untuk sesi ini.');
            Response::redirect('/sessions/show?id=' . $sessionId);
        }

        $stmt = $dbConn->prepare("
            SELECT supplier_id, criteria_id, raw_value
            FROM session_evaluations
            WHERE session_id = ?
        ");
        $stmt->execute([$sessionId]);
        $evaluations = $stmt->fetchAll();

        $rawMatrix = [];
        foreach ($suppliers as $s) {
            foreach ($criteria as $c) {
                $rawMatrix[$s['supplier_id']][$c['criteria_id']] = 0.0;
            }
        }
        foreach ($evaluations as $e) {
            $rawMatrix[$e['supplier_id']][$e['criteria_id']] = (float) $e['raw_value'];
        }

        // Normalisasi bobot
        $totalWeight = (float) array_sum(array_column($criteria, 'weight'));
        $normalizedWeights = [];
        foreach ($criteria as $c) {
            $normalizedWeights[$c['criteria_id']] = $totalWeight > 0
                ? round((float) $c['weight'] / $totalWeight, 4)
                : 0.0;
        }

        // Min / Max per kriteria
        $minMax = [];
        foreach ($criteria as $c) {
            $values = array_column($rawMatrix, $c['criteria_id']);
            $minMax[$c['criteria_id']] = [
                'min' => min($values),
                'max' => max($values),
            ];
        }

        // Matriks Utility
        $utilityMatrix = [];
        foreach ($suppliers as $s) {
            $sid = $s['supplier_id'];
            foreach ($criteria as $c) {
                $cid = $c['criteria_id'];
                $value = $rawMatrix[$sid][$cid];
                $min = $minMax[$cid]['min'];
                $max = $minMax[$cid]['max'];

                if ($max == $min) {
                    $utility = 1.0;
                } elseif ($c['type'] === 'cost') {
                    $utility = ($max - $value) / ($max - $min);
                } else {
                    $utility = ($value - $min) / ($max - $min);
                }
                $utilityMatrix[$sid][$cid] = round($utility, 4);
            }
        }

        // Hasil akhir
        $finalScores = [];
        foreach ($suppliers as $s) {
            $sid = $s['supplier_id'];
            $total = 0.0;
            foreach ($criteria as $c) {
                $cid = $c['criteria_id'];
                $total += $normalizedWeights[$cid] * $utilityMatrix[$sid][$cid];
            }
            $finalScores[$sid] = round($total, 4);
        }

        arsort($finalScores);
        $rankings = [];
        $rank = 1;
        foreach ($finalScores as $sid => $score) {
            $rankings[$sid] = $rank++;
        }

        require_once __DIR__ . '/../../views/sessions/result.php';
    }
}
