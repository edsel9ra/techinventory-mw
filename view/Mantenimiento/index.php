<!DOCTYPE html>
<html lang="es">

<head>
    <?php require_once "../../public/main/head.php"; ?>
    <title>Formato Mantenimiento de Equipos de Cómputo</title>
</head>

<body>
    <?php require_once "../../public/main/nav.php"; ?>
    <section class="container mt-4">
        <div class="card">
            <div class="card-body">
                <?php require_once "../../view/resources/form_header.php"; ?>
                <form id="formMantenimiento">
                    <input type="hidden" id="equipo_id" name="equipo_id"
                        value="<?= htmlspecialchars($_GET['equipo_id'] ?? '') ?>">
                    <!-- Información General -->
                    <div class="row g-3 align-items-center">
                        <div class="col">
                            <div class="input-group mb-3">
                                <label class="input-group-text fw-bold" for="fecha_realizado">FECHA:</label>
                                <input type="date" class="form-control" id="fecha_realizado" name="fecha_realizado" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group mb-3">
                                <label class="input-group-text fw-bold" for="nombre_equipo">EQUIPO A INTERVENIR:</label>
                                <input type="text" class="form-control bg-light" id="nombre_equipo" name="nombre_equipo" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 align-items-center">
                        <div class="col">
                            <div class="input-group mb-3">
                                <label class="input-group-text fw-bold" for="sede">SEDE:</label>
                                <input type="text" class="form-control bg-light" id="sede" name="sede" readonly>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group mb-3">
                                <label class="input-group-text fw-bold" for="cod_equipo">CÓDIGO:</label>
                                <input type="text" class="form-control bg-light" id="cod_equipo" name="cod_equipo" readonly>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!-- Tipo de Mantenimiento -->
                    <div class="row g-3 align-items-center">
                        <div class="col text-center">
                            <h5>TIPO DE MANTENIMIENTO</h5>
                        </div>
                        <div class="col d-grid gap-1 col-4 mx-auto">
                            <input type="radio" class="btn-check" name="tipo_mantenimiento" id="preventivo"
                                value="Preventivo" autocomplete="off" required>
                            <label class="btn btn-outline-secondary btn-md d-flex align-items-center gap-2 justify-content-center flex-nowrap" for="preventivo">
                                <span><i class="fa-solid fa-calendar-check"></i></span>
                                <span>PREVENTIVO</span>
                            </label>
                        </div>
                        <div class="col d-grid gap-1 col-4 mx-auto">
                            <input type="radio" class="btn-check" name="tipo_mantenimiento" id="correctivo"
                                value="Correctivo" autocomplete="off" required>
                            <label class="btn btn-outline-secondary btn-md d-flex align-items-center gap-2 justify-content-center flex-nowrap" for="correctivo">
                                <span><i class="fa-solid fa-wrench"></i></span>
                                <span>CORRECTIVO</span>
                            </label>
                        </div>
                    </div>
                    <hr>
                    <!-- Descripción de la Actividad -->
                    <div class="row g-3 align-items-center">
                        <div class="col text-center mb-3">
                            <div class="mb-3">
                                <label for="descripcion" class="form-label fw-bold">DESCRIPCIÓN DE LA ACTIVIDAD
                                    REALIZADA</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="7"
                                    required></textarea>
                            </div>
                        </div>
                        <div class="col text-center mb-3">
                            <div class="mb-3">
                                <label for="acciones_realizadas" class="form-label fw-bold">MATERIALES EMPLEADOS</label>
                                <textarea class="form-control" id="acciones_realizadas" name="acciones_realizadas"
                                    rows="7" required></textarea>
                            </div>
                        </div>
                        <div class="col text-center mb-3">
                            <div class="mb-3">
                                <label for="observaciones" class="form-label fw-bold">OBSERVACIONES</label>
                                <textarea class="form-control" id="observaciones" name="observaciones"
                                    rows="7"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col text-center">
                            <p>Se deja constancia de que el mantenimiento fue realizado de acuerdo a los estandares
                                establecidos
                                por el area de TI y que las observaciones y recomendaciones han sido comunicadas y
                                recibidas.</p>
                        </div>
                    </div>
                    <!-- Involucrados // Puede ser una grilla -->
                    <div class="row">
                        <div class="col text-center">
                            <input type="text" class="form-control" id="tecnico" name="tecnico" required>
                            <label for="tecnico" class="form-label fw-bold">RESPONSABLE DEL MANTENIMIENTO</label>
                        </div>
                        <div class="col text-center">
                            <input type="text" class="form-control" id="revisado_por" name="revisado_por" required>
                            <label for="revisado_por" class="form-label fw-bold">QUIEN RECIBE EL EQUIPO</label>
                        </div>
                    </div>
                    <hr>
                    <!-- Botones de Acción -->
                    <div class="text-center d-flex align-items-center gap-1 justify-content-center flex-nowrap">
                        <button type="submit" class="btn btn-success d-flex align-items-center gap-2 justify-content-center flex-nowrap">
                            <span><i class="fa-regular fa-pen-to-square"></i></span>
                            <span>Registrar Mantenimiento</span>
                        </button>
                        <button id="btnRegresar" class="btn btn-secondary d-flex align-items-center gap-2 justify-content-center flex-nowrap">
                            <span><i class="fa-solid fa-arrow-left"></i></span>
                            <span>Regresar al Listado</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <?php require_once "../../public/main/js.php"; ?>
    <script type="text/javascript" src="mantenimiento.js"></script>
</body>

</html>