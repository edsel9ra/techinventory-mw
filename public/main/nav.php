<?php
$rol = $_SESSION['rol_id'] ?? null;
?>
<nav class="main-menu">
    <!-- Aquí iria un logo -->
    <div class="scrollbar" id="style-1">
        <!-- Aquí iria el menu -->
        <ul>
            <?php
            switch ($rol) {
                case 1:
                    ?>
                    <li>
                        <a href="<?php echo BASE_URL; ?>view/admin.php">
                            <i class="bi bi-house bi-lg"></i>
                            <span class="nav-text">
                                Home
                                <span class="badge bg-primary ms-2">Admin</span>
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>view/ListarEquipo/">
                            <i class="bi bi-laptop bi-lg"></i>
                            <span class="nav-text">Equipos</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>view/Usuarios/">
                            <i class="bi bi-people bi-lg"></i>
                            <span class="nav-text">Usuarios</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>view/Calendario/index.php">
                            <i class="bi bi-calendar bi-lg"></i>
                            <span class="nav-text">Eventos</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>view/Multicalendario/index.php">
                            <i class="bi bi-calendar bi-lg"></i>
                            <span class="nav-text">Calendario Anual</span>
                        </a>
                    </li>
                    <?php break;

                case 2: ?>
                    <li>
                        <a href="<?php echo BASE_URL; ?>view/soporte.php">
                            <i class="bi bi-house bi-lg"></i>
                            <span class="nav-text">
                                Home
                                <span class="badge bg-info text-dark ms-2">Soporte</span>
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>view/ListarEquipo/">
                            <i class="bi bi-laptop bi-lg"></i>
                            <span class="nav-text">Equipos</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>view/Multicalendario/index.php">
                            <i class="bi bi-calendar bi-lg"></i>
                            <span class="nav-text">Calendario Anual</span>
                        </a>
                    </li>
                    <?php break;

                case 3: ?>
                    <li>
                        <a href="<?php echo BASE_URL; ?>view/inventario.php">
                            <i class="bi bi-house bi-lg"></i>
                            <span class="nav-text">
                                Home
                                <span class="badge bg-success ms-2">Inventario</span>
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo BASE_URL; ?>view/ListarEquipo/">
                            <i class="bi bi-laptop bi-lg"></i>
                            <span class="nav-text">Equipos</span>
                        </a>
                    </li>
                    <?php break;
            } ?>
            <li>
                <a href="<?php echo BASE_URL; ?>logout.php">
                    <i class="bi bi-box-arrow-right bi-lg"></i>
                    <span class="nav-text">Cerrar Sesión</span>
                </a>
            </li>
        </ul>
    </div>
</nav>