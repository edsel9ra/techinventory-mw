<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php require_once "../../public/main/head.php"; ?>
    <title>Listado de Equipos</title>
</head>

<body>
    <?php require_once "../../public/main/nav.php"; ?>
    <section class="container mt-4">
        <div class="p-4 bg-white shadow rounded">
            <div class="row">
                <div class="col d-flex align-items-center justify-content-center">
                    <h2 class="mb-3">Listado de Equipos Tecnológicos</h2>
                </div>
                <div class="col-auto">
                    <?php if ($_SESSION['rol_id'] == 1 || $_SESSION['rol_id'] == 3) { ?>
                        <button id="btnRegistrarEquipo" class="btn btn-primary fw-bold d-flex align-items-center gap-2 justify-content-center flex-nowrap">
                            <span><i class="fa-regular fa-square-plus"></i></span>
                            <span>Registrar Equipo en Inventario</span>
                        </button>
                    <?php } ?>
            </div>
            <div class="mb-3 row">
                <div class="col filtros d-flex align-items-center">
                    <div class="input-group mb-3">
                        <label class="input-group-text fw-bold mr-2">Filtrar por tipo</label>
                        <select id="filtroTipo" class="form-select form-control-sm w-auto">
                            <option value="">Todos</option>
                        </select>
                    </div>
                </div>
                <div class="col filtros d-flex align-items-center">
                    <div class="input-group mb-3">
                        <label class="input-group-text fw-bold mr-2">Filtrar por estado</label>
                        <select id="filtroEstado" class="form-select form-control-sm w-auto">
                            <option value="">Todos</option>
                        </select>
                    </div>
                </div>
                <div class="col filtros d-flex align-items-center">
                    <div class="input-group mb-3">
                        <label class="input-group-text fw-bold mr-2">Filtrar por Sede</label>
                        <select id="filtroSede" class="form-select form-control-sm w-auto">
                            <option value="">Todos</option>
                        </select>
                    </div>
                </div>
                <div class="col-auto">
                    <button id="btnLimpiarFiltros" class="btn btn-outline-secondary btn-sm ms-2" style="display: none;">
                        Limpiar filtros
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-borderless align-middle table-hover mb-3" id="tablaEquipos">
                    <thead class="table-dark">
                        <tr class="align-bottom">
                            <th class="d-none d-sm-table-cell text-center">Sede</th>
                            <th class="d-none d-sm-table-cell text-center">Activo Fijo</th>
                            <th class="d-none d-sm-table-cell text-center">Tipo Equipo</th>
                            <th class="d-none d-sm-table-cell text-center">Serial</th>
                            <th class="d-none d-sm-table-cell text-center">Estado</th>
                            <th class="d-none d-sm-table-cell text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="align-middle text-center"></tbody>
                </table>
            </div>

            <nav>
                <ul class="pagination justify-content-center" id="paginacion"></ul>
            </nav>
        </div>
    </section>


    <?php require_once "../../public/main/js.php"; ?>
    <script type="text/javascript" src="listarequipo.js"></script>
    <script>
        const rol_id = <?php echo $_SESSION['rol_id']; ?>;
    </script>
</body>

</html>