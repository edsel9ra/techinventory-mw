<?php

class Evento extends Conectar
{
    public function getEventos()
    {
        $conectar = parent::conexion();
        $stmt = $conectar->prepare("SELECT * FROM tbl_eventos_calendario WHERE activo = 1");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function insertEvento($data)
    {
        $conectar = parent::conexion();
        try {
            $conectar->beginTransaction();

            $stmt = $conectar->prepare("INSERT INTO tbl_eventos_calendario (titulo, descripcion, fecha_inicio, fecha_fin, all_day, color, sede_id, creado_por) VALUES (:titulo, :descripcion, :fecha_inicio, :fecha_fin, :all_day, :color, :sede_id, :creado_por)");
            $stmt->execute($data);
            $lastId = $conectar->lastInsertId();
            $conectar->commit();
            return $lastId;
        } catch (Exception $e) {
            $conectar->rollBack();
            throw new Exception("Error al insertar evento: " . $e->getMessage());
        }
    }

    public function updateEvento($evento_id, $data)
    {
        $conectar = parent::conexion();
        try {
            $conectar->beginTransaction();

            $stmt = $conectar->prepare("UPDATE tbl_eventos_calendario SET titulo = :titulo, descripcion = :descripcion, fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin, all_day = :all_day, color = :color, sede_id = :sede_id WHERE evento_id = :evento_id");

            $stmt->execute([
                'titulo' => $data['titulo'] ?? '',
                'descripcion' => $data['descripcion'] ?? '',
                'fecha_inicio' => $data['fecha_inicio'] ?? null,
                'fecha_fin' => $data['fecha_fin'] ?? null,
                'all_day' => isset($data['all_day']) ? intval($data['all_day']) : 0,
                'color' => $data['color'] ?? '#3788d8',
                'sede_id' => $data['sede_id'] ?? null,
                'evento_id' => intval($evento_id),
            ]);

            $conectar->commit();
            return true;
        } catch (Exception $e) {
            $conectar->rollBack();
            throw new Exception("Error al actualizar evento: " . $e->getMessage());
        }
    }


    public function deleteEvento($evento_id)
    {
        $conectar = parent::conexion();
        try {
            $conectar->beginTransaction();

            $stmt = $conectar->prepare("UPDATE tbl_eventos_calendario SET activo = 0 WHERE evento_id = :evento_id");
            $stmt->execute([
                'evento_id' => intval($evento_id),
            ]);
            $conectar->commit();
            return true;
        } catch (Exception $e) {
            $conectar->rollBack();
            throw new Exception("Error al eliminar evento: " . $e->getMessage());
        }
    }

    public function getEventosProximosEnCurso(){
        try{
            $conectar = parent::conexion();
            $stmt = $conectar->prepare("SELECT ev.titulo, ev.fecha_inicio, ev.fecha_fin, s.nombre_sede as sede FROM tbl_eventos_calendario ev JOIN tbl_sedes s ON ev.sede_id = s.sede_id WHERE ev.activo = 1 AND (ev.fecha_inicio BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 3 DAY) OR NOW() BETWEEN ev.fecha_inicio AND ev.fecha_fin)");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }catch(Exception $e){
            throw new Exception("Error al obtener eventos proximos en curso: " . $e->getMessage());
        }
    }

    public function existeEvento($evento_id){
        try{
            $conectar = parent::conexion();
            $stmt = $conectar->prepare("SELECT COUNT(*) FROM tbl_eventos_calendario WHERE evento_id = :evento_id");
            $stmt->execute([
                'evento_id' => intval($evento_id),
            ]);
            $result = $stmt->fetchColumn() > 0;
            return $result;
        }catch(Exception $e){
            throw new Exception("Error al verificar evento: " . $e->getMessage());
        }
    }
}
