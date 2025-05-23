<?php
require_once '../../config/conexion.php';
require_once '../../models/Equipo.php';
$equipo = new Equipo();

$equipo_id = $_GET['equipo_id'] ?? null;
if ($equipo_id) {
    $detalle_equipo = $equipo->listarEquipoConDetalle($equipo_id);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php require_once "../../public/main/head.php"; ?>
    <title>Editar Equipo</title>
    <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    <?php require_once "../../public/main/nav.php"; ?>
    <div class="container mt-4">
        <div class="p-4 bg-white shadow rounded">
            <form id="formEditarEquipo" method="POST">
                <div class="row">
                    <div class="col">
                        <h2 class="mb-3">Editar Equipo
                            <?php echo htmlspecialchars($detalle_equipo['cod_equipo'] ?? ''); ?></h2>
                    </div>
                    <div class="col-auto d-flex align-items-center gap-1 justify-content-center flex-nowrap">
                        <button id="btnRegresar" class="btn btn-secondary btn-md fw-bold d-flex align-items-center gap-2 justify-content-center flex-nowrap"><i class="fa-solid fa-arrow-left"></i>Regresar</button>
                        <button type="submit" class="btn btn-success btn-md fw-bold d-flex align-items-center gap-2 justify-content-center flex-nowrap"><i class="fa-regular fa-pen-to-square"></i>Actualizar Equipo</button>
                    </div>
                </div>

                <input type="hidden" id="equipo_id" name="equipo_id"
                    value="<?php echo htmlspecialchars($equipo_id); ?>">
                <input type="hidden" id="tipo_equipo_id" name="tipo_equipo_id"
                    value="<?php echo htmlspecialchars($detalle_equipo['tipo_equipo_id'] ?? ''); ?>">
                <input type="hidden" id="monitor_id_original" name="detalles[monitor_id_original]">
                <div class="row">
                    <div class="col">
                        <div class="card card-body">
                            <div class="mb-3">
                                <label for="sede" class="form-label fw-bold">Sede</label>
                                <select id="sede" name="sede" class="form-control" required>
                                    <!-- Opciones de sede cargadas dinámicamente -->
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="estado" class="form-label fw-bold">Estado</label>
                                <select id="estado" name="estado" class="form-control" required>
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                    <option value="Baja">Dado de Baja</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="responsable" class="form-label fw-bold">Responsable</label>
                                <input type="text" id="responsable" name="responsable" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card card-body">
                            <div id="detallesEquipo">
                                <!-- Aquí se cargarán los campos específicos según el tipo de equipo -->
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php require_once "../../public/main/js.php"; ?>
    <script src="editarequipo.js"></script>
</body>

</html>