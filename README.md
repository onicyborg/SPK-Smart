# SPK Pemilihan Supplier Ban (Native PHP Framework)

**Live URL:** [https://spk-smart.skripsian.site](https://spk-smart.skripsian.site)

Sistem Pendukung Keputusan berbasis web yang menggunakan metode **SMART** (Simple Multi Attribute Rating Technique) untuk pemilihan supplier ban bandara. Dibangun menggunakan framework PHP MVC ringan kustom dengan **zero dependencies** — tanpa Composer, tanpa package pihak ketiga.

---

## Fitur

- **Dashboard Eksekutif**: Ringkasan data statistik dan aktivitas terbaru dalam satu tampilan.
- **Manajemen Master Data**: CRUD untuk Supplier Ban Bandara dan Kriteria Penilaian (dengan penentuan bobot & tipe Benefit/Cost).
- **Manajemen User**: Sistem autentikasi dan peran pengguna (Admin, Staff, Pimpinan).
- **Sesi Penilaian (SPK)**: Pembuatan sesi evaluasi menggunakan form Wizard/Stepper.
- **Mesin Perhitungan SMART**: Kalkulasi otomatis nilai utility, normalisasi bobot, dan perankingan hasil akhir secara transparan di layar.
- **UI/UX Modern**: Dibangun menggunakan template Metronic (Bootstrap 5).

---

## Prasyarat

- **PHP 8.1** atau lebih tinggi
- Ekstensi **PDO** aktif (`php-pdo`)
- **MySQL** (disarankan) atau **PostgreSQL**
- Ekstensi driver database (`php-mysql` atau `php-pgsql`)

---

## Inisialisasi & Setup

### Langkah 1: Clone Repository

```bash
git clone <repository-url> spk-smart
cd spk-smart
```

### Langkah 2: Konfigurasi Environment

Salin file environment contoh dan sesuaikan kredensial database:

```bash
cp .env.example .env
```

Edit `.env` dengan pengaturan database kamu:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=spk_smart
DB_USER=root
DB_PASS=root
```

- Atur `DB_CONNECTION` ke `mysql` atau `pgsql` sesuai database yang digunakan.
- Buat database terlebih dahulu (`spk_smart` pada contoh di atas) sebelum menjalankan migrasi.

### Langkah 3: Jalankan Migrasi

```bash
php migrate.php
```

Perintah ini membuat seluruh tabel yang diperlukan: `users`, `criteria`, `suppliers`, `evaluation_sessions`, `session_criteria`, `session_suppliers`, `session_scores`, dan `session_evaluations`.

### Langkah 4: Jalankan Seeder

```bash
php seed.php
```

Perintah ini mengisi database dengan data awal untuk **Users**, **Suppliers**, dan **Criteria**, sehingga aplikasi dapat langsung digunakan.

### Langkah 5: Jalankan Server Development

```bash
php -S localhost:8080 -t public/
```

Buka `http://localhost:8080` di browser.

---

## Kredensial Login Default

Akun berikut dibuat secara otomatis setelah menjalankan `php seed.php`:

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | `Qwerty123*` |
| Staff | `staff` | `Qwerty123*` |
| Pimpinan | `pimpinan` | `Qwerty123*` |

---

## Perintah CLI yang Tersedia

| Perintah | Deskripsi |
|----------|-----------|
| `php migrate.php` | Menjalankan seluruh migrasi database yang belum dieksekusi dalam satu transaksi. Mencatat migrasi yang sudah dijalankan untuk menghindari duplikasi. |
| `php make_migration.php <name>` | Menghasilkan file migrasi baru yang sudah diberi timestamp di `database/migrations/`. Contoh: `php make_migration.php create_products_table` |
| `php seed.php` | Menjalankan seluruh seeder di `database/seeders/` untuk mengisi database dengan data awal. |

---

## Referensi Arsitektur

Proyek ini dibangun menggunakan native PHP MVC semi-framework kustom. Untuk spesifikasi teknis mendalam mengenai core engine — termasuk PSR-4 Autoloader kustom, Router, Query Builder, layer PDO dual-database, parser .env, dan sistem migrasi — lihat dokumen blueprint:

**[`codebase.md`](codebase.md)**

---

## Database Schema

Berikut adalah diagram Entity Relationship (ERD) yang menggambarkan struktur database sistem SPK Pemilihan Supplier Ban Bandara:

![Database Schema](schema-spk-smart.png)

### Penjelasan Skema

Database terdiri dari **8 tabel utama** yang saling terhubung melalui foreign key:

- **`users`** — Menyimpan data pengguna sistem dengan peran (admin, staff, pimpinan).
- **`criteria`** — Master data kriteria penilaian SMART (contoh: Harga, Kualitas) beserta tipe Benefit/Cost.
- **`suppliers`** — Master data supplier ban bandara lengkap dengan informasi PIC dan alamat.
- **`evaluation_sessions`** — Sesi evaluasi SPK yang mencakup periode waktu dan status (draft → berlangsung → selesai).
- **`session_criteria`** — Relasi many-to-many antara sesi dan kriteria, menyimpan bobot kustom per sesi.
- **`session_suppliers`** — Relasi many-to-many antara sesi dan supplier yang dipilih untuk dievaluasi.
- **`session_scores`** — Tabel legacy untuk menyimpan skor mentah (opsional).
- **`session_evaluations`** — Menyimpan nilai evaluasi mentah (raw value) setiap supplier pada setiap kriteria dalam satu sesi.

Semua tabel menggunakan **UUID** sebagai primary key untuk mendukung skalabilitas dan keamanan distribusi data.

---

## Struktur Proyek

```
/
├── app/
│   ├── Controllers/          # Controller aplikasi (Auth, Criteria, Dashboard, Profile, Session, Supplier, User)
│   ├── Middleware/            # Middleware autentikasi
│   └── Models/              # Model aplikasi (User)
├── core/                     # Mesin framework (Autoload, Router, DB, Request, Response, Session, UUID, dll.)
├── database/
│   ├── migrations/           # File migrasi database
│   └── seeders/              # Seeder database (User, Criteria, Supplier)
├── public/
│   ├── assets/              # Asset statis (CSS, JS, gambar)
│   ├── uploads/             # File upload pengguna
│   └── index.php            # Entry point aplikasi & routing
├── views/                    # Template tampilan PHP
│   ├── auth/                # Halaman login & profil
│   ├── criteria/            # CRUD kriteria
│   ├── dashboard/           # Halaman dashboard
│   ├── layouts/             # Layout master, footer, sidebar
│   ├── sessions/            # Halaman sesi: daftar, buat, input, hasil
│   ├── suppliers/           # CRUD supplier
│   └── users/               # Manajemen pengguna
├── .env                      # Konfigurasi environment (gitignored)
├── .env.example              # Template environment
├── .gitignore                # Aturan git ignore
├── codebase.md               # Blueprint teknis
├── make_migration.php        # Generator migrasi
├── migrate.php               # Runner migrasi
├── reset_database.php        # Script reset database
└── README.md                   # Dokumentasi proyek
```

---

## Lisensi

Proyek ini untuk keperluan internal/akademik.
