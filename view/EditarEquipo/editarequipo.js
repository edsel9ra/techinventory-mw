let sedesCache = [];

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const equipoId = urlParams.get('equipo_id');

    if (!equipoId) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se proporcionó un ID de equipo.'
        });
        return;
    }

    cargarSedes(() => {
        comboSedes('sede');
    });

    fetch(`../../controllers/equipo.php?op=get_equipo_edit&equipo_id=${equipoId}`)
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                const equipo = data.data;
                const detalles = data.detalles;

                document.getElementById('equipo_id').value = equipo.equipo_id;
                document.getElementById('tipo_equipo_id').value = equipo.tipo_equipo_id;
                document.getElementById('sede').value = equipo.sede_id;
                document.getElementById('estado').value = equipo.estado;
                document.getElementById('responsable').value = equipo.responsable;
                document.getElementById('monitor_id_original').value = detalles.monitor_id || '';

                cargarCamposEspecificos(equipo.nombre_equipo, detalles);

                if (equipo.estado.toLowerCase() === 'baja') {
                    deshabilitarFormMain();
                    deshabilitarDetalles();
                    Swal.fire({
                        icon: 'info',
                        title: 'Equipo dado de baja',
                        text: 'Este equipo está en estado de baja, por lo tanto, no se puede editar.',
                        confirmButtonText: 'Entendido'
                    });
                }


                // Cargar monitores para sede seleccionada
                if (equipo.sede_id) {
                    fetch(`../../controllers/equipo.php?op=combo_monitor_edit&sede_id=${equipo.sede_id}`)
                        .then(response => response.json())
                        .then(data => {
                            const monitorSelect = document.getElementById('monitor_id');
                            if (monitorSelect) {
                                monitorSelect.innerHTML = '<option value="" disabled selected>Seleccione un monitor</option>';
                                data.forEach(monitor => {
                                    const option = document.createElement('option');
                                    option.value = monitor.monitor_id;
                                    option.textContent = `${monitor.cod_equipo}`;
                                    if (detalles.monitor_id == monitor.monitor_id) {
                                        option.selected = true;
                                    }
                                    monitorSelect.appendChild(option);
                                });
                            }
                        })
                        .catch(error => Swal.fire('Error', error.message || 'Error al cargar monitores', 'error'));
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message
                });
            }
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un error al cargar los datos del equipo. ' + error
            });
        });

    document.getElementById('formEditarEquipo').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('../../controllers/equipo.php?op=update', {
            method: 'POST',
            body: formData
        })
            .then(response => response.text())
            .then(data => {
                try {
                    const jsonData = JSON.parse(data);
                    if (jsonData.status) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: jsonData.message,
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        }).then(() => {
                            const equipoId = jsonData.equipo_id;
                            if (equipoId) {
                                window.location.href = `../../view/HojaVida/?equipo_id=${equipoId}`;
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'No se pudo obtener el ID del equipo.'
                                });
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: jsonData.message
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un error inesperado. ' + error
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un error al actualizar el equipo. ' + error
                });
            });
    });

    const btnRegresar = document.getElementById('btnRegresar');
    if (btnRegresar) {
        btnRegresar.addEventListener('click', (event) => {
            event.preventDefault();
            const equipoId = urlParams.get('equipo_id');
            if (equipoId) {
                window.location.href = `../../view/HojaVida/?equipo_id=${equipoId}`;
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se proporcionó un ID de equipo.'
                });
            }
        });
    }

    // Cambiar sede y actualizar monitores disponibles
    document.getElementById('sede').addEventListener('change', (e) => {
        const sede_id = e.target.value;
        const monitorSeleccion = document.getElementById('monitorSeleccion');
        const monitorSelect = document.getElementById('monitor_id');

        monitorSeleccion.style.display = sede_id && e.target.value !== '' ? 'block' : 'none';

        fetch(`../../controllers/equipo.php?op=combo_monitor_edit&sede_id=${sede_id}`)
            .then(response => response.json())
            .then(data => {
                monitorSelect.innerHTML = '<option value="" disabled selected>Seleccione un monitor</option>';
                data.forEach(monitor => {
                    const option = document.createElement('option');
                    option.value = monitor.monitor_id;
                    option.textContent = `${monitor.cod_equipo}`;
                    monitorSelect.appendChild(option);
                });
            })
            .catch(error => Swal.fire('Error', error.message || 'Error al cargar monitores', 'error'));
    });
});

