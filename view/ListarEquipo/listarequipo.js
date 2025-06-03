let equipos = [];

document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const estadoParam = params.get('estado')?.toLowerCase() || '';

    fetch('../../controllers/equipo.php?op=listar')
        .then(res => res.json())
        .then(data => {
            equipos = data.aaData;
            initSelects(equipos);
            renderTablaConDataTable(equipos, estadoParam);
        });

    ['filtroTipo', 'filtroEstado', 'filtroSede'].forEach(id => {
        document.getElementById(id).addEventListener('change', () => {
            $('#tablaEquipos').DataTable().draw();
            verificarFiltrosActivos();
        });
    });

    if (rol_id == 1 || rol_id == 3) {
        document.getElementById('btnRegistrarEquipo').addEventListener('click', () => {
            window.open('../../view/NuevoEquipo/index.php', '_blank');
        });
    }
});

function initSelects(data) {
    const tipos = [...new Set(data.map(e => e[2]))].sort();
    const filtroTipo = document.getElementById('filtroTipo');
    tipos.forEach(tipo => {
        const option = document.createElement('option');
        option.value = tipo.toLowerCase();
        option.textContent = tipo;
        filtroTipo.appendChild(option);
    });

    const etiquetasEstado = {
        Activo: 'Activo',
        Inactivo: 'Inactivo',
        Baja: 'Dado de Baja'
    };
    const estados = [...new Set(data.map(e => e[4]))].sort();
    const filtroEstado = document.getElementById('filtroEstado');
    estados.forEach(estado => {
        const option = document.createElement('option');
        if (estado.toLowerCase() === 'activo') {
            option.classList.add('text-success');
        } else if (estado.toLowerCase() === 'inactivo') {
            option.classList.add('text-secondary');
        } else {
            option.classList.add('text-danger');
        }
        option.value = estado.toLowerCase();
        option.textContent = etiquetasEstado[estado] || estado;
        filtroEstado.appendChild(option);
    });

    const sedes = [...new Set(data.map(e => e[0]))].sort();
    const filtroSede = document.getElementById('filtroSede');
    sedes.forEach(sede => {
        const option = document.createElement('option');
        option.value = sede.toLowerCase();
        option.textContent = sede;
        filtroSede.appendChild(option);
    });
}

function renderTablaConDataTable(data, estadoInicial = '') {
    const table = $('#tablaEquipos').DataTable({
        data: data,
        destroy: true,
        columns: [
            { title: "Sede", data: 0 },
            { title: "Activo Fijo", data: 1 },
            { title: "Tipo Equipo", data: 2 },
            { title: "Serial", data: 3 },
            {
                title: "Estado",
                data: 4,
                render: (data) => {
                    switch (data) {
                        case "Activo": return '<span class="badge rounded-pill bg-success">Activo</span>';
                        case "Inactivo": return '<span class="badge rounded-pill bg-secondary">Inactivo</span>';
                        default: return '<span class="badge rounded-pill bg-danger">Dado de Baja</span>';
                    }
                }
            },
            {
                title: "Acciones",
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    const estado = row[4].toLowerCase();
                    const equipo_id = row[5]; // ID viene como último valor

                    const disabled = estado === 'baja' ? 'disabled' : '';

                    let botones = `
                        <button onclick="hojaVida('${equipo_id}')" class="btn btn-sm btn-info me-1" title="Información del equipo">
                            <i class="bi bi-info-circle-fill"></i>
                        </button>`;
                    if (estado === 'baja') {
                        botones += `
                        <button onclick="actaBaja('${equipo_id}')" class="btn btn-sm btn-dark" title="Generar acta de baja">
                            <i class="bi bi-file-earmark-pdf-fill"></i>
                        </button>`;
                    } else {
                        if (rol_id == 1 || rol_id == 2) {
                            botones += `
                            <button onclick="registrarMmto('${equipo_id}')" class="btn btn-sm btn-primary" title="Registrar mantenimiento">
                                <i class="bi bi-tools"></i>
                            </button>`;
                        }

                        if (rol_id == 1 || rol_id == 3) {
                            botones += `
                            <button onclick="actaEntrega('${equipo_id}')" class="btn btn-sm btn-danger" title="Generar acta de entrega">
                                <i class="bi bi-file-earmark-pdf-fill"></i>
                            </button>`;
                        }
                    }

                    return botones;
                }
            }
        ],
        dom: "<'row mb-3'<'col-md-6 d-flex align-items-center'B><'col-md-6'f>>" +
            "<'row'<'col-12'tr>>" +
            "<'row mt-3'<'col-md-6'i><'col-md-6'p>>",
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Exportar a Excel',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'pdfHtml5',
                text: 'Exportar a PDF',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            }
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/2.3.0/i18n/es-MX.json"
        },
        initComplete: function () {
            if (estadoInicial) {
                document.getElementById('filtroEstado').value = estadoInicial;
            }
            $.fn.dataTable.ext.search.push(function (settings, searchData, index, rowData, counter) {
                const tipoFiltro = document.getElementById('filtroTipo').value.toLowerCase();
                const estadoFiltro = document.getElementById('filtroEstado').value.toLowerCase();
                const sedeFiltro = document.getElementById('filtroSede').value.toLowerCase();

                const tipo = rowData[2].toLowerCase();
                const estado = rowData[4].toLowerCase();
                const sede = rowData[0].toLowerCase();

                return (!tipoFiltro || tipo === tipoFiltro) &&
                    (!estadoFiltro || estado === estadoFiltro) &&
                    (!sedeFiltro || sede === sedeFiltro);
            });
            table.draw();
            verificarFiltrosActivos();
        }
    });
}

// Funciones para abrir las ventanas
function hojaVida(equipo_id) {
    window.open(`../../view/HojaVida/?equipo_id=${equipo_id}`, '_blank');
}

function registrarMmto(equipo_id) {
    window.open(`../../view/Mantenimiento/?equipo_id=${equipo_id}`, '_blank');
}

function actaEntrega(equipo_id) {
    window.open(`../../controllers/equipo.php?op=acta_entrega_pdf&equipo_id=${equipo_id}`, '_blank');
}

function actaBaja(equipo_id) {
    window.open(`../../controllers/equipo.php?op=acta_baja_pdf&equipo_id=${equipo_id}`, '_blank');
}

document.getElementById('btnLimpiarFiltros').addEventListener('click', () => {
    document.getElementById('filtroEstado').value = '';
    document.getElementById('filtroTipo').value = '';
    document.getElementById('filtroSede').value = '';

    $('#tablaEquipos').DataTable().draw();
    verificarFiltrosActivos();

    // Opcional: limpiar también la URL
    const url = new URL(window.location.href);
    url.searchParams.delete('estado');
    url.searchParams.delete('tipo');
    url.searchParams.delete('sede');
    window.history.replaceState({}, '', url);
});

function verificarFiltrosActivos() {
    const estado = document.getElementById('filtroEstado').value;
    const tipo = document.getElementById('filtroTipo').value;
    const sede = document.getElementById('filtroSede').value;
    const btnLimpiar = document.getElementById('btnLimpiarFiltros');

    if (tipo || estado || sede) {
        btnLimpiar.style.display = 'inline-block';
    } else {
        btnLimpiar.style.display = 'none';
    }
}