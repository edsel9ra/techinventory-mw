<?php
class Mantenimiento extends Conectar
{
    public function insertMmto($data)
    {
        $conectar = parent::conexion();

        try {
            $conectar->beginTransaction();
            $stmt = $conectar->prepare("INSERT INTO tbl_mantenimientos (equipo_id, tipo, fecha_realizado, tecnico, descripcion, acciones_realizadas, observaciones, revisado_por) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['equipo_id'],
                $data['tipo_mantenimiento'],
                $data['fecha_realizado'],
                $data['tecnico'],
                $data['descripcion'],
                $data['acciones_realizadas'],
                $data['observaciones'],
                $data['revisado_por']
            ]);
            $conectar->commit();
            return [
                'status' => true,
                'message' => '✅ Mantenimiento registrado correctamente'
            ];
        } catch (Exception $e) {
            $conectar->rollBack();
            throw new Exception("❌ Error al registrar el mantenimiento: " . $e->getMessage());
        }
    }

    public function listarMmtosEquipo($equipo_id)
    {
        $conectar = parent::conexion();
        $stmt = $conectar->prepare("SELECT * FROM tbl_mantenimientos WHERE equipo_id = ? ORDER BY fecha_realizado DESC");
        $stmt->execute([$equipo_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMmtoId($mmto_id)
    {
        $conectar = parent::conexion();
        $stmt = $conectar->prepare("SELECT * FROM tbl_mantenimientos WHERE mmto_id = ?");
        $stmt->execute([$mmto_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listarMmtoCompleto()
    {
        $conectar = parent::conexion();
        $stmt = $conectar->prepare("SELECT m.*, e.* FROM tbl_mantenimientos m INNER JOIN tbl_equipos e ON m.equipo_id = e.equipo_id ORDER BY m.fecha_realizado DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function totalMantenimientos()
    {
        try {
            $conectar = parent::conexion();
            $stmt = $conectar->prepare("SELECT COUNT(*) AS total_mantenimientos FROM tbl_mantenimientos");
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            throw new Exception("Error al obtener el total de mantenimientos: " . $e->getMessage());
        }
    }

    public function totalMantenimientosPreventivos()
    {
        try {
            $conectar = parent::conexion();
            $stmt = $conectar->prepare("SELECT COUNT(*) AS total_mantenimientos_preventivos FROM tbl_mantenimientos WHERE tipo = 'Preventivo'");
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            throw new Exception("Error al obtener el total de mantenimientos preventivos: " . $e->getMessage());
        }
    }

    public function totalMantenimientosCorrectivos()
    {
        try {
            $conectar = parent::conexion();
            $stmt = $conectar->prepare("SELECT COUNT(*) AS total_mantenimientos_correctivos FROM tbl_mantenimientos WHERE tipo = 'Correctivo'");
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            throw new Exception("Error al obtener el total de mantenimientos correctivos: " . $e->getMessage());
        }
    }

    /*public function totalMantenimientosPorTecnico()
    {
        try{
            $conectar = parent::conexion();
            $stmt = $conectar->prepare("SELECT u.nombre_usr, COUNT(*) AS total_mantenimientos_tecnico FROM tbl_mantenimientos m JOIN tbl_usuarios u ON m.tecnico_id = u.user_id GROUP BY u.nombre_usr");
            $stmt->execute();
            return $stmt->fetchAll();
        }catch(Exception $e){
            throw new Exception("Error al obtener el total de mantenimientos por tecnico: " . $e->getMessage());
        }
    }*/

    public function mantenimientosPorMes($anio)
    {
        try {
            $conectar = parent::conexion();
            $stmt = $conectar->prepare("SELECT MONTH(fecha_realizado) AS mes, COUNT(*) AS total_mantenimientos FROM tbl_mantenimientos WHERE YEAR(fecha_realizado) = ? GROUP BY mes");
            $stmt->execute([$anio]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al obtener los mantenimientos por mes: " . $e->getMessage());
        }
    }
}