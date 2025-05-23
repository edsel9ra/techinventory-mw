<?php
require_once 'config/conexion.php';
require_once 'models/Equipo.php';

try {
    $equipo = new Equipo();

    $resultado = $equipo->insertEquipo(1, 2, 'Marca', 'Modelo', 'Serial', 'AF', 'Activo', 'Responsable', ['tamanio_pulgadas' => 24]);

    if ($resultado) {
        echo "✅ Equipo creado correctamente.";
    }

} catch (Exception $e) {
    echo "❌ Error al crear equipo: " . $e->getMessage();
}
