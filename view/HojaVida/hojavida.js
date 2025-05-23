const urlParams = new URLSearchParams(window.location.search);
const equipoId = urlParams.get('equipo_id');

if (equipoId) {
    fetch(`../../controllers/equipo.php?op=listar_detalle&equipo_id=${equipoId}`)
        .then(response => response.json())
        .then(response => {
            if (response.status) {
                mostrarDetalle(response.data);
            } else {
                document.getElementById('contenido-detalle').innerHTML = `
                        <div class="alert alert-danger">${response.message}</div>`;
            }
        });
}

// Esta función recibe un objeto de equipo y genera el HTML para mostrar su detalle
function mostrarDetalle(equipo) {
    //Arreglos Asociativos
    const opcionesComputador = {
        0: 'No',
        1: 'Sí',
        2: 'No Aplica',
        null: 'No'
    };

    const opcionesMonitor = {
        0: 'No',
        1: 'Sí',
        2: 'No Aplica',
        null: 'No'
    };

    const opcionesEstado = {
        'Activo': 'Activo',
        'Inactivo': 'Inactivo',
        'Baja': 'Dado de Baja',
        null: 'Sin estado definido'
    };

    const fecha = new Date(equipo.fecha_registro);
    const fechaFormateada = fecha.toLocaleDateString('es-CO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    let detalleHtml = `
            <h2>Hoja de Vida del Equipo: ${equipo.cod_equipo}</h2>
            <div class="btn-group w-100 justify-content-between gap-2 flex-wrap">
                <button type="button" class="btn btn-info fw-bold d-flex align-items-center gap-2 justify-content-center flex-nowrap"
                    data-bs-toggle="collapse" data-bs-target="#multiCollapseInfoEquipo"
                    aria-expanded="false" aria-controls="multiCollapseInfoEquipo">
                    <span><i class="fa-solid fa-circle-info"></i></span>
                    <span>Ficha General</span>
                </button>

                <button type="button" class="btn btn-info fw-bold d-flex align-items-center gap-2 justify-content-center flex-nowrap"
                    data-bs-toggle="collapse" data-bs-target="#multiCollapseDetallesEquipo"
                    aria-expanded="false" aria-controls="multiCollapseDetallesEquipo">
                    <span><i class="fa-solid fa-circle-info"></i></span>
                    <span>Detalles del Equipo</span>
                </button>

                <button type="button" class="btn btn-info fw-bold d-flex align-items-center gap-2 justify-content-center flex-nowrap"
                    onclick="mostrarInformacionCompleta()">
                    <span><i class="fa-solid fa-circle-info"></i></span>
                    <span>Ficha Técnica Completa</span>
                </button>`;

                if (equipo.estado !== 'Baja' && rol_id != 2) {
                    detalleHtml += `
                        <button class="btn btn-warning fw-bold d-flex align-items-center gap-2 justify-content-center flex-nowrap" 
                            onclick="editarEquipo(${equipo.equipo_id})">
                            <span><i class="fa-solid fa-pencil-square"></i></span>
                            <span>Editar Equipo</span>
                        </button>`;
                }

                detalleHtml += `
                <button id="btnRegresar" class="btn btn-secondary fw-bold d-flex align-items-center gap-2 justify-content-center flex-nowrap"
                    onclick="regresarListado()">
                    <span><i class="fa-solid fa-rectangle-list"></i></span>
                    <span>Regresar al Listado</span>
                </button>
            </div>

            <hr>
            <div class="row">
                <div class="col-md-6">
                    <div class="collapse multi-collapse" id="multiCollapseInfoEquipo">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title fw-bold">Ficha General</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group mb-4">
                                    <li class="list-group-item card-text"><strong>Tipo:</strong> ${equipo.nombre_equipo}</li>
                                    <li class="list-group-item card-text"><strong>Marca:</strong> ${equipo.marca_equipo}</li>
                                    <li class="list-group-item card-text"><strong>Modelo:</strong> ${equipo.modelo_equipo}</li>
                                    <li class="list-group-item card-text"><strong>Serial:</strong> ${equipo.serial_equipo}</li>
                                    <li class="list-group-item card-text"><strong>Sede:</strong> ${equipo.nombre_sede}</li>
                                    <li class="list-group-item card-text"><strong>Estado:</strong> ${opcionesEstado[equipo.estado]}</li>
                                    <li class="list-group-item card-text"><strong>Responsable:</strong> ${equipo.responsable}</li>
                                    <li class="list-group-item card-text"><strong>Fecha Registro:</strong> ${fechaFormateada}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>`;

    const detalle = equipo.detalle;
    switch (equipo.nombre_equipo) {
        case 'Computador':
            detalleHtml += `
                <div class="col-md-6">
                    <div class="collapse multi-collapse" id="multiCollapseDetallesEquipo">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title fw-bold">Características Específicas</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <li class="list-group-item"><strong>Nombre Equipo:</strong> ${detalle.nombre_pc}</li>
                                <li class="list-group-item"><strong>Tipo:</strong> ${detalle.tipo_computador}</li>
                                <li class="list-group-item"><strong>Procesador:</strong> ${detalle.procesador}</li>
                                <li class="list-group-item"><strong>RAM:</strong> ${detalle.ram} GB</li>
                                <li class="list-group-item"><strong>Disco:</strong> ${detalle.disco}</li>
                                <li class="list-group-item"><strong>Capacidad Disco:</strong> ${detalle.capacidad_disco} GB</li>
                                <li class="list-group-item"><strong>Sistema Operativo:</strong> ${detalle.os}</li>
                                <li class="list-group-item"><strong>Licencia Microsoft 365:</strong> ${opcionesComputador[detalle.licencia_microsoft]}</li>
                                <li class="list-group-item"><strong>Tiene Monitor:</strong> ${opcionesComputador[detalle.tiene_monitor]}</li>
                                <li class="list-group-item"><strong>Cargador:</strong> ${detalle.tipo_cargador}</li>`;
            if (detalle.tiene_monitor == 1 && detalle.monitor) {
                detalleHtml += `
                                        <li class="list-group-item"><strong>Monitor Asociado (Código - Activo Fijo):</strong> ${detalle.monitor.cod_equipo}</li>
                                        <li class="list-group-item"><strong>Tamaño:</strong> ${detalle.monitor.pulgadas} pulgadas</li>`;
            }
            detalleHtml += `</ul>
                        </div>
                    </div>
                </div>
            </div>`;
            break;

        case 'Monitor':
            detalleHtml += `
                <div class="col-md-6">
                    <div class="collapse multi-collapse" id="multiCollapseDetallesEquipo">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title fw-bold">Características Específicas</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <li class="list-group-item"><strong>Tamaño:</strong> ${detalle.tamanio_pulgadas}</li>
                                    <li class="list-group-item"><strong>Asignado Computador (Si/No):</strong> ${opcionesMonitor[detalle.asignado]}</li>
                                    <li class="list-group-item"><strong>Nombre Equipo Asignado:</strong> ${detalle.nombre_equipo_asignado || 'Sin equipo asignado'} </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>`;
            break;

        case 'Impresora':
            detalleHtml += `
                <div class="col-md-6">
                    <div class="collapse multi-collapse" id="multiCollapseDetallesEquipo">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title fw-bold">Características Específicas</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <li class="list-group-item"><strong>Tecnología:</strong> ${detalle.tecnologia}</li>
                                    <li class="list-group-item"><strong>Conectividad:</strong> ${detalle.conexion}</li>
                                </ul>
                        </div>
                    </div>
                </div>
            </div>`;
            break;

        case 'Tablet':
            detalleHtml += `
                <div class="col-md-6">
                    <div class="collapse multi-collapse" id="multiCollapseDetallesEquipo">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title fw-bold">Características Específicas</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group">
                                    <li class="list-group-item"><strong>Procesador:</strong> ${detalle.procesador}</li>
                                    <li class="list-group-item"><strong>RAM:</strong> ${detalle.ram} GB</li>
                                    <li class="list-group-item"><strong>Almacenamiento:</strong> ${detalle.rom} GB</li>
                                <li class="list-group-item"><strong>Sistema Operativo:</strong> ${detalle.os}</li>
                                <li class="list-group-item"><strong>Versión OS:</strong> ${detalle.version_os}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>`;
            break;

        default:
            detalleHtml += `<div class="alert alert-warning">No hay información específica para este tipo de equipo.</div>`;
    }

    document.getElementById('contenido-detalle').innerHTML = detalleHtml;
}

document.addEventListener('DOMContentLoaded', () => {
    const equipoId = new URLSearchParams(window.location.search).get('equipo_id');

    if (equipoId) {
        // Cargar el historial de mantenimientos
        fetch(`../../controllers/mantenimiento.php?op=listar_mmto_equipo&equipo_id=${equipoId}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    const historialContainer = document.getElementById('historialMmtos');
                    historialContainer.innerHTML = '<h3>Historial de Mantenimientos</h3>'; // Limpiar el contenedor

                    data.forEach((mmto, index) => {
                        const isFirst = index === 0 ? 'show' : ''; // Mostrar el primer mantenimiento expandido
                        const mmtoHtml = `
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading${mmto.mmto_id}">
                                    <button class="accordion-button ${isFirst ? '' : 'collapsed'}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${mmto.mmto_id}" aria-expanded="${isFirst ? 'true' : 'false'}" aria-controls="collapse${mmto.mmto_id}">
                                        Mantenimiento Realizado (Fecha): ${mmto.fecha_realizado}
                                    </button>
                                </h2>
                                <div id="collapse${mmto.mmto_id}" class="accordion-collapse collapse ${isFirst}" aria-labelledby="heading${mmto.mmto_id}" data-bs-parent="#historialMmtos">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <li class="list-group-item"><strong>Tipo de Mantenimiento:</strong> ${mmto.tipo}</li>
                                            <li class="list-group-item"><strong>Descripción:</strong> ${mmto.descripcion}</li>
                                            <li class="list-group-item"><strong>Acciones Realizadas:</strong> ${mmto.acciones_realizadas}</li>
                                            <li class="list-group-item"><strong>Observaciones:</strong> ${mmto.observaciones}</li>
                                            <li class="list-group-item"><strong>Responsable:</strong> ${mmto.tecnico}</li>
                                            <li class="list-group-item"><strong>Revisado y Recibido por:</strong> ${mmto.revisado_por}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        `;
                        historialContainer.innerHTML += mmtoHtml;
                    });
                } else {
                    document.getElementById('historialMmtos').innerHTML = '<h3>Historial de Mantenimientos</h3><p>No se encontraron mantenimientos registrados para este equipo. Por favor, registre un mantenimiento para habilitar esta sección.</p>';
                }
            })
            .catch(error => {
                Swal.fire('Error', error.message || 'Error al cargar el historial de mantenimientos', 'error');
                document.getElementById('historialMmtos').innerHTML = '<h3>Historial de Mantenimientos</h3> <p>Error al cargar el historial de mantenimientos.</p>';
            });
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se proporcionó un ID de equipo.'
        });
    }
});

