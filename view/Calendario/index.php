<!DOCTYPE html>
<html lang="es">

<head>
    <?php require_once __DIR__ . '/../../public/main/head.php'; ?>
    <title>Calendario</title>
</head>

<body>
    <?php require_once __DIR__ . '/../../public/main/nav.php'; ?>
    <div class="container mt-4">
        <div class="p-4 bg-white shadow rounded">
            <div class="row">
                <div class="col">
                    <h2 class="mb-3">Calendario</h2>
                </div>
                <div class="col-auto d-flex align-items-center gap-1">
                    <button id="btnRegresar"
                        class="btn btn-secondary ms-2 me-2 fw-bold d-flex align-items-center gap-2 justify-content-center flex-nowrap">
                        <span><i class="fa-solid fa-arrow-left"></i></span>
                        <span>Regresar</span>
                    </button>
                </div>
            </div>
            <div class="row g-3">
                <!-- Columna para eventos externos -->
                <div class="col-12 col-md-3">
                    <div class="p-3 border rounded bg-light shadow-sm h-20">
                        <p class="fw-bold mb-3 text-center">Eventos Predefinidos</p>
                        <div id='external-events' class="wrap">
                            <div class="fc-event p-2 rounded bg-primary text-white border shadow-sm" data-title="Visita Programada" data-color="#f39c12">Visita Programada</div>
                            <div class="fc-event p-2 rounded bg-success text-white border shadow-sm" data-title="Mantenimiento Preventivo" data-color="#28a745">Mantenimiento Preventivo</div>
                        </div>
                        <!--<div class="form-check mt-3">
                            <input type='checkbox' id='drop-remove' class="form-check-input" />
                            <label for='drop-remove' class="form-check-label">Eliminar después de soltar</label>
                        </div>-->
                        <!-- Boton para agregar eventos -->
                        <div class="d-flex justify-content-center mt-3">
                            <button id="btnNuevoEvento" class="btn btn-success fw-bold d-flex align-items-center gap-2">
                                <i class="fa-solid fa-plus"></i>
                                <span>Nuevo Evento</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Columna para el calendario -->
                <div class="col-12 col-md-9">
                    <div id='calendar-container' class="p-3 border rounded bg-white shadow-sm">
                        <div id='calendar'></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="modalEvento" tabindex="-1" aria-labelledby="modalEventoLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEventoLabel">Crear Evento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <form id="formEvento">
                        <input type="hidden" id="id" name="id">
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                            <input type="datetime-local" class="form-control" id="fecha_inicio" name="fecha_inicio"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_fin" class="form-label">Fecha Fin</label>
                            <input type="datetime-local" class="form-control" id="fecha_fin" name="fecha_fin" required>
                        </div>
                        <div class="mb-3">
                            <label for="color" class="form-label">Color</label>
                            <input type="color" class="form-control form-control-color" id="color" name="color"
                                value="#3788d8">
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="all_day" name="all_day">
                            <label class="form-check-label" for="all_day">Todo el día</label>
                        </div>
                        <div class="mb-3">
                            <label for="sede_id" class="form-label">Sede</label>
                            <select class="form-select" id="sede_id" name="sede_id">
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarEvento">Guardar</button>
                    <button type="button" class="btn btn-danger" id="btnEliminarEvento" style="display: none;">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <?php require_once __DIR__ . '/../../public/main/js.php'; ?>
    <script src="calendario.js"></script>
</body>

</html>