<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol_id'] != 2) {
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php require_once __DIR__ . '/../public/main/head.php'; ?>
    <title>Técnico - Dashboard</title>
</head>

<body>
    <?php require_once __DIR__ . '/../public/main/nav.php'; ?>
    <section class="main-content mt-3 mb-3">
        <div class="p-4 bg-white shadow rounded">
            <div class="row mt-2 mb-2" id="resumenMntos">
                <h1 class="text-center mb-4">Resumen de Mantenimientos</h1>
                <div class="col-lg-4 col-6">
                    <div class="small-box text-bg-secondary">
                        <div class="inner">
                            <h3 id="totalMntos"></h3>
                            <p>Mantenimientos realizados</p>
                        </div>
                        <div class="small-box-icon">
                            <ion-icon name="construct"></ion-icon>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box text-bg-primary">
                        <div class="inner">
                            <h3 id="totalMntosPreventivos"></h3>
                            <p>Mantenimientos preventivos</p>
                        </div>
                        <div class="small-box-icon">
                            <ion-icon name="today"></ion-icon>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-6">
                    <div class="small-box text-bg-info">
                        <div class="inner">
                            <h3 id="totalMntosCorrectivos"></h3>
                            <p>Mantenimientos correctivos</p>
                        </div>
                        <div class="small-box-icon">
                            <ion-icon name="sync"></ion-icon>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4 mb-4" id="mntosPorMes">
                <h2 class="text-center mb-4">Mantenimientos por mes</h2>

                <div class="col-12 d-flex flex-column align-items-center">
                    <div class="d-flex justify-content-center gap-2">
                        <!-- Filtro de año -->
                        <div class="input-group input-group-sm mb-3" style="width: 135px;">
                            <label class="input-group-text fw-bold" for="filtroAnio">Año</label>
                            <select id="filtroAnio" class="form-select form-select-sm">
                                <!-- Llenado dinámico -->
                            </select>
                        </div>
                        <div class="input-group input-group-sm mb-3" style="width: 170px;">
                            <label class="input-group-text fw-bold" for="filtroTipoMnto">Tipo</label>
                            <select id="filtroTipoMnto" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                <option value="Preventivo">Preventivo</option>
                                <option value="Correctivo">Correctivo</option>
                            </select>
                        </div>
                    </div>
                    <!-- Gráfico más grande -->
                    <div style="width: 100%; max-width: 900px;">
                        <canvas id="graficoMntosPorMes" height="150"></canvas>
                        <div id="totalMantenimientos" class="text-center mt-3 fw-bold fs-5"></div>
                    </div>
                </div>
            </div>
        </div>
        <div id="alert-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1100;"></div>
    </section>

    <!-- JS Principal -->
    <?php require_once __DIR__ . '/../public/main/js.php'; ?>
    <?php require_once __DIR__ . '/../public/main/dashboard.php'; ?>
</body>

</html>