function editarEquipo(equipo_id) {
    window.location.href = '../../view/EditarEquipo/?equipo_id=' + equipo_id;
}

function regresarListado() {
    window.location.href = '../../view/ListarEquipo/';
}

document.addEventListener('DOMContentLoaded', () => {
    const btnRegresar = document.getElementById('btnRegresar');
    if (btnRegresar) {
        btnRegresar.addEventListener('click', regresarListado);
    }
});

function mostrarInformacionCompleta() {
    const equipoId = new URLSearchParams(window.location.search).get('equipo_id');

    if (equipoId) {
        fetch(`../../controllers/equipo.php?op=listar_detalle&equipo_id=${equipoId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    const equipo = data.data;
                    const detalle = equipo.detalle;

                    const opcionesComputador = { 0: 'No', 1: 'Sí', 2: 'No Aplica', null: 'No' };
                    const opcionesMonitor = { 0: 'No', 1: 'Sí', 2: 'No Aplica', null: 'No' };
                    const opcionesEstado = {
                        'Activo': 'Activo',
                        'Inactivo': 'Inactivo',
                        'Baja': 'Dado de Baja',
                        null: 'No'
                    };

                    let detalleHtml = '';

                    switch (equipo.nombre_equipo) {
                        case 'Computador':
                            detalleHtml += `
                            <ul class="list-group">
                                <li class="list-group-item"><strong>Nombre Equipo:</strong> ${detalle.nombre_pc}</li>
                                <li class="list-group-item"><strong>Tipo:</strong> ${detalle.tipo_computador}</li>
                                <li class="list-group-item"><strong>Procesador:</strong> ${detalle.procesador}</li>
                                <li class="list-group-item"><strong>RAM:</strong> ${detalle.ram} GB</li>
                                <li class="list-group-item"><strong>Disco:</strong> ${detalle.disco}</li>
                                <li class="list-group-item"><strong>Capacidad Disco:</strong> ${detalle.capacidad_disco} GB</li>
                                <li class="list-group-item"><strong>Sistema Operativo:</strong> ${detalle.os}</li>
                                <li class="list-group-item"><strong>Licencia Microsoft 365:</strong> ${opcionesComputador[detalle.licencia_microsoft]}</li>
                                <li class="list-group-item"><strong>Tiene Monitor:</strong> ${opcionesComputador[detalle.tiene_monitor]}</li>
                                <li class="list-group-item"><strong>Cargador:</strong> ${detalle.tipo_cargador}</li>`;
                            if (detalle.tiene_monitor == 1 && detalle.monitor) {
                                detalleHtml += `
                                <li class="list-group-item"><strong>Monitor Asociado (Código - Activo Fijo):</strong> ${detalle.monitor.cod_equipo}</li>
                                <li class="list-group-item"><strong>Tamaño:</strong> ${detalle.monitor.pulgadas} pulgadas</li>`;
                            }
                            detalleHtml += `</ul>`;
                            break;

                        case 'Monitor':
                            detalleHtml += `
                            <ul class="list-group">
                                <li class="list-group-item"><strong>Tamaño:</strong> ${detalle.tamanio_pulgadas}</li>
                                <li class="list-group-item"><strong>Asignado Computador (Si/No):</strong> ${opcionesMonitor[detalle.asignado]}</li>
                                <li class="list-group-item"><strong>Nombre Equipo Asignado:</strong> ${detalle.nombre_equipo_asignado || 'Sin equipo asignado'} </li>
                            </ul>`;
                            break;

                        case 'Impresora':
                            detalleHtml += `
                            <ul class="list-group">
                                <li class="list-group-item"><strong>Tecnología:</strong> ${detalle.tecnologia}</li>
                                <li class="list-group-item"><strong>Conectividad:</strong> ${detalle.conexion}</li>
                            </ul>`;
                            break;

                        case 'Tablet':
                            detalleHtml += `
                            <ul class="list-group">
                                <li class="list-group-item"><strong>Procesador:</strong> ${detalle.procesador}</li>
                                <li class="list-group-item"><strong>RAM:</strong> ${detalle.ram} GB</li>
                                <li class="list-group-item"><strong>Almacenamiento:</strong> ${detalle.rom} GB</li>
                                <li class="list-group-item"><strong>Sistema Operativo:</strong> ${detalle.os}</li>
                                <li class="list-group-item"><strong>Versión OS:</strong> ${detalle.version_os}</li>
                            </ul>`;
                            break;
                    }

                    const contenidoHtml = `
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Información General</h5>
                                <ul class="list-group mb-4">
                                    <li class="list-group-item"><strong>Tipo:</strong> ${equipo.nombre_equipo}</li>
                                    <li class="list-group-item"><strong>Marca:</strong> ${equipo.marca_equipo}</li>
                                    <li class="list-group-item"><strong>Modelo:</strong> ${equipo.modelo_equipo}</li>
                                    <li class="list-group-item"><strong>Serial:</strong> ${equipo.serial_equipo}</li>
                                    <li class="list-group-item"><strong>Sede:</strong> ${equipo.nombre_sede}</li>
                                    <li class="list-group-item"><strong>Estado:</strong> ${opcionesEstado[equipo.estado]}</li>
                                    <li class="list-group-item"><strong>Responsable:</strong> ${equipo.responsable}</li>
                                    <li class="list-group-item"><strong>Fecha Registro:</strong> ${new Date(equipo.fecha_registro).toLocaleDateString('es-CO')}</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5>Características Específicas</h5>
                                ${detalleHtml}
                            </div>
                        </div>`;

                    // Mostrar en el modal
                    document.getElementById('contenidoModalEquipo').innerHTML = contenidoHtml;

                    // Mostrar modal con Bootstrap 5
                    const modal = new bootstrap.Modal(document.getElementById('modalInformacionEquipo'));
                    modal.show();

                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message });
                }
            })
            .catch(error => Swal.fire({ icon: 'error', title: 'Error', text: error.message || 'No se pudo obtener la información del equipo.' }));
    } else {
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se proporcionó un ID de equipo.' });
    }
}