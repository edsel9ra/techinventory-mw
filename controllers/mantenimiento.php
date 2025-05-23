<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Mantenimiento.php';
$mantenimiento = new Mantenimiento();

switch ($_GET["op"]) {
    case 'insert':
        verificarRol([1, 2]);
        try {
            $data = [
                'equipo_id' => $_POST['equipo_id'],
                'tipo_mantenimiento' => $_POST['tipo_mantenimiento'],
                'fecha_realizado' => $_POST['fecha_realizado'],
                'tecnico' => $_POST['tecnico'],
                'descripcion' => $_POST['descripcion'],
                'acciones_realizadas' => $_POST['acciones_realizadas'],
                'observaciones' => $_POST['observaciones'],
                'revisado_por' => $_POST['revisado_por']
            ];

            foreach ($data as $key => $value) {
                if (empty($value)) {
                    echo json_encode([
                        'status' => false,
                        'message' => "❌ El campo $key es obligatorio."
                    ]);
                    exit;
                }
            }

            if (!DateTime::createFromFormat('Y-m-d', $data['fecha_realizado'])) {
                echo json_encode([
                    'status' => false,
                    'message' => "❌ El campo 'fecha_realizado' debe tener un formato válido (YYYY-MM-DD)."
                ]);
                exit;
            }

            $tiposPermitidos = ['Correctivo', 'Preventivo'];
            if (!in_array($data['tipo_mantenimiento'], $tiposPermitidos)) {
                echo json_encode([
                    'status' => false,
                    'message' => "❌ El campo 'tipo_mantenimiento' debe ser 'Correctivo' o 'Preventivo'."
                ]);
                exit;
            }

            $camposTexto = ['descripcion', 'acciones_realizadas', 'observaciones'];
            foreach ($camposTexto as $campo) {
                if (strlen($data[$campo]) > 255) {
                    echo json_encode([
                        'status' => false,
                        'message' => "❌ El campo '$campo' no debe exceder los 255 caracteres."
                    ]);
                    exit;
                }
            }

            $resultado = $mantenimiento->insertMmto($data);
            
            if ($resultado) {
                echo json_encode([
                    'status' => true,
                    'message' => '✅ Mantenimiento registrado correctamente.'
                ]);
            } else {
                echo json_encode([
                    'status' => false,
                    'message' => '❌ Error al registrar el mantenimiento.'
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => '❌ Error al registrar el mantenimiento: ' . $e->getMessage()
            ]);
        }
        break;

    case 'listar_mmto_equipo':
        verificarRol([1, 2]);
        $equipo_id = $_GET['equipo_id'];
        $resultado = $mantenimiento->listarMmtosEquipo($equipo_id);
        echo json_encode($resultado);
        break;

    case 'get_mmto_id':
        verificarRol([1, 2]);
        $mmto_id = $_GET['mmto_id'];
        $resultado = $mantenimiento->getMmtoId($mmto_id);
        echo json_encode($resultado);
        break;

    case 'listar_mmto_completo':
        verificarRol([1, 2]);
        $resultado = $mantenimiento->listarMmtoCompleto();
        echo json_encode($resultado);
        break;

    case 'contar_mantenimientos_total':
        verificarRol([1, 2]);
        $resultado = $mantenimiento->totalMantenimientos();
        echo json_encode([
            'status' => true,
            'data' => $resultado
        ]);
        break;

    case 'contar_mantenimientos_preventivos':
        verificarRol([1, 2]);
        $resultado = $mantenimiento->totalMantenimientosPreventivos();
        echo json_encode([
            'status' => true,
            'data' => $resultado
        ]);
        break;

    case 'contar_mantenimientos_correctivos':
        verificarRol([1, 2]);
        $resultado = $mantenimiento->totalMantenimientosCorrectivos();
        echo json_encode([
            'status' => true,
            'data' => $resultado
        ]);
        break;

    case 'mantenimientos_por_mes':
        verificarRol([1, 2]);
        $anio = isset($_GET['anio']) ? intval($_GET['anio']) : date('Y');
        $resultado = $mantenimiento->mantenimientosPorMes($anio);
        echo json_encode([
            'status' => true,
            'data' => $resultado
        ]);
        break;
}