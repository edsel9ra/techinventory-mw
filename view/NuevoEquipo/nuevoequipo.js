let sedesCache = [];
let tiposCache = [];

document.addEventListener('DOMContentLoaded', () => {
    cargarSedes(() => {
        comboSedes('sede');

        const sedeSelect = document.getElementById('sede');
        const inputCodigoEquipo = document.getElementById('af');
        const btnGuardar = document.getElementById('btnGuardar');
        btnGuardar.disabled = true;

        sedeSelect.addEventListener("change", () => {
            const sedeId = sedeSelect.value;

            if (sedeId) {
                generarCodigoEquipo(sedeId)
                    .then(codigo => {
                        if (codigo) {
                            inputCodigoEquipo.value = codigo;
                            btnGuardar.disabled = false;
                        } else {
                            inputCodigoEquipo.value = "";
                            btnGuardar.disabled = true;
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo generar el código del equipo.'
                            });
                        }
                    })
                    .catch(err => {
                        inputCodigoEquipo.value = "";
                        btnGuardar.disabled = true;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error al generar el código del equipo.'
                        });
                    });
            } else {
                inputCodigoEquipo.value = "";
                btnGuardar.disabled = true;
            }
        });
    });

    cargarTiposEquipos(() => {
        comboTipos('tipo_equipo');
    });

    const contenedorDetalles = document.getElementById("campos_detalles");

    document.getElementById("tipo_equipo").addEventListener("change", function () {
        const tipo_equipo = this.value;
        contenedorDetalles.innerHTML = "";

        switch (tipo_equipo) {
            case "1":
                contenedorDetalles.innerHTML = `
                    <h4 class="mb-3">Detalles del Computador</h4>
                    <div class="row g-3 align-items-center">
                        <div class="col">
                            <div class="input-group mb-3">
                                <label class="input-group-text fw-bold" for="nombre_pc">Nombre del equipo</label>
                                <input type="text" class="form-control" name="nombre_pc" id="nombre_pc" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group mb-3">
                                <label class="input-group-text fw-bold" for="tipo_computador">Tipo de Computador</label>
                                <select class="form-select" id="tipo_computador" name="tipo_computador" required>
                                    <option value="" disabled selected>Seleccione</option>
                                    <option value="Desktop">Desktop</option>
                                    <option value="Portátil">Portátil</option>
                                    <option value="AIO">Todo en Uno</option>
                                    <option value="HioScreen">HioScreen</option>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group mb-3">
                                <label class="input-group-text fw-bold" for="procesador">Procesador</label>
                                <input type="text" class="form-control" name="procesador" id="procesador" required>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 align-items-center">
                        <div class="col">
                            <div class="input-group mb-1">
                                <label class="input-group-text fw-bold" for="ram">RAM</label>
                                <input type="number" class="form-control" name="ram" id="ram" required>
                                <span class="input-group-text">GB</span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group mb-3">
                                <label class="input-group-text fw-bold" for="disco">Disco</label>
                                <select class="form-select" name="disco" id="disco" required>
                                    <option value="" disabled selected>Seleccione</option>
                                    <option value="HDD">HDD</option>
                                    <option value="SSD SATA">SSD SATA</option>
                                    <option value="SSD SATA M.2">SSD SATA M.2</option>
                                    <option value="SSD NVMe M.2">SSD NVMe M.2</option>
                                    <option value="SSHD">SSHD (Estado Solido Híbrido)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group mb-3">
                                <label class="input-group-text fw-bold" for="capacidad_disco">Capacidad de Disco</label>
                                <input type="number" class="form-control" name="capacidad_disco" id="capacidad_disco" required>
                                <span class="input-group-text">GB</span>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 align-items-center">
                        <div class="col">
                            <div class="input-group mb-3">
                                <label class="input-group-text fw-bold" for="os">Sistema Operativo</label>
                                <input type="text" class="form-control" name="os" id="os" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group mb-3">
                                <label class="input-group-text fw-bold" for="licencia_microsoft">Licencia Microsoft 365</label>
                                <select class="form-select" name="licencia_microsoft" id="licencia_microsoft" required>
                                    <option value="" disabled selected>Seleccione</option>
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group mb-3">
                                <label class="input-group-text fw-bold" for="tiene_monitor">¿Tiene Monitor?</label>
                                <select class="form-select" id="tiene_monitor" name="tiene_monitor" required>
                                    <option value="" disabled selected>Seleccione</option>
                                    <option value="1">Sí</option>
                                    <option value="0">No</option>
                                    <option value="2">No Aplica</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 align-items-center">
                        <div class="col" id="monitor_select">
                            <div class="input-group mb-3">
                                <label class="input-group-text fw-bold" for="monitor">Monitor</label>
                                <select class="form-select" id="monitor_id" name="monitor_id" disabled>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="input-group mb-3">
                                <label class="input-group-text fw-bold" for="tipo_cargador">Tipo de Cargador</label>
                                <select class="form-select" name="tipo_cargador" id="tipo_cargador" required>
                                <option value="" disabled selected>Seleccione</option>
                                <option value="Original">Original</option>
                                <option value="Genérico">Genérico</option>
                                <option value="N/A">No aplica</option>
                            </select>
                            </div>
                        </div>
                    </div>
                `;
                const sedeSelect = document.getElementById("sede");
                const tipoComputadorSelect = document.getElementById("tipo_computador");
                const tieneMonitorSelect = document.getElementById("tiene_monitor");
                const monitorSelect = document.getElementById("monitor_id");

                const containerMonitorSelect = document.getElementById("monitor_select");
                containerMonitorSelect.style.display = "none";

                function ejecutarCallback(callback, resultado) {
                    if (typeof callback === "function") {
                        callback(resultado);
                    }
                }

                function cargarMonitores(sedeId, callback) {
                    fetch(`../../controllers/equipo.php?op=combo_monitor&sede_id=${sedeId}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.status && Array.isArray(data.data)) {
                                monitorSelect.innerHTML = "<option value=''>Seleccionar</option>";
                                data.data.forEach(monitor => {
                                    const option = document.createElement("option");
                                    option.value = monitor.monitor_id;
                                    option.textContent = monitor.cod_equipo;
                                    monitorSelect.appendChild(option);
                                });

                                monitorSelect.disabled = false;
                                containerMonitorSelect.style.display = "block";
                                ejecutarCallback(callback, true);
                            } else {
                                monitorSelect.innerHTML = "<option value=''>Monitores no disponibles</option>";
                                monitorSelect.disabled = true;
                                containerMonitorSelect.style.display = "block";
                                ejecutarCallback(callback, false);
                            }
                        })
                        .catch(err => {
                            monitorSelect.innerHTML = "<option value=''>Error al cargar</option>";
                            monitorSelect.disabled = true;
                            containerMonitorSelect.style.display = "block";
                            ejecutarCallback(callback, false);
                        });
                }

                function mostrarSelectMonitor(callback = () => { }) {
                    const sedeId = sedeSelect.value;
                    const tipoComp = tipoComputadorSelect.value;
                    const tieneMonitor = tieneMonitorSelect.value;

                    if (tipoComp === "Desktop" && tieneMonitor === "1" && sedeId !== "") {
                        cargarMonitores(sedeId, callback);
                    } else {
                        monitorSelect.innerHTML = "<option value=''>Seleccionar</option>";
                        monitorSelect.disabled = true;
                        containerMonitorSelect.style.display = "none";
                        ejecutarCallback(callback, false);
                    }
                }

                sedeSelect.addEventListener("change", mostrarSelectMonitor);
                tipoComputadorSelect.addEventListener("change", mostrarSelectMonitor);
                tieneMonitorSelect.addEventListener("change", mostrarSelectMonitor);
                break;

            case "2":
                contenedorDetalles.innerHTML = `
                <h4 class="mb-3">Detalles del Monitor</h4>
                <div class="row g-3 align-items-center">
                    <div class="col">
                        <div class="input-group mb-1">
                            <label class="input-group-text fw-bold" for="tamanio_pulgadas">Tamaño en pulgadas</label>
                            <input type="number" class="form-control" name="tamanio_pulgadas" id="tamanio_pulgadas" step="0.1" required>
                        </div>
                    </div>
                </div>
                `;
                break;

            case "3":
                contenedorDetalles.innerHTML = `
                <h4 class="mb-3">Detalles de Impresora</h4>
                <div class="row g-3 align-items-center">
                    <div class="col">
                        <div class="input-group mb-3">
                            <label class="input-group-text fw-bold" for="tecnologia">Tecnología</label>
                            <select class="form-select" name="tecnologia" id="tecnologia" required>
                                <option value="" disabled selected>Seleccione</option>
                                <option value="Térmica">Térmica</option>
                                <option value="Inyección">Inyección (Tinta)</option>
                                <option value="Toner">Toner</option>
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group mb-3">
                            <label class="input-group-text fw-bold" for="conexion">Tipo de Conexión</label>
                            <select class="form-select" name="conexion" id="conexion" required>
                                <option value="" disabled selected>Seleccione</option>
                                <option value="USB">USB</option>
                                <option value="LAN">Cable de Red</option>
                                <option value="WiFi">WiFi</option>
                            </select>
                        </div>
                    </div>
                </div>
                `;
                break;

            case "4":
                contenedorDetalles.innerHTML = `
                <h4 class="mb-3">Detalles de la Tablet</h4>
                <div class="row g-3 align-items-center">
                    <div class="col">
                        <div class="input-group mb-3">
                            <label class="input-group-text fw-bold" for="procesador">Procesador</label>
                            <input type="text" class="form-control" name="procesador" id="procesador" required>
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group mb-3">
                            <label class="input-group-text fw-bold" for="ram">RAM</label>
                            <input type="text" class="form-control" name="ram" id="ram" required>
                            <span class="input-group-text">GB</span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group mb-3">
                            <label class="input-group-text fw-bold" for="rom">ROM</label>
                            <input type="text" class="form-control" name="rom" id="rom" required>
                            <span class="input-group-text">GB</span>
                        </div>
                    </div>
                </div>
                <div class="row g-3 align-items-center">
                    <div class="col">
                        <div class="input-group mb-3">
                            <label class="input-group-text fw-bold" for="os">Sistema Operativo</label>
                            <input type="text" class="form-control" name="os" id="os" required>
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group mb-3">
                            <label class="input-group-text fw-bold" for="version_os">Versión del Sistema Operativo</label>
                            <input type="text" class="form-control" name="version_os" id="version_os" required>
                        </div>
                    </div>
                </div>
                `;
                break;

        }
    })

});

//funciones para cargar las sedes
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
                    text: 'Error cargando sedes.'
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

//Funciones para cargar los tipos de equipos
function cargarTiposEquipos(callback) {
    if (tiposCache.length > 0) {
        callback(tiposCache);
    } else {
        fetch('../../controllers/equipo.php?op=combo_tipo_equipo')
            .then(response => response.json())
            .then(data => {
                if (data.status === true && Array.isArray(data.data)) {
                    tiposCache = data.data;
                    callback(tiposCache);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error en formato de tipos de equipos.'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error cargando tipos de equipos.'
                });
            });
    }
}

function comboTipos(selectId, selectedTipoId = null) {
    const select = document.getElementById(selectId);
    if (!select) return;

    select.innerHTML = '<option value="" disabled selected>Seleccione</option>';
    tiposCache.forEach(tipo => {
        const option = document.createElement('option');
        option.value = tipo.tipo_equipo_id;
        option.textContent = tipo.nombre_equipo;
        if (selectedTipoId && tipo.tipo_equipo_id == selectedTipoId) {
            option.selected = true;
        }
        select.appendChild(option);
    });
}


document.getElementById("form_equipo").addEventListener("submit", function (e) {
    e.preventDefault();

    const contenedorDetalles = document.getElementById("campos_detalles");
    const formData = new FormData(this);

    // Obtener los campos dinámicos
    const camposDetalles = document.querySelectorAll('#campos_detalles input, #campos_detalles select, #campos_detalles textarea');
    let detalles = {};
    camposDetalles.forEach(input => {
        if (input.name) {
            detalles[input.name] = input.value;
        }
    });

    formData.append("detalles", JSON.stringify(detalles));

    fetch("../../controllers/equipo.php?op=insert", {
        method: "POST",
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: data.message,
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false
                });
                document.getElementById("form_equipo").reset();
                contenedorDetalles.innerHTML = "";
                document.getElementById("btnGuardar").disabled = true;
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message
                });
            }

        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al enviar el formulario. Por favor, inténtelo nuevamente.' + err
            });
        });
});

document.addEventListener('DOMContentLoaded', () => {
    const btnRegresar = document.getElementById('btnRegresar');
    if (btnRegresar) {
        btnRegresar.addEventListener('click', (event) => {
            event.preventDefault(); // Evitar el comportamiento predeterminado del botón
            // Redirigir a la página de listado de equipos
            window.location.href = `../../view/ListarEquipo/`;
        });
    }
});

async function generarCodigoEquipo(sedeId) {
    try {
        const res = await fetch(`../../controllers/equipo.php?op=generar_codigo_equipo&sede_id=${sedeId}`);
        const data = await res.json();
        if (data.status) {
            return data.data;
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
            return null;
        }
    } catch (err) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error generando el código del equipo.' + err
        });
        return null;
    }
}
