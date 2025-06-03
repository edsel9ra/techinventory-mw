// Obtener el ID del equipo desde la URL
const urlParams = new URLSearchParams(window.location.search);
const equipoId = urlParams.get('equipo_id');

// Cargar detalle del equipo
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

    const fechaRegistro = new Date(equipo.fecha_registro);
    const fechaFormateadaRegistro = fechaRegistro.toLocaleDateString('es-CO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    const fechaBaja = new Date(equipo.fecha_baja);
    const fechaFormateadaBaja = fechaBaja.toLocaleDateString('es-CO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    let detalleHtml = `
            <h2 class="col d-flex align-items-center justify-content-center mb-3">Hoja de Vida del Equipo: ${equipo.cod_equipo}</h2>
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
                                <ul class="list-group">
                                    <li class="list-group-item card-text"><strong>Tipo:</strong> ${equipo.nombre_equipo}</li>
                                    <li class="list-group-item card-text"><strong>Marca:</strong> ${equipo.marca_equipo}</li>
                                    <li class="list-group-item card-text"><strong>Modelo:</strong> ${equipo.modelo_equipo}</li>
                                    <li class="list-group-item card-text"><strong>Serial:</strong> ${equipo.serial_equipo}</li>
                                    <li class="list-group-item card-text"><strong>Sede:</strong> ${equipo.nombre_sede}</li>
                                    <li class="list-group-item card-text"><strong>Estado:</strong> ${opcionesEstado[equipo.estado]}</li>
                                    <li class="list-group-item card-text"><strong>Responsable:</strong> ${equipo.responsable}</li>
                                    <li class="list-group-item card-text"><strong>Fecha Registro:</strong> ${fechaFormateadaRegistro}</li>
                                    <li class="list-group-item card-text"><strong>Fecha Baja:</strong> ${equipo.fecha_baja ? fechaFormateadaBaja : 'N/A'}</li>
                                    <li class="list-group-item card-text"><strong>Motivo Baja:</strong> ${equipo.motivo_baja === 'Otro' ? (equipo.otro_motivo_baja || 'Otro') : equipo.motivo_baja || 'N/A'}</li>
                                    <li class="list-group-item card-text"><strong>Concepto Técnico:</strong> ${equipo.concepto_tecnico_baja || 'N/A'}</li>
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

    detalleHtml += `
    <div/>
    <!--<hr>-->
    <h4 class="mt-4 mb-3">Galería de Imágenes</h4>`;

    //Formulario de subida de imágenes
    if (rol_id != 2 && equipo.estado !== 'Baja') {
        detalleHtml += `   
    <form id="formSubirImagen" enctype="multipart/form-data" class="mb-4 p-3 border rounded shadow-sm bg-light">
        <input type="hidden" name="equipo_id" value="${equipo.equipo_id}">
        <input type="hidden" name="cod_equipo" value="${equipo.cod_equipo}">
        
        <div class="row g-3 align-items-center">
            <!-- Selector de imagen -->
            <div class="col-md-5">
                <label class="form-label fw-bold">Seleccionar imagen</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-regular fa-image"></i></span>
                    <input type="file" name="imagenes[]" id="inputImagenEquipo" multiple class="form-control" accept="image/*" required>
                </div>
                <small class="form-text text-muted">Selecciona las imágenes que desea subir.</small>
            </div>
            <!-- Campo de descripción -->
            <div class="col-md-5">
                <label class="form-label fw-bold">Descripción (opcional)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-clipboard-list"></i></span>
                    <input type="text" name="descripcion" class="form-control" placeholder="Ej. Vista frontal, etc">
                </div>
                <small class="form-text text-muted">La descripción se aplica a todas las imágenes que subirás.</small>
            </div>
            <!-- Botón de subir -->
            <div class="col-md-2 d-grid">
                <label class="form-label invisible">Botón</label>
                <button type="submit" class="btn btn-success">
                    <i class="fa-solid fa-upload"></i> Subir
                </button>
            </div>
        </div>
        <!-- Previsualización de imágenes -->
        <div class="row mt-3" id="previewImagenEquipo" style="display:none;">
            <img src="" alt="Previsualización" class="img-fluid rounded shadow-sm">
        </div>
    </form>`;
    }

    detalleHtml += `
    <!-- Galería de imágenes -->
    <div class="row el-element-overlay" id="galeriaEquipo"></div>`;


    document.getElementById('contenido-detalle').innerHTML = detalleHtml;
    lightbox.option({
        'resizeDuration': 200,
        'wrapAround': true,
        'albumLabel': "Imagen %1 de %2",
        'fadeDuration': 300,
        'imageFadeDuration': 300
    });
    
    cargarGaleriaEquipo(equipoId);
    configurarSubidaImagen(equipoId);
}

// Cargar historial de mantenimientos
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
                                <h2 class="accordion-header" id="heading${mmto.mantenimiento_id}">
                                    <button class="accordion-button ${isFirst ? '' : 'collapsed'}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${mmto.mantenimiento_id}" aria-expanded="${isFirst ? 'true' : 'false'}" aria-controls="collapse${mmto.mantenimiento_id}">
                                        Mantenimiento Realizado (Fecha): ${mmto.fecha_realizado}
                                    </button>
                                </h2>
                                <div id="collapse${mmto.mantenimiento_id}" class="accordion-collapse collapse ${isFirst}" aria-labelledby="heading${mmto.mantenimiento_id}" data-bs-parent="#historialMmtos">
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

// Editar equipo
function editarEquipo(equipo_id) {
    window.location.href = '../../view/EditarEquipo/?equipo_id=' + equipo_id;
}

// Regresar a listado
function regresarListado() {
    window.location.href = '../../view/ListarEquipo/';
}

// Evento de regresar
document.addEventListener('DOMContentLoaded', () => {
    const btnRegresar = document.getElementById('btnRegresar');
    if (btnRegresar) {
        btnRegresar.addEventListener('click', regresarListado);
    }
});

// Mostrar información completa
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
                                    <li class="list-group-item"><strong>Fecha Baja:</strong> ${equipo.fecha_baja ? new Date(equipo.fecha_baja).toLocaleDateString('es-CO') : 'N/A'}</li>
                                    <li class="list-group-item"><strong>Motivo Baja:</strong> ${equipo.motivo_baja === 'Otro' ? (equipo.otro_motivo_baja || 'Otro') : equipo.motivo_baja || 'N/A'}</li>
                                    <li class="list-group-item"><strong>Concepto Técnico:</strong> ${equipo.concepto_tecnico_baja || 'N/A'}</li>
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

// Cargar galería de imágenes
function cargarGaleriaEquipo(equipoId) {
    const baseUrl = window.location.origin + '/techinventory/';
    fetch(`${baseUrl}controllers/equipo.php?op=listar_imagenes_equipo&equipo_id=${equipoId}`)
        .then(res => res.json())
        .then(data => {
            const galeria = document.getElementById('galeriaEquipo');
            galeria.innerHTML = '';

            if (data.status && Array.isArray(data.data) && data.data.length > 0) {
                data.data.forEach(img => {
                    const rutaAbsoluta = baseUrl + img.ruta_imagen.replace(/^\/?/, '');
                    const col = document.createElement('div');
                    col.className = 'col-md-3 mb-4';
                    col.innerHTML = `
                        <div class="galeria-imagen">
                            <img src="${rutaAbsoluta}" alt="Imagen equipo">
                            <div class="galeria-overlay">
                                <a class="btn btn-sm btn-primary" href="${rutaAbsoluta}" data-lightbox="galeriaEquipo" title="${img.descripcion || ''}">
                                    <i class="fa-solid fa-magnifying-glass-plus"></i>
                                </a>
                                <button class="btn btn-sm btn-danger btn-eliminar-imagen" data-id="${img.imagen_id}" title="Eliminar">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="galeria-titulo text-muted text-center" style="font-size: 0.85rem;">${img.descripcion || ''}</div>
                    `;

                    galeria.appendChild(col);
                });

                // Configurar botones de eliminación
                const botonesEliminar = document.querySelectorAll('.btn-eliminar-imagen');
                botonesEliminar.forEach(boton => {
                    boton.addEventListener('click', function (e) {
                        e.preventDefault();
                        const imagenId = this.getAttribute('data-id');
                        Swal.fire({
                            title: '¿Estás seguro?',
                            text: 'Esta acción no se puede deshacer.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                fetch(`../../controllers/equipo.php?op=eliminar_imagen_equipo&imagen_id=${imagenId}`)
                                    .then(res => res.json())
                                    .then(respuesta => {
                                        if (respuesta.status) {
                                            Swal.fire('✅ Eliminado', respuesta.message, 'success');
                                            cargarGaleriaEquipo(equipoId);
                                        } else {
                                            Swal.fire('❌ Error', respuesta.message, 'error');
                                        }
                                    })
                                    .catch(err => {
                                        Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'Error al eliminar la imagen.' });
                                    });
                            }
                        });
                    });
                });
            } else {
                galeria.innerHTML = '<div class="col-12 text-center text-muted">No hay imágenes cargadas para este equipo. Seleccione las imágenes y subalas desde el botón "Subir". Los equipos que estan en estado "Baja" se deshabilita la función.</div>';
            }
        })
        .catch(err => {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error al cargar la galería.' });
            document.getElementById('galeriaEquipo').innerHTML = '<div class="col-12 text-center text-danger">Error al cargar la galería.</div>';
        });
}

