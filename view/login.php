<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Head -->
    <?php require_once "../public/main/head.php"; ?>
    <title>Iniciar sesión</title>

    <!-- JS Login personalizado -->
    <script defer src="../public/js/login.js"></script>
</head>

<body class="login-page bg-body-secondary">
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="text-center">Inventario Tecnológico</h3>
                <h3 class="text-center">Mister Wings</h3>
            </div>
            <div class="card-body login-card-body">
                <p class="login-box-msg">Inicia sesión para acceder al sistema</p>
                <form id="form_login">
                    <div class="input-group mb-3">
                        <div class="form-group form-floating">
                            <input type="email" class="form-control" name="correo_usr" id="correo_usr" placeholder="Correo"
                            required>
                            <label for="correo_usr">Correo</label>
                        </div>
                        <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                    </div>
                    <div class="input-group mb-3">
                        <div class="form-group form-floating">
                            <input type="password" class="form-control" name="passwd_usr" id="passwd_usr"
                            placeholder="Contraseña" required>
                            <label for="passwd_usr">Contraseña</label>
                        </div>
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                    <div class="form-group form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">
                        Recordar mi correo
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
                </form>
            </div>
        </div>
    </div>

    <!-- JS Principal -->
    <?php require_once "../public/main/js.php"; ?>
</body>

</html>