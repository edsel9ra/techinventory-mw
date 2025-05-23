<?php
// Define la ruta raíz del proyecto
define('ROOT_PATH', __DIR__);

// Carga la configuración de la base de datos
require_once ROOT_PATH . '/config/conexion.php';

// Función para autoload de modelos y controladores
spl_autoload_register(function ($class) {
    // Busca primero en models/
    $modelPath = ROOT_PATH . '/models/' . $class . '.php';
    if (file_exists($modelPath)) {
        require_once($modelPath);
        return;
    }

    // Luego en controllers/
    $controllerPath = ROOT_PATH . '/controllers/' . $class . '.php';
    if (file_exists($controllerPath)) {
        require_once($controllerPath);
        return;
    }

    // Puedes agregar más carpetas si lo necesitas
});
