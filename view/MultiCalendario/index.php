<!DOCTYPE html>
<html lang="es">
<head>
    <?php require_once __DIR__ . '/../../public/main/head.php'; ?>
    <title>MultiCalendario</title>
</head>
<body>
    <?php require_once __DIR__ . '/../../public/main/nav.php'; ?>
    <div class="container">
        <div class="p-4 bg-white shadow rounded">
            <h1>Calendario de eventos anuales</h1>
            <div id='multicalendar'></div>
        </div>
    </div>
    <?php require_once __DIR__ . '/../../public/main/js.php'; ?>
    <script src="multicalendario.js"></script>
</body>
</html>
