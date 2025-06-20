<?php
// Controlador para gestionar equipos
header('Content-Type: application/json');
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../models/Equipo.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../public/lib/fpdf/fpdf.php';
$equipo = new Equipo();
$usuario = new Usuario();

function formatearEquiposDataTable($equipos)
{
    $data = [];
    foreach ($equipos as $row) {
        $sub_array = [];
        $sub_array[] = $row['nombre_sede'];
        $sub_array[] = $row['cod_equipo'];
        $sub_array[] = $row['activo_anterior'];
        $sub_array[] = $row['nombre_equipo'];
        $sub_array[] = $row['serial_equipo'];
        $sub_array[] = $row['estado']; // Estado texto plano
        $sub_array[] = $row['equipo_id']; // Para usarlo en los botones
        $data[] = $sub_array;
    }
    $results = [
        "sEcho" => 1,
        "iTotalRecords" => count($data),
        "iTotalDisplayRecords" => count($data),
        "aaData" => $data
    ];
    return $results;
}

switch ($_GET["op"]) {

    //Combo tipo equipo
    case "combo_tipo_equipo":
        verificarRol([1, 3]);
        try {
            $tipos_equipos = $equipo->get_tipo_equipo();
            $data = [];

            foreach ($tipos_equipos as $row) {
                $data[] = [
                    'tipo_equipo_id' => $row['tipo_equipo_id'],
                    'nombre_equipo' => $row['nombre_equipo']
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

    //Insertar equipo
    case 'insert':
        verificarRol([1, 3]);
        try {
            if (!isset($_POST['detalles'])) {
                echo json_encode([
                    'status' => false,
                    'message' => 'Faltan los detalles del equipo'
                ]);
                exit;
            }

            $detalles = json_decode($_POST['detalles'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo json_encode([
                    'status' => false,
                    'message' => 'Detalles inválidos: ' . json_last_error_msg()
                ]);
                exit;
            }

            // Conversión segura a entero
            $tipo_equipo = isset($_POST['tipo_equipo']) ? intval($_POST['tipo_equipo']) : null;

            // Generar código del equipo
            $af = $equipo->generarCodigoEquipo($_POST['sede']);

            if (
                isset(
                $_POST['sede'],
                $tipo_equipo,
                $_POST['marca'],
                $_POST['modelo'],
                $_POST['serial'],
                $_POST['estado'],
                $_POST['responsable']
            ) && !empty($af) && is_array($detalles)
            ) {
                $resultado = $equipo->insertEquipo(
                    $_POST['sede'],
                    $tipo_equipo,
                    $_POST['marca'],
                    $_POST['modelo'],
                    $_POST['serial'],
                    $af,
                    $_POST['estado'],
                    $_POST['responsable'],
                    $detalles
                );

                echo json_encode([
                    'status' => true,
                    'message' => '✅ Equipo registrado correctamente',
                    'codigo_equipo' => $af
                ]);
            } else {
                echo json_encode([
                    'status' => false,
                    'message' => '⚠️ Faltan campos obligatorios o valores inválidos'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => '❌ Error al registrar el equipo: ' . $e->getMessage()
            ]);
        }
        break;

    //Combo monitor
    case 'combo_monitor':
        verificarRol([1, 3]);
        try {
            $sede_id = $_GET['sede_id'] ?? null;
            $monitores = $equipo->get_monitor($sede_id);
            $data = [];

            foreach ($monitores as $row) {
                $data[] = [
                    'monitor_id' => $row['monitor_id'],
                    'cod_equipo' => $row['cod_equipo']
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

    //Combo Monitor Edit
    case 'combo_monitor_edit':
        verificarRol([1, 3]);
        try {
            $sede_id = $_GET['sede_id'] ?? null;
            $monitores = $equipo->get_monitor($sede_id);
            echo json_encode($monitores);
        } catch (Exception $e) {
            echo json_encode(['status' => false, 'message' => $e->getMessage()]);
        }
        break;

    //Listar Equipos
    case 'listar':
        verificarRol([1, 2, 3]);
        $datos = $equipo->listarEquipos();
        echo json_encode(formatearEquiposDataTable($datos));
        break;

    //Listar Detalle de Equipo
    case 'listar_detalle':
        verificarRol([1, 2, 3]);
        try {
            $equipo_id = $_GET['equipo_id'] ?? null;
            if (empty($equipo_id)) {
                throw new Exception("❌ El ID del equipo no puede estar vacío.");
            }
            $detalle_equipo = $equipo->listarEquipoConDetalle($equipo_id);
            echo json_encode([
                'status' => true,
                'data' => $detalle_equipo
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    //Obtener Equipo para Editar
    case 'get_equipo_edit':
        verificarRol([1, 3]);
        try {
            $equipo_id = $_GET['equipo_id'] ?? null;
            if (empty($equipo_id)) {
                throw new Exception("❌ El ID del equipo no puede estar vacío.");
            }
            $equipo_data = $equipo->get_equipo_id($equipo_id);
            $detalles = $equipo->get_detalle_tipo($equipo_data['tipo_equipo_id'], $equipo_data['detalle_equipo_id']);
            if (empty($equipo_data)) {
                throw new Exception("❌ No se encontró información del equipo.");
            }
            echo json_encode([
                'status' => true,
                'data' => $equipo_data,
                'detalles' => $detalles
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    //Actualizar Equipo
    case 'update':
        verificarRol([1, 3]);
        try {
            $equipo_id = $_POST['equipo_id'] ?? null;
            $detalles = $_POST['detalles'] ?? null;

            if (empty($equipo_id)) {
                throw new Exception("❌ El ID del equipo no puede estar vacío.");
            }
            if (!is_array($detalles)) {
                throw new Exception("❌ Los detalles deben ser un array.");
            }

            if (isset($detalles['monitor_id']) && $detalles['monitor_id'] !== '') {
                $monitor_id = $detalles['monitor_id'];
            } elseif (isset($detalles['monitor_id_original']) && $detalles['monitor_id_original'] !== '') {
                $monitor_id = $detalles['monitor_id_original'];
            } else {
                $monitor_id = null;
            }

            // Asegúrate de incluir el monitor en los detalles
            $detalles['monitor_id'] = $monitor_id;

            $datos = [
                'sede' => $_POST['sede'] ?? null,
                'tipo_equipo_id' => $_POST['tipo_equipo_id'] ?? null,
                'estado' => $_POST['estado'] ?? null,
                'responsable' => $_POST['responsable'] ?? null,
                'detalles' => $detalles
            ];

            // Si el estado es Baja, agregar los campos de baja
            if ($datos['estado'] === 'Baja') {
                $datos['proceso_baja'] = $_POST['proceso_baja'] ?? null;
                $datos['motivo_baja'] = $_POST['motivo_baja'] ?? null;
                $datos['otro_motivo_baja'] = $_POST['otro_motivo_baja'] ?? null;
                $datos['concepto_tecnico_baja'] = $_POST['concepto_tecnico_baja'] ?? null;
            }

            $resultado = $equipo->editarEquipo($equipo_id, $datos);

            if ($resultado) {
                echo json_encode([
                    'status' => true,
                    'message' => '✅ Equipo actualizado correctamente',
                    'data' => $resultado,
                    'equipo_id' => $equipo_id // Incluir el equipo_id para la redirección
                ]);
            } else {
                echo json_encode([
                    'status' => false,
                    'message' => '❌ No se pudo actualizar el equipo. Verifica los datos enviados.'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => '❌ Error al actualizar el equipo: ' . $e->getMessage()
            ]);
        }
        break;

    //Generar Código de Equipo
    case 'generar_codigo_equipo':
        verificarRol([1, 3]);
        try {
            $sede_id = $_GET['sede_id'] ?? null;
            if (empty($sede_id)) {
                throw new Exception("❌ El ID de la sede no puede estar vacío.");
            }
            $codigo_equipo = $equipo->generarCodigoEquipo($sede_id);
            echo json_encode([
                'status' => true,
                'data' => $codigo_equipo
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    //Acta de Entrega
    case 'acta_entrega_pdf':
        verificarRol([1, 3]);
        try {
            $equipo_id = $_GET['equipo_id'] ?? null;
            if (empty($equipo_id)) {
                throw new Exception("❌ El ID del equipo no es válido.");
            }
            $equipo_data = $equipo->get_equipo_id($equipo_id);

            if (empty($equipo_data)) {
                throw new Exception("❌ No se encontró información del equipo.");
            }

            $usuario_data = $usuario->get_user_id($_SESSION['user_id']);

            $formatter = new IntlDateFormatter(
                'es_CO', // Localización
                IntlDateFormatter::LONG,
                IntlDateFormatter::NONE,
                'America/Bogota', // Zona horaria
                IntlDateFormatter::GREGORIAN,
                "d 'de' MMMM 'de' y"
            );
            $fecha = $formatter->format(new DateTime());
            class PDF extends FPDF
            {
                public function Footer()
                {
                    $this->SetY(-35);
                    $this->SetFont("Arial", "I", 10);
                    $this->Cell(0, 5, "Oficina Administrativa", 0, 1, 'C');
                    $this->Cell(0, 5, "Carrera 44 # 10-45", 0, 1, 'C');
                    $this->Cell(0, 5, "Barrio Departamental", 0, 1, 'C');
                    $this->Cell(0, 5, "Tel: 338 28 85 - 318 334 9813", 0, 1, 'C');
                }
            }

            $pdf = new PDF();
            $pdf->AliasNbPages();
            $pdf->AddPage();

            //Logo
            $pdf->SetFont('Arial', '', 12);
            $pdf->Image(__DIR__ . '/../public/img/logo_sin_circulo.png', 75, 3, 60);
            $pdf->Ln(45);

            //Fecha
            $pdf->Cell(0, 10, "Santiago de Cali, $fecha", 0, 2);

            //Titulo
            $pdf->SetFont("Arial", "BI", 14);
            $pdf->Cell(0, 10, "ACTA DE ENTREGA", 0, 1, 'C');
            $pdf->Ln(2);

            //Texto
            $pdf->SetFont("Arial", "", 12);
            $pdf->MultiCell(0, 6, mb_convert_encoding("Por medio de la presente se hace entrega del siguiente objeto a ", "ISO-8859-1", "UTF-8") . $equipo_data['nombre_sede'] . mb_convert_encoding(", con la siguiente información:", "ISO-8859-1", "UTF-8"));
            $pdf->Ln(5);

            //Tabla
            $anchoTabla = 140;
            $posX = (210 - $anchoTabla) / 2;

            $pdf->SetFont('Arial', 'BI', 12);
            $pdf->SetX($posX);
            $pdf->Cell(40, 10, mb_convert_encoding('CANTIDAD', "ISO-8859-1", "UTF-8"), 1, 0, 'C');
            $pdf->Cell(100, 10, mb_convert_encoding('DESCRIPCIÓN', "ISO-8859-1", "UTF-8"), 1, 1, 'C');

            $pdf->SetFont('Arial', '', 12);
            $descripcion = "Se entrega " . $equipo_data['nombre_equipo'] . "\n";
            $descripcion .= mb_convert_encoding("Código asignado: ", "ISO-8859-1", "UTF-8") . $equipo_data['cod_equipo'] . "\n";
            $descripcion .= "Marca: " . $equipo_data['marca_equipo'] . "\n";
            $descripcion .= "Modelo: " . $equipo_data['modelo_equipo'] . "\n";
            $descripcion .= "Serial: " . $equipo_data['serial_equipo'] . "\n";

            $pdf->SetX($posX);
            $pdf->Cell(40, 30, '1', 1, 0, 'C');
            $pdf->MultiCell(100, 6, $descripcion, 1, 'C');

            //Nota
            $pdf->Ln(5);
            $pdf->SetFont('Arial', 'BI', 12);
            $pdf->Cell(15, 10, 'Nota:', 0, 0);
            $pdf->SetFont('Arial', '', 12);
            $pdf->MultiCell(0, 10, 'Se entrega en buen estado, probado y funcional. Accesorios originales incluidos.', 0, 1);
            $pdf->Ln(10);

            //Firmas
            $pdf->Ln(20);
            //Encabezados
            $pdf->SetFont('Arial', 'BI', 12);
            $pdf->Cell(90, 10, mb_convert_encoding('Entrega', "ISO-8859-1", "UTF-8"), 0, 0, 'L');
            $pdf->Cell(90, 10, mb_convert_encoding('Recibe', "ISO-8859-1", "UTF-8"), 0, 1, 'L');
            $pdf->Ln(20);

            //Lineas
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(90, 10, '_______________________________', 0, 0, 'C');
            $pdf->Cell(90, 10, '_______________________________', 0, 1, 'C');

            //Nombres
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(90, 7, mb_convert_encoding($usuario_data['nombre_usr'], "ISO-8859-1", "UTF-8"), 0, 0, 'C');
            $pdf->Cell(90, 7, '', 0, 1, 'C');
            //Cargos
            $pdf->SetFont('Arial', 'I', 12);
            $pdf->Cell(90, 7, mb_convert_encoding($usuario_data['cargo_usr'], "ISO-8859-1", "UTF-8"), 0, 0, 'C');
            $pdf->Cell(90, 7, '', 0, 1, 'C');

            $pdf->Output('I', 'acta_entrega_' . $equipo_data['cod_equipo'] . '.pdf');

            echo json_encode([
                'status' => true,
                'message' => '✅ Acta de entrega generada correctamente',
                'data' => $equipo_data
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    //Contar equipos
    case 'contar_equipos_total':
        verificarRol([1, 3]);
        try {
            $resultado = $equipo->totalEquipos();
            echo json_encode([
                'status' => true,
                'data' => $resultado
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    case 'contar_equipos_activos':
        verificarRol([1, 3]);
        try {
            $resultado = $equipo->equiposActivos();
            echo json_encode([
                'status' => true,
                'data' => $resultado
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    case 'contar_equipos_inactivos':
        verificarRol([1, 3]);
        try {
            $resultado = $equipo->equiposInactivos();
            echo json_encode([
                'status' => true,
                'data' => $resultado
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    case 'contar_equipos_baja':
        verificarRol([1, 3]);
        try {
            $resultado = $equipo->equiposBaja();
            echo json_encode([
                'status' => true,
                'data' => $resultado
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    case 'contar_tipos_equipo_activos':
        verificarRol([1, 3]);
        try {
            $resultado = $equipo->equiposPorTipoActivos();
            echo json_encode([
                'status' => true,
                'data' => $resultado
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    case 'contar_tipos_equipo_inactivos':
        verificarRol([1, 3]);
        try {
            $resultado = $equipo->equiposPorTipoInactivos();
            echo json_encode([
                'status' => true,
                'data' => $resultado
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    case 'contar_tipos_equipo_baja':
        verificarRol([1, 3]);
        try {
            $resultado = $equipo->equiposPorTipoBaja();
            echo json_encode([
                'status' => true,
                'data' => $resultado
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    case 'contar_equipos_sedes':
        verificarRol([1, 3]);
        try {
            $resultado = $equipo->equiposPorSedeActivos();
            echo json_encode([
                'status' => true,
                'data' => $resultado
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    //Subir imágenes
    case 'subir_imagen_equipo':
        verificarRol([1, 3]);
        try {
            //Recibir archivos
            $archivos = $_FILES['imagenes'] ?? [];
            $maxSize = 5 * 1024 * 1024;
            $mimePermitidos = ['image/jpeg', 'image/png', 'image/jpg', 'image/bmp', 'image/webp'];
            $extensionesPermitidas = ['jpeg', 'jpg', 'png', 'bmp', 'webp'];
            //Contador de archivos guardados
            $guardadas = 0;

            //Recibir datos
            $equipo_id = $_POST['equipo_id'] ?? null;
            $cod_equipo = $_POST['cod_equipo'] ?? null;
            $descripcion = $_POST['descripcion'] ?? null;

            // Validar que se haya seleccionado un equipo y su código
            if (!$equipo_id || !$cod_equipo) {
                throw new Exception("❌ No se encontró el equipo con ID $equipo_id");
            }

            //Recorrer archivos
            for ($i = 0; $i < count($archivos['name']); $i++) {
                if ($archivos['error'][$i] !== UPLOAD_ERR_OK)
                    continue;

                $nombreOriginal = $archivos['name'][$i];
                $tmpName = $archivos['tmp_name'][$i];
                $size = $archivos['size'][$i];

                if ($size > $maxSize)
                    continue;

                $mime = mime_content_type($tmpName);
                $ext = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
                if (!in_array($mime, $mimePermitidos) || !in_array($ext, $extensionesPermitidas))
                    continue;

                $nombreSanitizado = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $nombreOriginal); // limpia caracteres raros

                $directorio = __DIR__ . '/../public/uploads/gallery/' . $cod_equipo;
                if (!is_dir($directorio))
                    mkdir($directorio, 0775, true);

                $rutaRelativa = "/public/uploads/gallery/$cod_equipo/$nombreSanitizado";
                $rutaDestino = "$directorio/$nombreSanitizado";

                if (move_uploaded_file($tmpName, $rutaDestino)) {
                    $equipo->insertImagenEquipo($equipo_id, $rutaRelativa, $descripcion);
                    $guardadas++;
                }
            }

            echo json_encode([
                'status' => $guardadas > 0,
                'message' => $guardadas > 0 ? "$guardadas Imágenes subidas correctamente" : '❌ No se subieron imágenes',
                'data' => $equipo_id
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    case 'listar_imagenes_equipo':
        verificarRol([1, 2, 3]);
        try {
            $equipo_id = $_GET['equipo_id'] ?? null;
            if (empty($equipo_id)) {
                throw new Exception("❌ El ID del equipo no puede estar vacío.");
            }
            $imagenes = $equipo->get_imagenes_equipo($equipo_id);
            echo json_encode([
                'status' => true,
                'data' => $imagenes
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    case 'eliminar_imagen_equipo':
        verificarRol([1, 3]);
        try {
            $imagen_id = $_GET['imagen_id'] ?? null;
            if (!$imagen_id) {
                throw new Exception("❌ ID de la imagen no proporcionado");
            }
            $equipo->deleteImagenEquipo($imagen_id);
            echo json_encode([
                'status' => true,
                'message' => '✅ Imagen eliminada correctamente'
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;

    case 'acta_baja_pdf':
        verificarRol([1, 3]);
        try {
            $equipo_id = $_GET['equipo_id'] ?? null;
            if (empty($equipo_id)) {
                throw new Exception("❌ El ID del equipo no es válido.");
            }

            $equipo_data = $equipo->get_equipo_id($equipo_id);
            if (empty($equipo_data)) {
                throw new Exception("❌ No se encontró información del equipo.");
            }

            class PDF extends FPDF
            {
                public function Header()
                {
                    $this->AddFont('Impact', '', 'impact.php');
                    $this->SetFont('Impact', '', 22);
                    $this->Image(__DIR__ . '/../public/img/logo_sin_circulo.png', 15, 2, 35);
                    $this->Cell(0, 10, mb_convert_encoding('FORMATO', "ISO-8859-1", "UTF-8"), 0, 1, 'C');
                    $this->Cell(0, 10, mb_convert_encoding('ACTA DE BAJA ACTIVOS FIJOS', "ISO-8859-1", "UTF-8"), 0, 1, 'C');
                    $this->Ln(7);
                }
            }

            $pdf = new PDF();
            $pdf->AddPage();
            $pdf->SetFont("Arial", "", 10);

            $fecha_formateada = date("d-M-Y", strtotime($equipo_data['fecha_baja']));

            $pdf->SetFont("Arial", "BI", 11);
            $pdf->Cell(45, 10, mb_convert_encoding('FECHA', "ISO-8859-1", "UTF-8"), 1, 0, 'C');
            $pdf->SetFont("Arial", "", 11);
            $pdf->Cell(50, 10, $fecha_formateada, 1, 0, 'C');
            $pdf->SetFont("Arial", "BI", 11);
            $pdf->Cell(45, 10, mb_convert_encoding('PROCESO', "ISO-8859-1", "UTF-8"), 1, 0, 'C');
            $pdf->SetFont("Arial", "", 11);
            $pdf->Cell(50, 10, mb_convert_encoding($equipo_data['proceso_baja'], "ISO-8859-1", "UTF-8"), 1, 1, 'C');
            $pdf->SetFont("Arial", "BI", 11);
            $pdf->Cell(45, 10, mb_convert_encoding('SEDE', "ISO-8859-1", "UTF-8"), 1, 0, 'C');
            $pdf->SetFont("Arial", "", 11);
            $pdf->Cell(50, 10, mb_convert_encoding($equipo_data['nombre_sede'], "ISO-8859-1", "UTF-8"), 1, 0, 'C');
            $pdf->SetFont("Arial", "BI", 11);
            $pdf->Cell(45, 10, mb_convert_encoding('RESPONSABLE', "ISO-8859-1", "UTF-8"), 1, 0, 'C');
            $pdf->SetFont("Arial", "", 11);
            $pdf->Cell(50, 10, mb_convert_encoding($equipo_data['responsable'], "ISO-8859-1", "UTF-8"), 1, 1, 'C');

            $pdf->Ln(5);
            $pdf->SetFont("Arial", "B", 11);
            $pdf->Cell(0, 10, mb_convert_encoding('DESCRIPCIÓN DE ELEMENTO / EQUIPO', "ISO-8859-1", "UTF-8"), 1, 1, 'C');
            $pdf->SetFont("Arial", "BI", 11);
            $pdf->Cell(45, 10, mb_convert_encoding('EQUIPO', "ISO-8859-1", "UTF-8"), 1, 0, 'C');
            $pdf->SetFont("Arial", "", 11);
            $pdf->Cell(50, 10, $equipo_data['nombre_equipo'], 1, 0, 'C');
            $pdf->SetFont("Arial", "BI", 11);
            $pdf->Cell(45, 10, mb_convert_encoding('MARCA', "ISO-8859-1", "UTF-8"), 1, 0, 'C');
            $pdf->SetFont("Arial", "", 11);
            $pdf->Cell(50, 10, $equipo_data['marca_equipo'], 1, 1, 'C');

            $pdf->SetFont("Arial", "BI", 11);
            $pdf->Cell(35, 10, mb_convert_encoding('CÓDIGO EQUIPO', "ISO-8859-1", "UTF-8"), 1, 0, 'C');
            $pdf->SetFont("Arial", "", 11);
            $pdf->Cell(30, 10, $equipo_data['cod_equipo'], 1, 0, 'C');
            $pdf->SetFont("Arial", "BI", 11);
            $pdf->Cell(25, 10, mb_convert_encoding('SERIAL', "ISO-8859-1", "UTF-8"), 1, 0, 'C');
            $pdf->SetFont("Arial", "", 10);
            $pdf->Cell(37.5, 10, $equipo_data['serial_equipo'], 1, 0, 'C');
            $pdf->SetFont("Arial", "BI", 11);
            $pdf->Cell(25, 10, mb_convert_encoding('MODELO', "ISO-8859-1", "UTF-8"), 1, 0, 'C');
            $pdf->SetFont("Arial", "", 11);
            $pdf->Cell(37.5, 10, $equipo_data['modelo_equipo'], 1, 1, 'C');

            $pdf->Ln(5);
            $pdf->SetFont("Arial", "B", 11);
            $pdf->Cell(0, 10, 'MOTIVO DE BAJA', 1, 1, 'C');

            $motivos = [
                mb_convert_encoding('Mal estado', "ISO-8859-1", "UTF-8"),
                mb_convert_encoding('Siniestro', "ISO-8859-1", "UTF-8"),
                mb_convert_encoding('Mal uso', "ISO-8859-1", "UTF-8"),
                mb_convert_encoding('Perdida o hurto', "ISO-8859-1", "UTF-8"),
                mb_convert_encoding('Daño eléctrico / electrónico', "ISO-8859-1", "UTF-8"),
                mb_convert_encoding('Deterioro', "ISO-8859-1", "UTF-8"),
                mb_convert_encoding('Obsoleto', "ISO-8859-1", "UTF-8"),
                mb_convert_encoding('Otro', "ISO-8859-1", "UTF-8")
            ];

            for ($i = 0; $i < count($motivos); $i += 2) {
                $motivo_1 = $motivos[$i];
                $motivo_2 = $motivos[$i + 1];

                $marca_1 = (mb_convert_encoding($equipo_data['motivo_baja'],"ISO-8859-1","UTF-8") === $motivo_1) ? 'X' : '';
                $marca_2 = (mb_convert_encoding($equipo_data['motivo_baja'],"ISO-8859-1","UTF-8") === $motivo_2) ? 'X' : '';

                $pdf->SetFont("Arial", "I", 11);
                $pdf->Cell(80, 10, $motivo_1, 1, 0, 'C');
                $pdf->SetFont("Arial", "", 11);
                $pdf->Cell(15, 10, $marca_1, 1, 0, 'C');
                $pdf->SetFont("Arial", "I", 11);
                $pdf->Cell(80, 10, $motivo_2, 1, 0, 'C');
                $pdf->SetFont("Arial", "", 11);
                $pdf->Cell(15, 10, $marca_2, 1, 1, 'C');
            }

            $pdf->Ln(5);
            $pdf->SetFont("Arial", "B", 11);
            $pdf->Cell(0, 10, mb_convert_encoding('DETALLE DE CONCEPTO TÉCNICO', "ISO-8859-1", "UTF-8"), 1, 1, 'C');
            if ($equipo_data['motivo_baja'] === 'Otro') {
                $pdf->SetFont("Arial", "", 11);
                $pdf->MultiCell(0, 8, mb_convert_encoding('Otro Motivo:', "ISO-8859-1", "UTF-8") . ' ' . $equipo_data['otro_motivo_baja'], 1);
                $pdf->MultiCell(0, 8, $equipo_data['concepto_tecnico_baja'], 1);
            } else {
                $pdf->SetFont("Arial", "", 11);
                $pdf->MultiCell(0, 8, $equipo_data['concepto_tecnico_baja'], 1);
            }

            $pdf->Ln(10);
            $pdf->SetFont("Arial", "B", 11);

            $pdf->Ln(20);
            $pdf->Cell(95, 10, "_________________________", 0, 0, 'C');
            $pdf->Cell(95, 10, "_________________________", 0, 1, 'C');
            $pdf->Cell(95, 6, mb_convert_encoding("Líder Responsable del Proceso", "ISO-8859-1", "UTF-8"), 0, 0, 'C');
            $pdf->Cell(95, 6, mb_convert_encoding("Responsable de revisión técnica", "ISO-8859-1", "UTF-8"), 0, 1, 'C');

            $nombre_archivo = 'acta_baja_equipo_' . $equipo_data['cod_equipo'] . '.pdf';
            $pdf->Output('I', $nombre_archivo);

            echo json_encode([
                'status' => true,
                'message' => '✅ Acta de baja generada correctamente',
                'data' => $equipo_data
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => '❌ ' . $e->getMessage()
            ]);
        }
        break;

    case 'listar_equipos_sede':
        verificarRol([1, 2, 3]);
        try {
            if (!isset($_GET['sede_id'])) {
                throw new Exception("❌ El ID de la sede no puede estar vacío.");
            }
            $sede_id = intval($_GET['sede_id']);
            $datos = $equipo->listarEquiposSedes($sede_id);
            echo json_encode(formatearEquiposDataTable($datos));
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
        break;
}