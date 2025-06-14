<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol_id'] != 3) {
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Head -->
    <?php require_once __DIR__ . '/../public/main/head.php'; ?>
    <title>Gestión de Equipos - Dashboard</title>
</head>

<body>
    <?php require_once __DIR__ . '/../public/main/nav.php'; ?>
    <section class="main-content mt-3 mb-3">
        <div class="p-4 bg-white shadow rounded">
        <div class="row mb-4 mt-4" id="resumen">
                <h2 class="text-center">Resumen</h2>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-secondary">
                        <div class="inner">
                            <h3 id="totalEquipos"></h3>
                            <p>Total de Equipos</p>
                        </div>
                        <div class="small-box-icon">
                            <ion-icon name="laptop"></ion-icon>
                        </div>
                        <a href="ListarEquipo/" class="small-box-footer">Ver Equipos<i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-success">
                        <div class="inner">
                            <h3 id="totalActivos"></h3>
                            <p>Total Activos</p>
                        </div>
                        <div class="small-box-icon">
                            <ion-icon name="cloud-done"></ion-icon>
                        </div>
                        <a href="ListarEquipo/?estado=activo" class="small-box-footer">Ver Activos<i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-warning">
                        <div class="inner">
                            <h3 id="totalInactivos"></h3>
                            <p>Total Inactivos</p>
                        </div>
                        <div class="small-box-icon">
                            <ion-icon name="warning"></ion-icon>
                        </div>
                        <a href="ListarEquipo/?estado=inactivo" class="small-box-footer">Ver Inactivos<i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-danger">
                        <div class="inner">
                            <h3 id="totalBaja"></h3>
                            <p>Total Dados de Baja</p>
                        </div>
                        <div class="small-box-icon">
                            <ion-icon name="trash-bin"></ion-icon>
                        </div>
                        <a href="ListarEquipo/?estado=baja" class="small-box-footer">Ver Equipos de Baja<i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="row mt-4 mb-4" id="equiposPorTipo">
                <h2 class="text-center">Equipos por estado/tipo</h2>
                <div class="col-lg-4 col-6 d-flex justify-content-center">
                    <canvas id="graficoEquiposTipoActivos" width="400" height="250"></canvas>
                </div>
                <div class="col-lg-4 col-6 d-flex justify-content-center">
                    <canvas id="graficoEquiposTipoInactivos" width="400" height="250"></canvas>
                </div>
                <div class="col-lg-4 col-6 d-flex justify-content-center">
                    <canvas id="graficoEquiposTipoBaja" width="400" height="250"></canvas>
                </div>
            </div>
            <div class="row mt-4 mb-4" id="equiposPorSede">
                <h2 class="text-center">Equipos activos por sede</h2>
                <div class="col-12 d-flex flex-column align-items-center">
                    <div style="width: 100%; max-width: 500px;">
                        <canvas id="graficoEquiposSedeActivos" height="90"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- JS Principal -->
    <?php require_once __DIR__ . '/../public/main/js.php'; ?>
    <script defer src="../public/js/estadistica.js"></script>
</body>

</html>