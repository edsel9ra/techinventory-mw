<?php
require_once __DIR__ . '/../../autoload.php';
?>
<!-- Menu -->
<script type="text/javascript" src="<?php echo BASE_URL; ?>public/js/menu.js"></script>

<!-- ROL -->
<script>
    const rol_id = <?php echo $_SESSION['rol_id']; ?>;
</script>

<!-- Bootstrap -->
<script src="<?php echo BASE_URL; ?>public/js/lib/bootstrap/bootstrap.bundle.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.19.1/dist/sweetalert2.all.min.js"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- PDFMake -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js" integrity="sha384-VFQrHzqBh5qiJIU0uGU5CIW3+OWpdGGJM9LBnGbuIH2mkICcFZ7lPd/AAtI7SNf7" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js" integrity="sha384-/RlQG9uf0M2vcTw3CX7fbqgbj/h8wKxw7C3zu9/GxcBPRKOEcESxaxufwRXqzq6n" crossorigin="anonymous"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.3.0/b-3.2.3/b-html5-3.2.3/datatables.min.js" integrity="sha384-Xft6d3E1Hl9N81Qm2TZryBquczVg75GEgA+OdPizzFq1v+hbpchBuGLwuSMVW4Xz" crossorigin="anonymous"></script>

<!-- Font Awesome -->
<script src="<?php echo BASE_URL; ?>public/js/lib/fontawesome/all.js"></script>
<script src="<?php echo BASE_URL; ?>public/js/lib/fontawesome/fontawesome.js"></script>

<!-- AdminLTE -->
<script src="<?php echo BASE_URL; ?>public/js/lib/adminlte/adminlte.js"></script>

<!-- Overlayscrollbars -->
<script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"
      integrity="sha256-dghWARbRe2eLlIJ56wNB+b760ywulqK3DzZYEpsg2fQ="
      crossorigin="anonymous"></script>

<!-- Ionicons -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- FullCalendar -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar/index.global.min.js'></script>

<!-- Tippy -->
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Magnific Popup JS -->
<script src="https://cdn.jsdelivr.net/npm/magnific-popup@1.1.0/dist/jquery.magnific-popup.min.js"></script>

<!-- Lightbox -->
<script src="https://cdn.jsdelivr.net/npm/lightbox2@2/dist/js/lightbox.min.js"></script>