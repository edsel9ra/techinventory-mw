<?php
const BASE_URL = "/techinventory/";
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
date_default_timezone_set('America/Bogota');

class Conectar
{
    protected $dbh;
    protected function Conexion()
    {
        try {
            $conectar = $this->dbh = new PDO("mysql:host=localhost;dbname=wwmist_invit", "root", "");
            $conectar->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conectar->exec("SET NAMES 'utf8'");
            $conectar->exec("SET time_zone = '-05:00'");
            return $conectar;
        } catch (Exception $e) {
            die("¡Error BD!: " . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->Conexion();
    }

}