// Configurar subida de imágenes
function configurarSubidaImagen(equipoId) {
    const form = document.getElementById('formSubirImagen');
    const inputImagen = document.getElementById('inputImagenEquipo');
    const previewContainer = document.getElementById('previewImagenEquipo');

    if (!form || !inputImagen || !previewContainer) return;

    // Mostrar previsualización múltiple
    inputImagen.addEventListener('change', function (event) {
        const files = Array.from(event.target.files);
        const maxSizeMB = 5;

        previewContainer.innerHTML = ''; // limpiar previews anteriores

        files.forEach(file => {
            if (!file.type.startsWith('image/')) return;

            if (file.size > maxSizeMB * 1024 * 1024) {
                Swal.fire('Archivo demasiado grande', `Máx. permitido: ${maxSizeMB}MB`, 'warning');
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                const col = document.createElement('div');
                col.className = 'col-md-3 mb-2';
                col.innerHTML = `
                    <img src="${e.target.result}" class="img-fluid rounded shadow-sm" style="width: 100%; height: 150px; object-fit: cover;"/>
                `;
                previewContainer.appendChild(col);
            };
            reader.readAsDataURL(file);
        });

        previewContainer.style.display = files.length ? 'flex' : 'none';
    });

    // Submit
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(form);

        fetch('../../controllers/equipo.php?op=subir_imagen_equipo', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    Swal.fire('✅ Completo', data.message, 'success');
                    form.reset();
                    previewContainer.innerHTML = '';
                    previewContainer.style.display = 'none';
                    cargarGaleriaEquipo(equipoId); // recarga galería
                } else {
                    Swal.fire('❌ Error', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('❌ Error', error.message || 'No se pudo subir la imagen.', 'error');
            });
    });
}
