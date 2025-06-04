<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol_id'] != 1 && $_SESSION['rol_id'] != 2) {
    switch ($_SESSION['rol_id']) {
        case 3:
            header('Location: ../inventario.php');
            break;
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once __DIR__ . '/../../public/main/head.php'; ?>
    <title>MultiCalendario</title>
</head>
<body>
    <?php require_once __DIR__ . '/../../public/main/nav.php'; ?>
    <section class="main-content mt-3 mb-3">
        <div class="p-4 bg-white shadow rounded">
            <h1 class="col d-flex align-items-center justify-content-center">Calendario de eventos anuales</h1>
            <div id='multicalendar'></div>
        </div>
    </section>
    <?php require_once __DIR__ . '/../../public/main/js.php'; ?>
    <script src="multicalendario.js"></script>
</body>
</html>
