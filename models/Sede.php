<?php
class Sede extends Conectar
{
    public function get_sede()
    {
        $conectar = parent::conexion();
        try {
            $stmt = $conectar->prepare("SELECT * FROM tbl_sedes WHERE estado = 1");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Error al obtener las sedes: " . $e->getMessage());
        }
    }

    public function get_sede_id($sede_id)
    {
        $conectar = parent::conexion();
        try {
            $stmt = $conectar->prepare("SELECT * FROM tbl_sedes WHERE sede_id = ?");
            $stmt->execute([$sede_id]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Error al obtener la sede con ID $sede_id: " . $e->getMessage());
        }
    }

}