<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol_id'] != 1 && $_SESSION['rol_id'] != 3) {
    switch ($_SESSION['rol_id']) {
        case 2:
            header('Location: ../soporte.php');
            break;
    }
    exit();
}
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
    <?php require_once __DIR__ . '/../../public/main/head.php'; ?>
    <title>Editar Equipo</title>
    <style>
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <?php require_once __DIR__ . '/../../public/main/nav.php'; ?>
    <section class="main-content mt-3 mb-3">
        <div class="p-4 bg-white shadow rounded">
            <form id="formEditarEquipo" method="POST">
                <div class="row">
                    <div class="col">
                        <h2 class="mb-3">Editar Equipo
                            <?php echo htmlspecialchars($detalle_equipo['cod_equipo'] ?? ''); ?>
                        </h2>
                    </div>
                    <div class="col-auto d-flex align-items-center gap-1 justify-content-center flex-nowrap">
                        <button id="btnRegresar"
                            class="btn btn-secondary btn-md fw-bold d-flex align-items-center gap-2 justify-content-center flex-nowrap"><i
                                class="fa-solid fa-arrow-left"></i>Regresar</button>
                        <button type="submit"
                            class="btn btn-success btn-md fw-bold d-flex align-items-center gap-2 justify-content-center flex-nowrap"><i
                                class="fa-regular fa-pen-to-square"></i>Actualizar Equipo</button>
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
    </section>

    <!-- Modal Baja -->
    <div class="modal fade" id="modalBajaEquipo" tabindex="-1" aria-labelledby="modalBajaEquipoLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="formBajaEquipo">
                    <div class="modal-header">
                        <h5 class="modal-title">Información de Baja del Equipo</h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Proceso:</label>
                            <input type="text" class="form-control" name="proceso_baja" required />
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Motivo de baja:</label>
                            <select class="form-control" name="motivo_baja" id="motivo_baja" required>
                                <option value="" disabled selected>Seleccione un motivo</option>
                                <option value="Mal estado">Mal estado</option>
                                <option value="Mal uso">Mal uso</option>
                                <option value="Daño eléctrico / electrónico">Daño eléctrico / electrónico</option>
                                <option value="Obsoleto">Obsoleto</option>
                                <option value="Siniestro">Siniestro</option>
                                <option value="Perdida o hurto">Perdida o hurto</option>
                                <option value="Deterioro">Deterioro</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div class="mb-3" id="otroMotivoContainer" style="display:none;">
                            <label class="form-label fw-bold">Otro motivo:</label>
                            <input type="text" class="form-control" name="otro_motivo_baja" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Concepto técnico:</label>
                            <textarea class="form-control" name="concepto_tecnico_baja" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Confirmar Baja</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php require_once __DIR__ . '/../../public/main/js.php'; ?>
    <script src="editarequipo.js"></script>
    <script src="modalbaja.js"></script>
</body>

</html>