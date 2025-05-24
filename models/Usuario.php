<?php
class Usuario extends Conectar
{
    public function login($correo_usr, $passwd_usr)
    {
        $conectar = parent::conexion();

        try {
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }

            $stmt = $conectar->prepare("SELECT * FROM tbl_usuarios WHERE correo_usr = ? AND activo = 1");
            $stmt->execute([$correo_usr]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            //var_dump($usuario); exit;

            if ($usuario && isset($usuario['passwd_usr']) && password_verify($passwd_usr, $usuario['passwd_usr'])) {
                $_SESSION['user_id'] = $usuario['user_id'];
                $_SESSION['nombre_usr'] = $usuario['nombre_usr'];
                $_SESSION['rol_id'] = $usuario['rol_id'];

                return [
                    'redirect' => $this->obtenerUrlPorRol($usuario['rol_id'])
                ];
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new Exception("Error en el método login: " . $e->getMessage());
        }
    }

    private function obtenerUrlPorRol($rol_id)
    {
        return match ($rol_id) {
            1 => '../view/admin.php',
            2 => '../view/soporte.php',
            3 => '../view/inventario.php',
            4 => '../view/observador.php',
            default => '../view/error.php',
        };
    }

    public function get_roles()
    {
        $conectar = parent::conexion();
        try {
            $stmt = $conectar->prepare("SELECT * FROM tbl_roles");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al listar roles: " . $e->getMessage());
        }
    }

    public function crearUser($nombre_usr, $cargo_usr, $correo_usr, $passwd_usr, $rol_id)
    {
        $conectar = parent::conexion();
        try {
            if (empty($nombre_usr) || empty($cargo_usr) || empty($correo_usr) || empty($passwd_usr) || empty($rol_id)) {
                throw new Exception("Todos los campos son obligatorios.");
            }

            if (!filter_var($correo_usr, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("El correo electrónico no tiene un formato válido.");
            }

            if (strlen($passwd_usr) < 8) {
                throw new Exception("La contraseña debe tener al menos 8 caracteres.");
            }
            //
            $conectar->beginTransaction();
            $hash = password_hash($passwd_usr, PASSWORD_DEFAULT);
            $stmt = $conectar->prepare("INSERT INTO tbl_usuarios (nombre_usr, cargo_usr, correo_usr, passwd_usr, rol_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nombre_usr, $cargo_usr, $correo_usr, $hash, $rol_id]);
            $conectar->commit();
            return true;
        } catch (PDOException $e) {
            $conectar->rollBack();
            if ($e->errorInfo[1] == 1062) {
                throw new Exception("El correo ya está registrado.");
            }
            throw new Exception("Error al guardar el usuario: " . $e->getMessage());
        }
    }

    public function editarUser($user_id, $campos)
    {
        $conectar = parent::conexion();
        try {
            $conectar->beginTransaction();
            $sql = "UPDATE tbl_usuarios SET ";
            $valores = [];
            $parametros = [];
            foreach ($campos as $campo => $valor) {
                $valores[] = "$campo = ?";
                $parametros[] = $valor;
            }
            $sql .= implode(", ", $valores);
            $sql .= " WHERE user_id = ?";
            $parametros[] = $user_id;
            $stmt = $conectar->prepare($sql);
            $stmt->execute($parametros);
            $conectar->commit();
            return true;
        } catch (Exception $e) {
            $conectar->rollBack();
            throw new Exception("Error al editar el usuario: " . $e->getMessage());
        }
    }

    public function eliminarUser(int $user_id): bool
    {
        $conectar = parent::conexion();
        try {
            $conectar->beginTransaction();
            $stmt = $conectar->prepare("UPDATE tbl_usuarios SET activo = 0 WHERE user_id = ?");
            $stmt->execute([$user_id]);

            if ($stmt->rowCount() > 0) {
                $conectar->commit();
                return true;
            } else {
                $conectar->rollBack();
                throw new Exception("No se encontró el usuario con ID: $user_id");
            }
        } catch (Exception $e) {
            $conectar->rollBack();
            throw new Exception("Error al eliminar el usuario: " . $e->getMessage());
        }
    }


    public function listarUsuarios()
    {
        $conectar = parent::conexion();
        try {
            $stmt = $conectar->prepare("SELECT u.*, r.nombre_rol FROM tbl_usuarios u JOIN tbl_roles r ON u.rol_id = r.rol_id WHERE u.activo = 1");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al listar usuarios: " . $e->getMessage());
        }
    }

    public function get_user_id($user_id)
    {
        $conectar = parent::conexion();
        try {
            $stmt = $conectar->prepare("SELECT * FROM tbl_usuarios WHERE user_id = ?");
            $stmt->execute([$user_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener usuario: " . $e->getMessage());
        }
    }
}