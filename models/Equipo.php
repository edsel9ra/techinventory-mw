<?php
class Equipo extends Conectar
{
    //Se puede agregar otros tipos de equipos, segun sea el caso

    //Metodos para insertar equipos
    /**
     * Inserta un nuevo equipo en la base de datos.
     *
     * @param int $sede El ID de la sede.
     * @param int $tipo El ID del tipo de equipo.
     * @param string $marca La marca del equipo.
     * @param string $modelo El modelo del equipo.
     * @param string $serial El serial del equipo.
     * @param string $cod_equipo El código interno del equipo.
     * @param string $estado El estado del equipo.
     * @param string $responsable El responsable del equipo.
     * @param array $detalles Los detalles del equipo.
     * @return bool True si se insertó correctamente, false en caso contrario.
     * @throws Exception Si ocurre un error durante la inserción.
     */
    public function insertEquipo($sede, $tipo, $marca, $modelo, $serial, $cod_equipo, $estado, $responsable, $detalles)
    {
        $conectar = parent::conexion();

        try {
            $conectar->beginTransaction();
            $detalle_equipo_id = null;

            switch (intval($tipo)) {
                // Inserta características de un equipo de cómputo
                case 1:
                    $detalle_equipo_id = $this->insertDetalleComputador($conectar, $detalles);

                    $monitor_id = $detalles['monitor_id'] ?? null;
                    $nombre_pc = $detalles['nombre_pc'] ?? null;
                    // Si el computador tiene un monitor asignado, actualizar el estado del monitor
                    if (!empty($monitor_id)) {
                        $this->validarMonitorAsignado($conectar, $monitor_id);
                        $stmt = $conectar->prepare("UPDATE tbl_monitores SET asignado = 1, nombre_equipo_asignado = ? WHERE monitor_id = ?");
                        $stmt->execute([$nombre_pc, $monitor_id]);
                        if ($stmt->rowCount() === 0) {
                            throw new Exception("No se pudo asignar el monitor con ID $monitor_id.");
                        }
                    }
                    break;

                case 2:
                    // Validar que los datos necesarios para el monitor estén presentes
                    if (!isset($detalles['tamanio_pulgadas'])) {
                        throw new Exception("Falta el tamaño del monitor.");
                    }
                    // Insertar el detalle del monitor
                    $detalle_equipo_id = $this->insertDetalleMonitor($conectar, $detalles);
                    break;

                case 3:
                    $detalle_equipo_id = $this->insertDetalleImpresora($conectar, $detalles);
                    break;

                case 4:
                    $detalle_equipo_id = $this->insertDetalleTablet($conectar, $detalles);
                    break;

                case 5:
                    $detalle_equipo_id = $this->insertDetalleDispositivoRed($conectar, $detalles);
                    break;

                default:
                    throw new Exception("Tipo de equipo no reconocido: $tipo");
            }

            // Validar que se haya generado detalle
            if (!$detalle_equipo_id) {
                throw new Exception("No se pudo guardar el detalle del equipo.");
            }

            // Insertar en la tabla principal de equipos
            $stmt = $conectar->prepare("INSERT INTO tbl_equipos 
            (sede_id, tipo_equipo_id, marca_equipo, modelo_equipo, serial_equipo, cod_equipo, estado, detalle_equipo_id, responsable)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $sede,
                $tipo,
                $marca,
                $modelo,
                $serial,
                $cod_equipo,
                $estado,
                $detalle_equipo_id,
                $responsable
            ]);

            $conectar->commit();
            return true;

        } catch (Exception $e) {
            $conectar->rollBack();
            throw new Exception("Error al guardar el equipo: " . $e->getMessage());
        }
    }

    private function insertDetalleComputador($conectar, $detalles)
    {
        $campos_obligatorios = [
            'nombre_pc',
            'tipo_computador',
            'procesador',
            'ram',
            'disco',
            'capacidad_disco',
            'os',
            'licencia_microsoft',
            'tiene_monitor',
            'tipo_cargador'
        ];

        foreach ($campos_obligatorios as $campo) {
            if (!isset($detalles[$campo])) {
                throw new Exception("Falta el campo '$campo' en los detalles del computador.");
            }
        }

        $monitor_id = null; // Definir monitor_id como null por defecto
        $nombre_pc = $detalles['nombre_pc'];

        if (
            $detalles['tipo_computador'] === 'Desktop' &&
            isset($detalles['tiene_monitor']) && $detalles['tiene_monitor'] == 1
        ) {
            if (empty($detalles['monitor_id'])) {
                throw new Exception("El monitor es obligatorio para computadores tipo Desktop con monitor.");
            }
            $monitor_id = $detalles['monitor_id'];
        }

        $stmt = $conectar->prepare("INSERT INTO tbl_computadores 
        (nombre_pc, tipo_computador, procesador, ram, disco, capacidad_disco, os, licencia_microsoft, tiene_monitor, monitor_id, tipo_cargador)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $nombre_pc,
            $detalles['tipo_computador'],
            $detalles['procesador'],
            $detalles['ram'],
            $detalles['disco'],
            $detalles['capacidad_disco'],
            $detalles['os'],
            $detalles['licencia_microsoft'],
            $detalles['tiene_monitor'],
            $monitor_id,
            $detalles['tipo_cargador']
        ]);

        return $conectar->lastInsertId();
    }

    private function insertDetalleMonitor($conectar, $detalles)
    {
        // Validar que los datos necesarios estén presentes
        if (!isset($detalles['tamanio_pulgadas'])) {
            throw new Exception("Falta el tamaño del monitor.");
        }

        // Insertar el monitor
        $stmt = $conectar->prepare("INSERT INTO tbl_monitores (tamanio_pulgadas, asignado) VALUES (?, 0)");
        $stmt->execute([$detalles['tamanio_pulgadas']]);

        // Retornar el ID del monitor recién insertado
        return $conectar->lastInsertId();
    }

    /**
     * Valida si un monitor está asignado a otro equipo.
     *
     * @param PDO $conectar La conexión a la base de datos.
     * @param int $monitor_id El ID del monitor a validar.
     * @param bool $permitirReasignacion Indica si se permite reasignar el monitor.
     * @return bool True si el monitor está libre, false si está asignado.
     * @throws Exception Si el monitor no existe o está asignado.
     */
    private function validarMonitorAsignado($conectar, $monitor_id, $permitirReasignacion = false)
    {
        $stmt = $conectar->prepare("SELECT asignado, nombre_equipo_asignado FROM tbl_monitores WHERE monitor_id = ?");
        $stmt->execute([$monitor_id]);
        $monitor = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$monitor) {
            throw new Exception("El monitor con ID $monitor_id no existe.");
        }

        if ((int) $monitor['asignado'] === 1) {
            if (!$permitirReasignacion) {
                throw new Exception("El monitor con ID $monitor_id ya está asignado al equipo '{$monitor['nombre_equipo_asignado']}'.");
            }
            return true; // Indica que debe desasignarse antes
        }

        return false; // El monitor está libre
    }

    private function insertDetalleImpresora($conectar, $detalles)
    {
        if (!isset($detalles['tecnologia'], $detalles['conexion'])) {
            throw new Exception("Faltan datos de la impresora");
        }
        $stmt = $conectar->prepare("INSERT INTO tbl_impresoras (tecnologia, conexion) VALUES (?, ?)");
        $stmt->execute([$detalles['tecnologia'], $detalles['conexion']]);
        return $conectar->lastInsertId();
    }

    private function insertDetalleTablet($conectar, $detalles)
    {
        if (
            !isset(
            $detalles['procesador'],
            $detalles['ram'],
            $detalles['rom'],
            $detalles['os'],
            $detalles['version_os']
        )
        ) {
            throw new Exception("Faltan datos de la tablet");
        }
        $stmt = $conectar->prepare("INSERT INTO tbl_tablets (procesador, ram, rom, os, version_os) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $detalles['procesador'],
            $detalles['ram'],
            $detalles['rom'],
            $detalles['os'],
            $detalles['version_os']
        ]);
        return $conectar->lastInsertId();
    }

    private function insertDetalleDispositivoRed($conectar, $detalles)
    {
        if (
            !isset(
            $detalles['tipo_dispositivo'],
            $detalles['ip_address'],
            $detalles['mac_address'],
            $detalles['ubicacion']
        )
        ) {
            throw new Exception("Faltan datos del dispositivo.");
        }
        $stmt = $conectar->prepare("INSERT INTO tbl_dispositivos_red (tipo_dispositivo, ip_address, mac_address, ubicacion) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $detalles['tipo_dispositivo'],
            $detalles['ip_address'],
            $detalles['mac_address'],
            $detalles['ubicacion']
        ]);
        return $conectar->lastInsertId();
    }

    /** 
     * Se agregan otros tipos de equipos
     */

    //Obetner los monitores bajo ciertas condiciones y de una determinada sede, debe estar registrado el monitor
    public function get_monitor($sede_id = null)
    {
        $conectar = parent::conexion();

        try {
            $sql = "SELECT e.*, m.* 
                FROM tbl_equipos e 
                LEFT JOIN tbl_monitores m ON e.detalle_equipo_id = m.monitor_id 
                WHERE e.tipo_equipo_id = 2 AND m.asignado = 0";

            if ($sede_id !== null) {
                $sql .= " AND e.sede_id = :sede_id";
            }

            $stmt = $conectar->prepare($sql);

            if ($sede_id !== null) {
                $stmt->bindParam(':sede_id', $sede_id, PDO::PARAM_INT);
            }

            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Error al obtener monitores: " . $e->getMessage());
        }
    }

    //Metodos get
    public function listarEquipos()
    {
        $conectar = parent::conexion();

        try {
            $stmt = $conectar->prepare("SELECT e.*, s.nombre_sede, te.nombre_equipo FROM tbl_equipos e JOIN tbl_sedes s ON e.sede_id = s.sede_id JOIN tbl_tipos_equipos te ON e.tipo_equipo_id = te.tipo_equipo_id");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Error al obtener equipos: " . $e->getMessage());
        }
    }

    public function listarEquipoConDetalle($equipo_id)
    {
        $conectar = parent::conexion();

        try {
            // Obtener información general del equipo
            $stmt = $conectar->prepare("
            SELECT e.*, s.nombre_sede, te.nombre_equipo
            FROM tbl_equipos e 
            JOIN tbl_sedes s ON e.sede_id = s.sede_id 
            JOIN tbl_tipos_equipos te ON e.tipo_equipo_id = te.tipo_equipo_id
            WHERE e.equipo_id = ?
        ");
            $stmt->execute([$equipo_id]);
            $equipo = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$equipo) {
                throw new Exception("El equipo con ID $equipo_id no existe.");
            }

            // Obtener detalles específicos según el tipo de equipo
            $detalle = null;
            switch (intval($equipo['tipo_equipo_id'])) {
                case 1:
                    // Consulta para obtener información del computador y su monitor asociado
                    $stmt = $conectar->prepare("
                    SELECT 
                        c.*, 
                        m.monitor_id AS monitor_id
                    FROM tbl_computadores c
                    LEFT JOIN tbl_monitores m ON m.monitor_id = c.monitor_id
                    WHERE c.computador_id = ?
                ");
                    $stmt->execute([$equipo['detalle_equipo_id']]);
                    $detalle = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Verificar si el computador tiene un monitor asociado
                    if ($detalle && $detalle['monitor_id']) {
                        $stmt_monitor = $conectar->prepare("
                        SELECT e.*, m.tamanio_pulgadas AS pulgadas 
                        FROM tbl_equipos e 
                        JOIN tbl_monitores m ON e.detalle_equipo_id = m.monitor_id 
                        WHERE e.tipo_equipo_id = 2 AND e.detalle_equipo_id = ?
                    ");
                        $stmt_monitor->execute([$detalle['monitor_id']]);
                        $monitor = $stmt_monitor->fetch(PDO::FETCH_ASSOC);

                        // Agregar los detalles del monitor al detalle del computador
                        $detalle['monitor'] = $monitor;
                    } else {
                        $detalle['monitor'] = null;
                    }
                    break;

                case 2:
                    $stmt = $conectar->prepare("
                        SELECT * 
                        FROM tbl_monitores
                        WHERE monitor_id = ?
                        ");
                    $stmt->execute([$equipo['detalle_equipo_id']]);
                    $detalle = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$detalle) {
                        throw new Exception("El monitor con ID {$equipo['detalle_equipo_id']} no está correctamente relacionado.");
                    }
                    break;

                case 3:
                    $stmt = $conectar->prepare("
                    SELECT * 
                    FROM tbl_impresoras 
                    WHERE impresora_id = ?
                ");
                    $stmt->execute([$equipo['detalle_equipo_id']]);
                    $detalle = $stmt->fetch(PDO::FETCH_ASSOC);
                    break;

                case 4:
                    $stmt = $conectar->prepare("
                    SELECT * 
                    FROM tbl_tablets 
                    WHERE tablet_id = ?
                ");
                    $stmt->execute([$equipo['detalle_equipo_id']]);
                    $detalle = $stmt->fetch(PDO::FETCH_ASSOC);
                    break;

                case 5:
                    $stmt = $conectar->prepare("
                    SELECT * 
                    FROM tbl_dispositivos_red 
                    WHERE dispositivo_id = ?
                ");
                    $stmt->execute([$equipo['detalle_equipo_id']]);
                    $detalle = $stmt->fetch(PDO::FETCH_ASSOC);
                    break;

                default:
                    throw new Exception("Tipo de equipo no reconocido: " . intval($equipo['tipo_equipo_id']));
            }

            // Combinar información general y detalles específicos
            $equipo['detalle'] = $detalle;

            return $equipo;

        } catch (Exception $e) {
            throw new Exception("Error al listar equipo con detalle: " . $e->getMessage());
        }
    }

    public function get_equipo_id($equipo_id)
    {
        $conectar = parent::conexion();

        try {
            $stmt = $conectar->prepare("SELECT e.*, te.nombre_equipo, s.nombre_sede FROM tbl_equipos e JOIN tbl_tipos_equipos te ON e.tipo_equipo_id = te.tipo_equipo_id LEFT JOIN tbl_sedes s ON e.sede_id = s.sede_id WHERE equipo_id = ?");
            $stmt->execute([$equipo_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al obtener el equipo: " . $e->getMessage());
        }
    }

    public function get_tipo_equipo()
    {
        $conectar = parent::conexion();
        try {
            $stmt = $conectar->prepare("SELECT * FROM tbl_tipos_equipos");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Error al obtener el tipo de equipo: " . $e->getMessage());
        }
    }

    public function get_detalle_tipo($tipo_equipo_id, $detalle_equipo_id)
    {
        $conectar = parent::conexion();

        try {
            switch (intval($tipo_equipo_id)) {
                case 1:
                    $stmt = $conectar->prepare("SELECT * FROM tbl_computadores WHERE computador_id = ?");
                    break;
                case 2:
                    $stmt = $conectar->prepare("SELECT * FROM tbl_monitores WHERE monitor_id = ?");
                    break;
                case 3:
                    $stmt = $conectar->prepare("SELECT * FROM tbl_impresoras WHERE impresora_id = ?");
                    break;
                case 4:
                    $stmt = $conectar->prepare("SELECT * FROM tbl_tablets WHERE tablet_id = ?");
                    break;
                case 5:
                    $stmt = $conectar->prepare("SELECT * FROM tbl_dispositivos_red WHERE dispositivo_id = ?");
                    break;
                default:
                    throw new Exception("Tipo de equipo no reconocido: " . intval($tipo_equipo_id));
            }

            $stmt->execute([$detalle_equipo_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al obtener el detalle del equipo: " . $e->getMessage());
        }
    }

    //Metodos editar
    public function editarEquipo($equipo_id, $datos)
    {
        $conectar = parent::conexion();

        if (!$conectar) {
            throw new Exception("No se pudo establecer la conexión con la base de datos.");
        }

        try {
            $conectar->beginTransaction();

            $camposObligatorios = ['sede', 'estado', 'responsable'];
            foreach ($camposObligatorios as $campo) {
                if (empty($datos[$campo])) {
                    throw new Exception("❌ El campo {$campo} es obligatorio.");
                }
            }

            if (!isset($datos['detalles']) || !is_array($datos['detalles'])) {
                throw new Exception("❌ Los detalles no son validos.");
            }

            // 1. Traer información actual del equipo
            $stmt = $conectar->prepare("SELECT tipo_equipo_id, detalle_equipo_id, estado FROM tbl_equipos WHERE equipo_id = ?");
            $stmt->execute([$equipo_id]);
            $equipoExistente = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$equipoExistente) {
                throw new Exception("El equipo con ID $equipo_id no existe.");
            }

            // 2. Actualizar tabla general tbl_equipos
            $estadoAnterior = $equipoExistente['estado'];
            $estadoNuevo = $datos['estado'];

            if ($estadoAnterior !== 'Baja' && $estadoNuevo === 'Baja') {
                $fechaBaja = date('Y-m-d H:i:s');
                $stmt = $conectar->prepare("UPDATE tbl_equipos SET 
                    sede_id = ?, 
                    estado = ?, 
                    responsable = ?,
                    fecha_baja = ?,
                    proceso_baja = ?,
                    motivo_baja = ?,
                    otro_motivo_baja = ?,
                    concepto_tecnico_baja = ?
                    WHERE equipo_id = ?
                ");
                $stmt->execute([
                    $datos['sede'],
                    $estadoNuevo,
                    $datos['responsable'],
                    $fechaBaja,
                    $datos['proceso_baja'] ?? null,
                    $datos['motivo_baja'] ?? null,
                    $datos['otro_motivo_baja'] ?? null,
                    $datos['concepto_tecnico_baja'] ?? null,
                    $equipo_id
                ]);
            } else {
                $stmt = $conectar->prepare("UPDATE tbl_equipos SET 
                    sede_id = ?, 
                    estado = ?, 
                    responsable = ?
                    WHERE equipo_id = ?
                ");
                $stmt->execute([
                    $datos['sede'],
                    $estadoNuevo,
                    $datos['responsable'],
                    $equipo_id
                ]);
            }

            // 3. Actualizar tabla de detalles según tipo de equipo
            $tipoEquipo = intval($datos['tipo_equipo_id']); // Usamos el tipo nuevo seleccionado
            $detalleId = $equipoExistente['detalle_equipo_id']; // Siempre usamos el mismo detalle ID que ya existía

            switch ($tipoEquipo) {
                case 1:
                    $this->updateDetalleComputador($conectar, $datos['detalles'], $detalleId);
                    break;
                // Cuando es tipo 2, no se actualiza el monitor, pero si se actualiza el estado, la sede y el responsable del monitor si es necesario
                case 2:
                    break;
                case 3:
                    $this->updateDetalleImpresora($conectar, $datos['detalles'], $detalleId);
                    break;
                case 4:
                    $this->updateDetalleTablet($conectar, $datos['detalles'], $detalleId);
                    break;
                case 5:
                    $this->updateDetalleDispositivoRed($conectar, $datos['detalles'], $detalleId);
                    break;
                default:
                    throw new Exception("Tipo de equipo no reconocido: " . intval($tipoEquipo));
            }

            // 5. Confirmar la transacción
            $conectar->commit();
            return true;

        } catch (Exception $e) {
            if ($conectar->inTransaction()) {
                $conectar->rollBack();
            }
            throw new Exception("Error al editar el equipo: " . $e->getMessage());
        }
    }

    private function updateDetalleComputador($conectar, $detalles, $detalle_equipo_id)
    {
        try {

            // 1. Consultar el monitor actualmente asignado a este computador
            $stmt = $conectar->prepare("SELECT monitor_id FROM tbl_computadores WHERE computador_id = ?");
            $stmt->execute([$detalle_equipo_id]);
            $computadorActual = $stmt->fetch(PDO::FETCH_ASSOC);
            $monitorAnterior = $computadorActual['monitor_id'] ?? null;

            $nuevoMonitorId = array_key_exists('monitor_id', $detalles) ? $detalles['monitor_id'] : null;
            $tieneMonitor = (int) ($detalles['tiene_monitor'] ?? 0);

            // Preservar el monitor actual si no se envió uno nuevo y debe tener monitor
            if ($tieneMonitor === 1 && empty($nuevoMonitorId)) {
                $nuevoMonitorId = $monitorAnterior;
            }

            // 2. Si antes tenía monitor y se quitó o se cambió, desasignar el monitor anterior
            if (!empty($monitorAnterior) && ($tieneMonitor === 0 || $monitorAnterior != $nuevoMonitorId)) {
                $stmt = $conectar->prepare("UPDATE tbl_monitores SET asignado = 0, nombre_equipo_asignado = NULL WHERE monitor_id = ?");
                $stmt->execute([$monitorAnterior]);
            }

            // 3. Si tiene un nuevo monitor, validarlo y asignarlo
            if ($tieneMonitor === 1 && !empty($nuevoMonitorId)) {
                if ($monitorAnterior != $nuevoMonitorId) {
                    $this->validarMonitorAsignado($conectar, $nuevoMonitorId);
                    $stmt = $conectar->prepare("UPDATE tbl_monitores SET asignado = 1, nombre_equipo_asignado = ? WHERE monitor_id = ?");
                    $stmt->execute([$detalles['nombre_pc'], $nuevoMonitorId]);
                }
            }

            // 3. Actualizar el registro del computador
            $stmt = $conectar->prepare("UPDATE tbl_computadores SET 
            nombre_pc = ?, 
            ram = ?, 
            disco = ?, 
            capacidad_disco = ?, 
            os = ?, 
            licencia_microsoft = ?, 
            tiene_monitor = ?, 
            monitor_id = ?,
            tipo_cargador = ?
            WHERE computador_id = ?");

            $stmt->execute([
                $detalles['nombre_pc'],
                $detalles['ram'],
                $detalles['disco'],
                $detalles['capacidad_disco'],
                $detalles['os'],
                $detalles['licencia_microsoft'],
                (int) $detalles['tiene_monitor'],
                $detalles['monitor_id'] ?? null,
                $detalles['tipo_cargador'],
                $detalle_equipo_id
            ]);

        } catch (Exception $e) {
            throw new Exception("Error al actualizar el computador: " . $e->getMessage());
        }
    }

    private function updateDetalleImpresora($conectar, $detalles, $detalle_equipo_id)
    {
        $stmt = $conectar->prepare("UPDATE tbl_impresoras SET 
            conexion = ? 
            WHERE impresora_id = ?");

        $stmt->execute([
            $detalles['conexion'],
            $detalle_equipo_id
        ]);
    }

    private function updateDetalleTablet($conectar, $detalles, $detalle_equipo_id)
    {
        $stmt = $conectar->prepare("UPDATE tbl_tablets SET 
            os = ?, 
            version_os = ? 
            WHERE tablet_id = ?");

        $stmt->execute([
            $detalles['os'],
            $detalles['version_os'],
            $detalle_equipo_id
        ]);
    }

    private function updateDetalleDispositivoRed($conectar, $detalles, $detalle_equipo_id)
    {
        $stmt = $conectar->prepare("UPDATE tbl_dispositivos_red SET 
            tipo_dispositivo = ?, 
            ip_address = ?, 
            mac_address = ?, 
            ubicacion = ? 
            WHERE dispositivo_id = ?");

        $stmt->execute([
           $detalles['tipo_dispositivo'],
            $detalles['ip_address'],
            $detalles['mac_address'],
            $detalles['ubicacion'],
            $detalle_equipo_id
        ]);
    }

    /** 
     * Se agregan otros tipos de equipos
     */

    public function generarCodigoEquipo($sede_id)
    {
        $conectar = parent::conexion();
        try {
            $sql_sede = "SELECT codigo_sede FROM tbl_sedes WHERE sede_id = ?";
            $stmt = $conectar->prepare($sql_sede);
            $stmt->execute([$sede_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $codigo_sede = $row['codigo_sede'] ?? '00';

            $intento = 1;
            do {
                $maximo = "SELECT MAX(CAST(SUBSTRING(cod_equipo, -3) AS UNSIGNED)) AS ultimo FROM tbl_equipos WHERE cod_equipo  LIKE ?";
                $like = "EQTI{$codigo_sede}%";
                $stmt2 = $conectar->prepare($maximo);
                $stmt2->execute([$like]);
                $row = $stmt2->fetch(PDO::FETCH_ASSOC);
                $ultimo = is_null($row['ultimo']) ? 0 : (int) $row['ultimo'];
                $consecutivo = str_pad($row['ultimo'] + 1, 3, '0', STR_PAD_LEFT);
                $cod_equipo = "EQTI{$codigo_sede}{$consecutivo}";
                $intento++;
            } while ($this->existeCodigoEquipo($cod_equipo));
            return $cod_equipo;
        } catch (Exception $e) {
            throw new Exception("Error al generar el código del equipo: " . $e->getMessage());
        }
    }

    public function existeCodigoEquipo($cod_equipo)
    {
        $conectar = parent::conexion();
        try {
            $stmt = $conectar->prepare("SELECT COUNT(*) FROM tbl_equipos WHERE cod_equipo = ?");
            $stmt->execute([$cod_equipo]);
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            throw new Exception("Error al verificar el código del equipo: " . $e->getMessage());
        }
    }

    //Funciones de Visualizacion de información
    public function totalEquipos()
    {
        try {
            $conectar = parent::conexion();
            $stmt = $conectar->prepare("SELECT COUNT(*) AS total_equipos FROM tbl_equipos");
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            throw new Exception("Error al obtener el total de equipos: " . $e->getMessage());
        }
    }

    public function equiposActivos()
    {
        try {
            $conectar = parent::conexion();
            $stmt = $conectar->prepare("SELECT COUNT(*) AS total_equipos_activo FROM tbl_equipos WHERE estado = 'Activo'");
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            throw new Exception("Error al obtener los equipos activos: " . $e->getMessage());
        }
    }

    public function equiposInactivos()
    {
        try {
            $conectar = parent::conexion();
            $stmt = $conectar->prepare("SELECT COUNT(*) AS total_equipos_inactivo FROM tbl_equipos WHERE estado = 'Inactivo'");
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            throw new Exception("Error al obtener los equipos inactivos: " . $e->getMessage());
        }
    }

    public function equiposBaja()
    {
        try {
            $conectar = parent::conexion();
            $stmt = $conectar->prepare("SELECT COUNT(*) AS total_equipos_baja FROM tbl_equipos WHERE estado = 'Baja'");
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            throw new Exception("Error al obtener los equipos en baja: " . $e->getMessage());
        }
    }

    public function equiposPorTipoActivos()
    {
        try {
            $conectar = parent::conexion();
            $stmt = $conectar->prepare("SELECT te.nombre_equipo, COUNT(*) AS total_tipo_equipo_activo FROM tbl_equipos e JOIN tbl_tipos_equipos te ON e.tipo_equipo_id = te.tipo_equipo_id WHERE e.estado = 'Activo' GROUP BY te.nombre_equipo");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al obtener los equipos por tipo activos: " . $e->getMessage());
        }
    }

    public function equiposPorTipoInactivos()
    {
        try {
            $conectar = parent::conexion();
            $stmt = $conectar->prepare("SELECT te.nombre_equipo, COUNT(*) AS total_tipo_equipo_inactivo FROM tbl_equipos e JOIN tbl_tipos_equipos te ON e.tipo_equipo_id = te.tipo_equipo_id WHERE e.estado = 'Inactivo' GROUP BY te.nombre_equipo");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al obtener los equipos por tipo inactivos: " . $e->getMessage());
        }
    }

    public function equiposPorTipoBaja()
    {
        try {
            $conectar = parent::conexion();
            $stmt = $conectar->prepare("SELECT te.nombre_equipo, COUNT(*) AS total_tipo_equipo_baja FROM tbl_equipos e JOIN tbl_tipos_equipos te ON e.tipo_equipo_id = te.tipo_equipo_id WHERE e.estado = 'Baja' GROUP BY te.nombre_equipo");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al obtener los equipos por tipo baja: " . $e->getMessage());
        }
    }

    public function equiposPorSedeActivos()
    {
        try {
            $conectar = parent::conexion();
            $stmt = $conectar->prepare("SELECT s.nombre_sede, COUNT(*) AS total_sede_activo FROM tbl_equipos e JOIN tbl_sedes s ON e.sede_id = s.sede_id WHERE e.estado = 'Activo' GROUP BY s.nombre_sede");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al obtener los equipos por sede activos: " . $e->getMessage());
        }
    }

    public function insertImagenEquipo($equipo_id, $ruta_imagen, $descripcion = null)
    {
        try {
            $conectar = parent::conexion();
            $stmt = $conectar->prepare("INSERT INTO tbl_equipo_imagenes (equipo_id, ruta_imagen, descripcion) VALUES (?, ?, ?)");
            $stmt->execute([$equipo_id, $ruta_imagen, $descripcion]);
            return true;
        } catch (Exception $e) {
            throw new Exception("Error al insertar la imagen del equipo: " . $e->getMessage());
        }
    }

    public function get_imagenes_equipo($equipo_id)
    {
        try {
            $conectar = parent::conexion();
            $stmt = $conectar->prepare("SELECT * FROM tbl_equipo_imagenes WHERE equipo_id = ?");
            $stmt->execute([$equipo_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Error al obtener las imagenes del equipo: " . $e->getMessage());
        }
    }

    public function deleteImagenEquipo($imagen_id)
    {
        try {
            $conectar = parent::conexion();
            $stmt = $conectar->prepare("SELECT * FROM tbl_equipo_imagenes WHERE imagen_id = ?");
            $stmt->execute([$imagen_id]);
            $img = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$img) {
                throw new Exception("❌ La imagen no existe");
            }

            $rutaFisica = __DIR__ . '/../' . ltrim($img['ruta_imagen'], '/');
            if (file_exists($rutaFisica)) {
                unlink($rutaFisica);
            }
            $stmt = $conectar->prepare("DELETE FROM tbl_equipo_imagenes WHERE imagen_id = ?");
            $stmt->execute([$imagen_id]);
            return true;
        } catch (Exception $e) {
            throw new Exception("Error al eliminar la imagen del equipo: " . $e->getMessage());
        }
    }

    public function listarEquiposSedes($sede_id)
    {
        try {
            $conectar = parent::conexion();
            $stmt = $conectar->prepare("SELECT e.*, s.nombre_sede, te.nombre_equipo 
                FROM tbl_equipos e 
                JOIN tbl_sedes s ON e.sede_id = s.sede_id 
                JOIN tbl_tipos_equipos te ON e.tipo_equipo_id = te.tipo_equipo_id 
                WHERE e.sede_id = ? AND e.estado != 'Baja'");
            $stmt->execute([$sede_id]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (Exception $e) {
            throw new Exception("Error al obtener los equipos por sede: " . $e->getMessage());
        }
    }
}