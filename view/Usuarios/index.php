<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rol_id'] != 1) {
    header('Location: ../../logout.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php require_once "../../public/main/head.php"; ?>
    <title>Usuarios</title>
</head>

<body>
    <?php require_once "../../public/main/nav.php"; ?>
    <section class="container mt-4">
        <div class="p-4 bg-white shadow rounded">
            <h1>Usuarios</h1>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            Crear Usuario
                        </div>
                        <div class="card-body">
                            <form id="formCrearUsuario">
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control" id="nombre_usr" name="nombre_usr" required>
                                </div>
                                <div class="mb-3">
                                    <label for="correo" class="form-label">Correo</label>
                                    <input type="email" class="form-control" id="correo_usr" name="correo_usr" required>
                                </div>
                                <div class="mb-3">
                                    <label for="passwd" class="form-label">Contraseña</label>
                                    <input type="password" class="form-control" id="passwd_usr" name="passwd_usr"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="rol" class="form-label">Rol</label>
                                    <select class="form-select" id="rol_id" name="rol_id" required>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success w-100 btn-md d-flex align-items-center gap-2 justify-content-center flex-nowrap"><i class="fa-regular fa-floppy-disk"></i>Guardar</button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Listado de usuarios -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            Lista de Usuarios Registrados
                        </div>
                        <div class="table-responsive card-body">
                            <table id="tablaUsuarios" class="table table-hover table-borderless align-middle mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Correo</th>
                                        <th>Rol</th>
                                        <th>Acciones</th> <!-- ✅ Solo una columna de botones -->
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content" id="formEditarUsuarioContent">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditarUsuarioLabel">Editar Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formEditarUsuario">
                            <input type="hidden" id="edit_user_id" name="user_id">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="edit_nombre_usr" name="nombre_usr" required>
                            </div>
                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo</label>
                                <input type="email" class="form-control" id="edit_correo_usr" name="correo_usr"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="passwd" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="edit_passwd_usr" name="passwd_usr"
                                    placeholder="Ingrese una nueva contraseña (opcional)">
                            </div>
                            <div class="mb-3">
                                <label for="rol" class="form-label">Rol</label>
                                <select class="form-select" id="edit_rol_id" name="rol_id" required>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-md d-flex align-items-center gap-2 justify-content-center flex-nowrap"
                                    data-bs-dismiss="modal"><i class="fa-solid fa-arrow-left"></i>Cancelar</button>
                                <button type="submit" class="btn btn-success btn-md d-flex align-items-center gap-2 justify-content-center flex-nowrap"><i class="fa-regular fa-pen-to-square"></i>Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php require_once "../../public/main/js.php"; ?>
    <script type="text/javascript" src="usuarios.js"></script>
</body>

</html>