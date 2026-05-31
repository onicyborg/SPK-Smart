</div>
                        </div>
                    </div>

                    <div class="app-footer py-3 d-flex flex-column flex-md-row flex-center flex-md-stack"
                        id="kt_app_footer">
                        <div class="text-gray-700 order-2 order-md-1 w-100 text-center">
                            <span class="text-muted fw-semibold me-1">&copy; <?= date('Y') ?></span>
                            <span class="text-gray-600 fw-semibold">SPK-Smart</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= $_ENV['ASSET_URL'] ?>/assets/plugins/global/plugins.bundle.js"></script>
    <script src="<?= $_ENV['ASSET_URL'] ?>/assets/js/scripts.bundle.js"></script>

    <script src="<?= $_ENV['ASSET_URL'] ?>/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
    <script src="<?= $_ENV['ASSET_URL'] ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>

    <script>
        (function () {
            function applyIcon(mode) {
                var icon = document.getElementById('theme_toggle_icon');
                if (!icon) return;
                icon.className = 'bi ' + (mode === 'dark' ? 'bi-moon' : 'bi-sun');
            }

            function getMode() {
                return document.documentElement.getAttribute('data-bs-theme') || 'light';
            }

            function setMode(mode) {
                document.documentElement.setAttribute('data-bs-theme', mode);
                try { localStorage.setItem('data-bs-theme', mode); } catch (e) {}
                applyIcon(mode);
            }

            document.addEventListener('DOMContentLoaded', function () {
                applyIcon(getMode());
                var btn = document.getElementById('theme_toggle');
                if (btn) {
                    btn.addEventListener('click', function () {
                        var next = getMode() === 'dark' ? 'light' : 'dark';
                        setMode(next);
                    });
                }
            });
        })();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.btn').forEach(function (el) {
                if (!el.classList.contains('btn-sm')) {
                    el.classList.add('btn-sm');
                }
                if (el.classList.contains('btn-lg')) {
                    el.classList.remove('btn-lg');
                }
            });
        });
    </script>

    <?php $flashSuccess = \Core\Session::getFlash('success'); if ($flashSuccess): ?>
    <script>
        (function(){
            var msg = <?= json_encode($flashSuccess) ?>;
            if (window.toastr && toastr.success) { toastr.success(msg); }
            else { console.log('SUCCESS:', msg); }
        })();
    </script>
    <?php endif; ?>

    <?php $flashError = \Core\Session::getFlash('error'); if ($flashError): ?>
    <script>
        (function(){
            var msg = <?= json_encode($flashError) ?>;
            if (window.toastr && toastr.error) { toastr.error(msg); }
            else { console.error('ERROR:', msg); }
        })();
    </script>
    <?php endif; ?>

    <?php $flashErrors = \Core\Session::getFlash('errors'); if (!empty($flashErrors) && is_array($flashErrors)): ?>
    <script>
        (function(){
            var errs = <?= json_encode($flashErrors) ?>;
            var msg = errs.join('\n');
            if (window.toastr && toastr.error) { toastr.error(msg); }
            else { console.error('ERRORS:', msg); }
        })();
    </script>
    <?php endif; ?>

    <?= $scripts ?? '' ?>
</body>

</html>