function cargarSedes(callback) {
    if (sedesCache.length > 0) {
        callback(sedesCache);
    } else {
        fetch('../../controllers/sede.php?op=combo')
            .then(response => response.json())
            .then(data => {
                if (data.status === true && Array.isArray(data.data)) {
                    sedesCache = data.data;
                    callback(sedesCache);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error en formato de sedes.'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error cargando sedes. ' + error
                });
            });
    }
}

function comboSedes(selectId, selectedSedeId = null) {
    const select = document.getElementById(selectId);
    if (!select) return;

    select.innerHTML = '<option value="" disabled selected>Seleccione</option>';
    sedesCache.forEach(sede => {
        const option = document.createElement('option');
        option.value = sede.sede_id;
        option.textContent = sede.nombre_sede;
        if (selectedSedeId && sede.sede_id == selectedSedeId) {
            option.selected = true;
        }
        select.appendChild(option);
    });
}

function cargarCamposEspecificos(tipo_equipo, detalles = {}) {
    const detallesContainer = document.getElementById('detallesEquipo');
    detallesContainer.innerHTML = '';

    switch (tipo_equipo) {
        case "Computador":
            detallesContainer.innerHTML = `
                <div class="mb-3">
                    <label for="nombre_pc" class="form-label fw-bold">Nombre Computador:</label>
                    <input type="text" id="nombre_pc" name="detalles[nombre_pc]" class="form-control" value="${detalles.nombre_pc || ''}" required>
                </div>
                <div class="mb-3">
                    <label for="ram" class="form-label fw-bold">RAM:</label>
                    <input type="number" id="ram" name="detalles[ram]" class="form-control" value="${detalles.ram || ''}" required>
                </div>
                <div class="mb-3">
                    <label for="disco" class="form-label fw-bold">Disco:</label>
                    <select id="disco" name="detalles[disco]" class="form-control" required>
                        <option value="HDD" ${detalles.disco === 'HDD' ? 'selected' : ''}>HDD</option>
                        <option value="SSD SATA" ${detalles.disco === 'SSD SATA' ? 'selected' : ''}>SSD SATA</option>
                        <option value="SSD SATA M.2" ${detalles.disco === 'SSD SATA M.2' ? 'selected' : ''}>SSD SATA M.2</option>
                        <option value="SSD NVMe M.2" ${detalles.disco === 'SSD NVMe M.2' ? 'selected' : ''}>SSD NVMe M.2</option>
                        <option value="SSHD" ${detalles.disco === 'SSHD' ? 'selected' : ''}>SSHD</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="capacidad_disco" class="form-label fw-bold">Capacidad Disco:</label>
                    <input type="number" id="capacidad_disco" name="detalles[capacidad_disco]" class="form-control" value="${detalles.capacidad_disco || ''}" required>
                </div>
                <div class="mb-3">
                    <label for="os" class="form-label fw-bold">Sistema Operativo:</label>
                    <input type="text" id="os" name="detalles[os]" class="form-control" value="${detalles.os || ''}" required>
                </div>
                <div class="mb-3">
                    <label for="licencia_microsoft" class="form-label fw-bold">¿Tiene Licencia Microsoft 365?:</label>
                    <select id="licencia_microsoft" name="detalles[licencia_microsoft]" class="form-control" required>
                        <option value="1" ${detalles.licencia_microsoft === 1 ? 'selected' : ''}>Sí</option>
                        <option value="0" ${detalles.licencia_microsoft === 0 ? 'selected' : ''}>No</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="tiene_monitor" class="form-label fw-bold">¿Tiene Monitor?:</label>
                    <select id="tiene_monitor" name="detalles[tiene_monitor]" class="form-control" required>
                        <option value="1" ${detalles.tiene_monitor === 1 ? 'selected' : ''}>Sí</option>
                        <option value="0" ${detalles.tiene_monitor === 0 ? 'selected' : ''}>No</option>
                        <option value="2" ${detalles.tiene_monitor === 2 ? 'selected' : ''}>No Aplica</option>
                    </select>
                </div>
                <div class="mb-3" id="monitorSeleccion" style="display: ${detalles.tiene_monitor == 1 ? 'block' : 'none'};">
                    <div id="spinnerCargaMonitor" style="display: none; margin-top: 10px;">
                        <span>Cargando monitores...</span>
                        <div class="spinner" style="display: inline-block; width: 16px; height: 16px; border: 2px solid #ccc; border-top: 2px solid #333; border-radius: 50%; animation: spin 0.8s linear infinite;"></div>
                    </div>
                    <label for="monitor_id" class="form-label">Monitor Asignado:</label>
                    <select id="monitor_id" name="detalles[monitor_id]" class="form-control"></select>
                </div>
                <div class="mb-3">
                    <label for="tipo_cargador" class="form-label fw-bold">Tipo de Cargador:</label>
                    <select id="tipo_cargador" name="detalles[tipo_cargador]" class="form-control" required>
                        <option value="Original" ${detalles.tipo_cargador === 'Original' ? 'selected' : ''}>Original</option>
                        <option value="Genérico" ${detalles.tipo_cargador === 'Genérico' ? 'selected' : ''}>Genérico</option>
                        <option value="N/A" ${detalles.tipo_cargador === 'N/A' ? 'selected' : ''}>No Aplica</option>
                    </select>
                </div>
            `;

            document.getElementById('tiene_monitor').addEventListener('change', (e) => {
                const monitorSeleccion = document.getElementById('monitorSeleccion');
                monitorSeleccion.style.display = e.target.value == '1' ? 'block' : 'none';
            });

            break;

        case "Impresora":
            detallesContainer.innerHTML = `
                <div class="mb-3">
                    <label for="conexion" class="form-label fw-bold">Conexión:</label>
                    <select id="conexion" name="detalles[conexion]" class="form-control" required>
                        <option value="USB" ${detalles.conexion === 'USB' ? 'selected' : ''}>USB</option>
                        <option value="LAN" ${detalles.conexion === 'LAN' ? 'selected' : ''}>LAN</option>
                        <option value="WiFi" ${detalles.conexion === 'WiFi' ? 'selected' : ''}>WiFi</option>
                    </select>
                </div>
            `;
            break;

        case "Tablet":
            detallesContainer.innerHTML = `
                <div class="mb-3">
                    <label for="os" class="form-label">Sistema Operativo:</label>
                    <input type="text" id="os" name="detalles[os]" class="form-control" value="${detalles.os || ''}" required>
                </div>
                <div class="mb-3">
                    <label for="version_os" class="form-label">Versión OS:</label>
                    <input type="text" id="version_os" name="detalles[version_os]" class="form-control" value="${detalles.version_os || ''}" required>
                </div>
            `;
            break;

        default:
            detallesContainer.innerHTML = '<p class="card-text">No se encontraron detalles específicos para editar este tipo de equipo.</p>';
            break;
    }
}

function deshabilitarFormMain() {
    const form = document.getElementById('formEditarEquipo');
    const elementos = form.querySelectorAll('input, select, textarea, button');
    elementos.forEach(elemento => {
        if (elemento.type !== 'button' && elemento.id !== 'btnRegresar') {
            elemento.disabled = true;
        }
    });
}

function deshabilitarDetalles() {
    const detalles = document.getElementById('detallesEquipo');
    const elementos = detalles.querySelectorAll('input, select, textarea');
    elementos.forEach(elemento => {
        elemento.disabled = true;
    });
}
