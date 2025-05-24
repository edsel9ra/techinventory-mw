<!-- detalle_equipo.php -->
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php require_once "../../public/main/head.php"; ?>
    <title>Hoja de Vida del Equipo</title>
</head>

<body>
    <?php require_once "../../public/main/nav.php"; ?>
    <section class="container mt-4">
        <div id="contenido-detalle" class="p-4 bg-white shadow rounded">
            <h3>Consultando información del equipo...</h3>
        </div>
    </section>

    <section class="container mt-4">
        <div class="accordion p-4 bg-white shadow rounded" id="historialMmtos">
            <!-- Aquí se cargarán los mantenimientos dinámicamente -->
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="modalInformacionEquipo" tabindex="-1" aria-labelledby="modalInformacionEquipoLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalInformacionEquipoLabel">Información Completa del Equipo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" id="contenidoModalEquipo">
                    <!-- Aquí se cargará la info desde mostrarInformacionCompleta() -->
                </div>
            </div>
        </div>
    </div>


    <?php require_once "../../public/main/js.php"; ?>
    <script type="text/javascript" src="hojavida.js"></script>
    <script>
        const rol_id = <?php echo $_SESSION['rol_id']; ?>;
    </script>
</body>

</html>