<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol_id'] != 1) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Head -->
    <?php require_once "../public/main/head.php"; ?>
    <title>Administrador - Dashboard</title>
</head>

<body>
    <?php require_once "../public/main/nav.php"; ?>
    <section class="container mt-4">
        <div class="p-4 bg-white shadow rounded">
            <!--<h1>Bienvenido Administrador: <?php echo htmlspecialchars($_SESSION['nombre_usr']); ?></h1>
            <p>Este es tu panel de usuario.</p>
            <a href="../logout.php" class="btn btn-danger">Cerrar sesión</a>-->

            <div class="row mb-4 mt-4">
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
                        <a href="#" class="small-box-footer">Más información<i class="fa fa-arrow-circle-right"></i></a>
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
                        <a href="#" class="small-box-footer">Más información<i class="fa fa-arrow-circle-right"></i></a>
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
                        <a href="#" class="small-box-footer">Más información<i class="fa fa-arrow-circle-right"></i></a>
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
                        <a href="#" class="small-box-footer">Más información<i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="row mt-4 mb-4">
                <h2 class="text-center">Equipos por tipo</h2>
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
            <div class="row mt-4 mb-4">
                <h2 class="text-center">Equipos activos por sede</h2>
                <div class="col-12 d-flex flex-column align-items-center">
                    <div style="width: 100%; max-width: 500px;">
                        <canvas id="graficoEquiposSedeActivos" height="90"></canvas>
                    </div>
                </div>
            </div>
            <div class="row mt-4 mb-4">
                <h2 class="text-center">Resumen de Mantenimientos</h2>
                <div class="col-lg-4 col-6">
                    <div class="small-box text-bg-secondary">
                        <div class="inner">
                            <h3 id="totalMntos"></h3>
                            <p>Mantenimientos realizados</p>
                        </div>
                        <div class="small-box-icon">
                            <ion-icon name="construct"></ion-icon>
                        </div>
                        <a href="#" class="small-box-footer">Más información<i class="fa fa-arrow-circle-right"></i></a>
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
                        <a href="#" class="small-box-footer">Más información<i class="fa fa-arrow-circle-right"></i></a>
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
                        <a href="#" class="small-box-footer">Más información<i class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
            <div class="row mt-4 mb-4">
                <h2 class="text-center">Mantenimientos por mes</h2>

                <div class="col-12 d-flex flex-column align-items-center">
                    <!-- Filtro de año -->
                    <div class="input-group input-group-sm mb-3" style="max-width: 200px;">
                        <label class="input-group-text fw-bold" for="filtroAnio">Año</label>
                        <select id="filtroAnio" class="form-select form-select-sm">
                            <!-- Llenado dinámico -->
                        </select>
                    </div>

                    <!-- Gráfico más grande -->
                    <div style="width: 100%; max-width: 900px;">
                        <canvas id="graficoMntosPorMes" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- JS Principal -->
    <?php require_once "../public/main/js.php"; ?>
    <script defer src="../public/js/estadistica.js"></script>
    <script>
        const rol_id = <?php echo $_SESSION['rol_id']; ?>;
    </script>
</body>

</html>