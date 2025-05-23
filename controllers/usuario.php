<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ .'/../config/auth.php';
require_once __DIR__ . '/../models/Usuario.php';

$usuario = new Usuario();

switch ($_GET["op"]) {
    case 'login':
        try {
            // Obtener los datos enviados desde el frontend
            $correo_usr = filter_input(INPUT_POST, 'correo_usr', FILTER_VALIDATE_EMAIL);
            $passwd_usr = $_POST['passwd_usr'] ?? null;

            // Validar que los campos no estén vacíos
            if (empty($correo_usr) || empty($passwd_usr)) {
                echo json_encode([
                    'status' => false,
                    'message' => '❌ El correo y la contraseña son obligatorios.'
                ]);
                exit;
            }

            // Llamar al método login del modelo Usuario
            $resultado = $usuario->login($correo_usr, $passwd_usr);

            // Verificar si el login fue exitoso
            if ($resultado) {
                echo json_encode([
                    'status' => true,
                    'message' => '✅ Inicio de sesión exitoso',
                    'redirect' => $resultado['redirect'] // URL de redirección según el rol
                ]);
            } else {
                echo json_encode([
                    'status' => false,
                    'message' => '❌ Credenciales incorrectas'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => '⚠️ Error interno del servidor: ' . $e->getMessage()
            ]);
        }
        break;

    case 'crear_usuario':
        verificarRol([1]);
        try {
            $nombre_usr = $_POST['nombre_usr'] ?? null;
            $correo_usr = $_POST['correo_usr'] ?? null;
            $passwd_usr = $_POST['passwd_usr'] ?? null;
            $rol_id = $_POST['rol_id'] ?? null;
            $usuario->crearUser(
                $nombre_usr,
                $correo_usr,
                $passwd_usr,
                $rol_id
            );
            echo json_encode([
                'status' => true,
                'message' => '✅ Usuario creado exitosamente'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => '⚠️ Error interno del servidor: ' . $e->getMessage()
            ]);
        }
        break;

    case 'listar_usuarios':
        verificarRol([1]);
        try {
            $usuarios = $usuario->listarUsuarios();
            $data = [];

            foreach ($usuarios as $row) {
                $acciones = '
                <button type="button" class="btn btn-primary btn-sm btn-editar"
                    data-user-id="' . (int) $row["user_id"] . '"
                    data-nombre="' . htmlspecialchars($row["nombre_usr"], ENT_QUOTES) . '"
                    data-correo="' . htmlspecialchars($row["correo_usr"], ENT_QUOTES) . '"
                    data-passwd="' . htmlspecialchars($row["passwd_usr"], ENT_QUOTES) . '"
                    data-rol-id="' . (int) $row["rol_id"] . '">
                    <i class="bi bi-pencil-fill"></i>
                </button>
                <button type="button" class="btn btn-danger btn-sm btn-eliminar"
                    data-user-id="' . (int) $row["user_id"] . '">
                    <i class="bi bi-trash-fill"></i>
                </button>';

                $sub_array = [];
                $sub_array[] = $row["nombre_usr"];
                $sub_array[] = $row["correo_usr"];
                $sub_array[] = $row["nombre_rol"];
                $sub_array[] = $acciones;

                $data[] = $sub_array;
            }

            $results = [
                "sEcho" => 1,
                "iTotalRecords" => count($data),
                "iTotalDisplayRecords" => count($data),
                "aaData" => $data
            ];

            echo json_encode($results);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => '⚠️ Error interno del servidor: ' . $e->getMessage()
            ]);
        }
        break;

    case 'combo_roles':
        verificarRol([1]);
        try {
            $roles = $usuario->get_roles();
            $data = [];
            foreach ($roles as $row) {
                $data[] = [
                    'rol_id' => $row['rol_id'],
                    'nombre_rol' => $row['nombre_rol']
                ];
            }
            echo json_encode([
                'status' => true,
                'data' => $data
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => '⚠️ Error interno del servidor: ' . $e->getMessage()
            ]);
        }
        break;

    case 'eliminar_usuario':
        verificarRol([1]);
        try {
            $user_id = isset($_POST['user_id']) ? (int) $_POST['user_id'] : null;

            if (!$user_id) {
                throw new Exception("⚠️ ID de usuario no proporcionado");
            }

            $resultado = $usuario->eliminarUser($user_id);

            if ($resultado) {
                echo json_encode([
                    'status' => true,
                    'message' => '✅ Usuario eliminado exitosamente'
                ]);
            } else {
                echo json_encode([
                    'status' => false,
                    'message' => '⚠️ Error al eliminar el usuario'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => '⚠️ Error interno del servidor: ' . $e->getMessage()
            ]);
        }
        break;


    case 'editar_usuario':
        verificarRol([1]);
        try {
            $user_id = $_POST['user_id'] ?? null;
            if (!$user_id) {
                throw new Exception("⚠️ ID de usuario no proporcionado");
            }

            $camposActualizar = [];
            if (isset($_POST['nombre_usr'])) {
                $camposActualizar['nombre_usr'] = $_POST['nombre_usr'];
            }
            if (isset($_POST['correo_usr'])) {
                $camposActualizar['correo_usr'] = $_POST['correo_usr'];
            }
            if (!empty($_POST['passwd_usr'])) { // Solo actualizar si se ingresa una nueva contraseña
                $camposActualizar['passwd_usr'] = password_hash($_POST['passwd_usr'], PASSWORD_DEFAULT);
            }
            if (isset($_POST['rol_id'])) {
                $camposActualizar['rol_id'] = $_POST['rol_id'];
            }

            if (empty($camposActualizar)) {
                throw new Exception("⚠️ No hay campos para actualizar");
            }

            $resultado = $usuario->editarUser($user_id, $camposActualizar);
            if ($resultado) {
                echo json_encode([
                    'status' => true,
                    'message' => '✅ Usuario editado exitosamente'
                ]);
            } else {
                echo json_encode([
                    'status' => false,
                    'message' => '⚠️ Error al editar el usuario'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => '⚠️ Error interno del servidor: ' . $e->getMessage()
            ]);
        }
        break;

    default:
        echo json_encode([
            'status' => false,
            'message' => '⚠️ Operación no válida.'
        ]);
        break;
}