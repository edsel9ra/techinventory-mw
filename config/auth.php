<?php
function verificarRol(array $roles_permitidos): void {

    if(!isset($_SESSION['rol_id']) || !in_array($_SESSION['rol_id'], $roles_permitidos)) {
        http_response_code(403);
        echo json_encode([
            'status' => 'error',
            'message' => 'Acceso denegado'
        ]);
        exit;
    }
}