<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Evento.php';
$evento = new Evento();
function esFechaValida($fecha)
{
    $formatos = ['Y-m-d\TH:i', 'Y-m-d H:i:s', 'Y-m-d'];

    foreach ($formatos as $formato) {
        $dt = DateTime::createFromFormat($formato, $fecha);
        if ($dt && $dt->format($formato) === $fecha) {
            return true;
        }
    }
    return false;
}

switch ($_GET["op"]) {
    //Listar eventos
    case 'listar':
        verificarRol([1,2]);
        $datos = $evento->getEventos();
        $data = [];

        foreach ($datos as $row) {
            $isAllDay = filter_var($row['all_day'], FILTER_VALIDATE_BOOLEAN);

            // Si es evento de todo el día, usamos solo la fecha (YYYY-MM-DD)
            $start = $isAllDay ? substr($row['fecha_inicio'], 0, 10) : (new DateTime($row['fecha_inicio']))->format('Y-m-d H:i:s');
            $end = $isAllDay ? substr($row['fecha_fin'], 0, 10) : (new DateTime($row['fecha_fin']))->format('Y-m-d H:i:s');

            $data[] = [
                'id' => $row['id'],
                'title' => $row['titulo'],
                'start' => $start,
                'end' => $end,
                'allDay' => $isAllDay,
                'color' => $row['color'],
                'extendedProps' => [
                    'descripcion' => $row['descripcion'] ?? '',
                    'sede_id' => $row['sede_id'] ?? null,
                ]
            ];
        }

        echo json_encode($data);
        break;

    //Insertar evento
    case 'insert':
        verificarRol([1]);
        try {
            $datos = [
                'titulo' => $_POST['titulo'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'fecha_inicio' => $_POST['fecha_inicio'] ?? '',
                'fecha_fin' => $_POST['fecha_fin'] ?? '',
                'all_day' => isset($_POST['all_day']) ? 1 : 0,
                'color' => $_POST['color'] ?? '',
                'sede_id' => $_POST['sede_id'] ?? null,
                'creado_por' => $_POST['creado_por'] ?? 'Sistema'
            ];

            // Validación rápida para campos requeridos
            if (empty($datos['titulo']) || empty($datos['fecha_inicio']) || empty($datos['fecha_fin'])) {
                echo json_encode([
                    "status" => false,
                    "message" => "Campos requeridos incompletos."
                ]);
                exit;
            }

            $allDay = $datos['all_day'];

            if ($allDay) {
                // Usamos solo la fecha (sin hora)
                $datos['fecha_inicio'] = date('Y-m-d', strtotime($datos['fecha_inicio']));
                $datos['fecha_fin'] = date('Y-m-d', strtotime($datos['fecha_fin']));
            } else {
                // Aseguramos formato completo
                $datos['fecha_inicio'] = date('Y-m-d H:i:s', strtotime($datos['fecha_inicio']));
                $datos['fecha_fin'] = date('Y-m-d H:i:s', strtotime($datos['fecha_fin']));
            }

            if (!esFechaValida($datos['fecha_inicio']) || !esFechaValida($datos['fecha_fin'])) {

                echo json_encode([
                    "status" => false,
                    "message" => "Formato de fecha inválido." . $datos['fecha_inicio'] . " " . $datos['fecha_fin']
                ]);
                exit;
            }

            if (strtotime($datos['fecha_fin']) <= strtotime($datos['fecha_inicio'])) {
                echo json_encode([
                    "status" => false,
                    "message" => "Fecha de fin debe ser mayor a fecha de inicio."
                ]);
                exit;
            }

            $idInsertado = $evento->insertEvento($datos);

            echo json_encode([
                "status" => true,
                "id" => $idInsertado,
                "titulo" => $_POST['titulo'],
                "fecha_inicio" => $_POST['fecha_inicio'],
                "fecha_fin" => $_POST['fecha_fin'],
                "all_day" => $allDay,
                "color" => $_POST['color'],
                "extendedProps" => [
                    "descripcion" => $_POST['descripcion'],
                    "sede_id" => $_POST['sede_id'] ?? null,
                ],
                "message" => "Evento insertado correctamente"
            ]);
        } catch (Exception $e) {
            http_response_code(500); // Opcional pero recomendable
            echo json_encode([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
        break;

    //Actualizar evento
    case 'update':
        verificarRol([1]);
        try {
            if (!isset($_POST['id']) || empty($_POST['id'])) {
                throw new Exception("❌ ID inválido.");
            }

            if (!isset($_POST["sede_id"]) || empty($_POST["sede_id"])) {
                throw new Exception("❌ Sede inválida.");
            }

            $datos = [];
            $id = $_POST['id'];

            if (isset($_POST['titulo']))
                $datos['titulo'] = $_POST['titulo'];
            if (isset($_POST['descripcion']))
                $datos['descripcion'] = $_POST['descripcion'];
            if (isset($_POST['fecha_inicio']))
                $datos['fecha_inicio'] = $_POST['fecha_inicio'];
            if (isset($_POST['fecha_fin']))
                $datos['fecha_fin'] = $_POST['fecha_fin'];
            if (isset($_POST['all_day']))
                $datos['all_day'] = !empty($_POST['all_day']) ? 1 : 0;
            if (isset($_POST['color']))
                $datos['color'] = $_POST['color'];
            if (isset($_POST['sede_id']))
                $datos['sede_id'] = $_POST['sede_id'];

            // Ajuste de formato de fecha según all_day
            if (isset($datos['all_day']) && $datos['all_day']) {
                $datos['fecha_inicio'] = date('Y-m-d', strtotime($datos['fecha_inicio']));
                $datos['fecha_fin'] = date('Y-m-d', strtotime($datos['fecha_fin']));
            } else {
                $datos['fecha_inicio'] = date('Y-m-d H:i:s', strtotime($datos['fecha_inicio']));
                $datos['fecha_fin'] = date('Y-m-d H:i:s', strtotime($datos['fecha_fin']));
            }

            if (!empty($datos['fecha_inicio']) && !esFechaValida($datos['fecha_inicio'])) {
                throw new Exception("❌ Fecha de inicio inválida.");
            }

            if (!empty($datos['fecha_fin']) && !esFechaValida($datos['fecha_fin'])) {
                throw new Exception("❌ Fecha de fin inválida.");
            }

            if (!empty($datos['fecha_fin']) && !empty($datos['fecha_inicio']) && strtotime($datos['fecha_fin']) <= strtotime($datos['fecha_inicio'])) {
                throw new Exception("❌ Fecha de fin debe ser mayor a fecha de inicio.");
            }

            $evento->updateEvento($id, $datos);

            echo json_encode([
                "status" => true,
                "id" => $id,
                "titulo" => $datos['titulo'],
                "fecha_inicio" => $datos['fecha_inicio'],
                "fecha_fin" => $datos['fecha_fin'],
                "all_day" => $datos['all_day'] ?? 0,
                "color" => $datos['color'],
                "descripcion" => $datos['descripcion'],
                "sede_id" => $datos['sede_id'],
                "message" => "✅ Evento actualizado correctamente"
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "status" => false,
                "message" => "❌ " . $e->getMessage()
            ]);
        }
        break;

    //Eliminar evento
    case 'delete':
        verificarRol([1]);
        try {
            if (!isset($_POST['id']) || empty($_POST['id'])) {
                throw new Exception("❌ ID inválido.");
            }

            $id = $_POST['id'];
            $evento->deleteEvento($id);

            echo json_encode([
                "status" => true,
                "id" => $id,
                "message" => "✅ Evento eliminado correctamente"
            ]);
        } catch (Exception $e) {
            http_response_code(500); // Opcional pero recomendable
            echo json_encode([
                "status" => false,
                "message" => "❌ " . $e->getMessage()
            ]);
        }
        break;
}