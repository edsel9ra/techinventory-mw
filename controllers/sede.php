<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Sede.php';
$sede = new Sede();

switch ($_GET["op"]) {
    case 'combo':
        verificarRol([1, 3]);
        try {
            $sedes = $sede->get_sede();
            $data = [];

            foreach ($sedes as $row) {
                $data[] =[
                    'sede_id' => $row['sede_id'],
                    'nombre_sede' => $row['nombre_sede']
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
}