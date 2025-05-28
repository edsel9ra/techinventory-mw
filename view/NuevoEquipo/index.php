<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol_id'] != 1 && $_SESSION['rol_id'] != 3) {
    switch ($_SESSION['rol_id']) {
        case 2:
            header('Location: ../soporte.php');
            break;
    }
    exit();
}?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php require_once __DIR__ . '/../../public/main/head.php'; ?>
    <title>Registrar Equipo</title>
</head>

<body>
    <?php require_once __DIR__ . '/../../public/main/nav.php'; ?>
    <!-- Campos fijos -->
    <section class="main-content mt-3 mb-3">
        <div class="p-4 bg-white shadow rounded">
            <form id="form_equipo">
                <div class="row">
                    <div class="col d-flex align-items-center justify-content-center">
                        <h2 class="mb-3">Registro de nuevo equipo en inventario</h2>
                    </div>
                    <div class="col-auto d-flex align-items-center gap-1">
                        <button type="submit" id="btnGuardar" class="btn btn-primary ms-2 mb-3 fw-bold d-flex align-items-center gap-2 justify-content-center flex-nowrap">
                            <span><i class="fa-solid fa-floppy-disk"></i></span>
                            <span>Guardar</span>
                        </button>
                        <button id="btnRegresar" class="btn btn-secondary ms-2 mb-3 fw-bold d-flex align-items-center gap-2 justify-content-center flex-nowrap">
                            <span><i class="fa-solid fa-arrow-left"></i></span>
                            <span>Regresar</span>
                        </button>
                    </div>
                </div>
                <div class="row g-3 align-items-center">
                    <div class="col">
                        <div class="input-group mb-3">
                            <label class="input-group-text fw-bold" for="sede">Sede</label>
                            <select class="form-select" name="sede" id="sede" required></select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group mb-3">
                            <label class="input-group-text fw-bold" for="tipo_equipo">Tipo de equipo</label>
                            <select class="form-select" name="tipo_equipo" id="tipo_equipo" required></select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group mb-3">
                            <label class="input-group-text fw-bold" for="af">Código Equipo</label>
                            <input type="text" class="form-control" name="af" id="af" readonly>
                        </div>
                    </div>
                </div>
                <div class="row g-3 align-items-center">
                    <div class="col">
                        <div class="input-group mb-3">
                            <label class="input-group-text fw-bold" for="marca">Marca</label>
                            <input type="text" class="form-control" name="marca" id="marca" required>
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group mb-3">
                            <label class="input-group-text fw-bold" for="modelo">Modelo</label>
                            <input type="text" class="form-control" name="modelo" id="modelo" required>
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group mb-3">
                            <label class="input-group-text fw-bold" for="serial">Serial</label>
                            <input type="text" class="form-control" name="serial" id="serial" required>
                        </div>
                    </div>
                </div>
                <div class="row g-3 align-items-center">
                    <div class="col">
                        <div class="input-group mb-3">
                            <label class="input-group-text fw-bold" for="estado">Estado</label>
                            <select class="form-select" name="estado" id="estado" required>
                                <option value="" disabled selected>Seleccione</option>
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                                <option value="Baja">Dado de Baja</option>
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group mb-3">
                            <label class="input-group-text fw-bold" for="responsable">Responsable</label>
                            <input type="text" class="form-control" name="responsable" id="responsable" required>
                        </div>
                    </div>
                </div>
                <div class="main-content" id="campos_detalles"></div>
            </form>
        </div>
    </section>

    <!-- JS Principal -->
    <?php require_once __DIR__ . '/../../public/main/js.php'; ?>
    <script type="text/javascript" src="nuevoequipo.js"></script>

</body>

</html>