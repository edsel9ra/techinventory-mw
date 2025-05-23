<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol_id'] != 4) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuario - Dashboard</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1>Bienvenido Observador: <?php echo htmlspecialchars($_SESSION['nombre_usr']); ?></h1>
        <p>Este es tu panel de usuario.</p>
        <a href="logout.php" class="btn btn-danger">Cerrar sesión</a>
    </div>
</body>
</html>
