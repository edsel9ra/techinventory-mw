document.addEventListener('DOMContentLoaded', function () {
    const selectAnio = document.getElementById('filtroAnio');
    const selectMes = document.getElementById('filtroMes');
    const tipoSelect = document.getElementById('filtroTipoMnto');
    const filtrosContainer = document.getElementById('filtrosMntos');
    const anioActual = new Date().getFullYear();
    const mesActual = new Date().getMonth() + 1;

    // Generar opciones para los últimos 5 años
    if (rol_id === 1 || rol_id === 2) {
        for (let i = 0; i < 5; i++) {
            const anio = anioActual - i;
            const option = document.createElement('option');
            option.value = anio;
            option.textContent = anio;
            if (anio === anioActual) option.selected = true;
            selectAnio.appendChild(option);
        }

        // Eventos de cambio
        selectAnio.addEventListener('change', actualizarGrafico);
        tipoSelect.addEventListener('change', actualizarGrafico);

        // Graficar inicialmente
        graficarMntosPorMes(anioActual, tipoSelect.value);
    } else {
        // Ocultar filtros si no tiene permisos
        if (filtrosContainer) {
            filtrosContainer.style.display = 'none';
        }
    }

    if (rol_id === 1) {
        // Generar opciones para meses
        const mesesNombres = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        for (let i = 1; i <= 12; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = mesesNombres[i - 1];
            if (i === mesActual) option.selected = true;
            selectMes.appendChild(option);
        }

        selectMes.addEventListener('change', actualizarGraficoTecnico);
        selectAnio.addEventListener('change', actualizarGraficoTecnico);
        graficoMntosPorTecnico(mesActual, anioActual);
    }

    if (rol_id === 1 || rol_id === 2) {
        contarMntosTotal();
        contarMntosPreventivos();
        contarMntosCorrectivos();
    }

    if (rol_id === 1 || rol_id === 3) {
        contarEquiposTotal();
        contarEquiposActivos();
        contarEquiposInactivos();
        contarEquiposBaja();
        graficarEquiposPorTipoActivos();
        graficarEquiposPorTipoInactivos();
        graficarEquiposPorTipoBaja();
        graficarEquiposPorSedeActivos();
    }

    function actualizarGrafico() {
        const anio = selectAnio.value;
        const tipo = tipoSelect.value;
        graficarMntosPorMes(anio, tipo);
    }

    function actualizarGraficoTecnico() {
        const mes = selectMes.value;
        const anio = selectAnio.value;
        graficoMntosPorTecnico(mes, anio);
    }
});

//Funciones para Equipos
function contarEquiposTotal() {
    fetch('../controllers/equipo.php?op=contar_equipos_total')
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                document.getElementById('totalEquipos').textContent = data.data;
            }
        });
}

function contarEquiposActivos() {
    fetch('../controllers/equipo.php?op=contar_equipos_activos')
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                document.getElementById('totalActivos').textContent = data.data;
            }
        });
}

function contarEquiposInactivos() {
    fetch('../controllers/equipo.php?op=contar_equipos_inactivos')
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                document.getElementById('totalInactivos').textContent = data.data;
            }
        });
}

function contarEquiposBaja() {
    fetch('../controllers/equipo.php?op=contar_equipos_baja')
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                document.getElementById('totalBaja').textContent = data.data;
            }
        });
}

function graficarEquiposPorTipoActivos() {
    fetch('../controllers/equipo.php?op=contar_tipos_equipo_activos')
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                const nombres = data.data.map(item => item.nombre_equipo);
                const totales = data.data.map(item => parseInt(item.total_tipo_equipo_activo, 10));

                const ctx = document.getElementById('graficoEquiposTipoActivos').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: nombres,
                        datasets: [{
                            label: 'Equipos Activos',
                            data: totales,
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.6)',
                                'rgba(255, 206, 86, 0.6)',
                                'rgba(75, 192, 192, 0.6)',
                                'rgba(255, 99, 132, 0.6)'
                            ],
                            borderColor: [
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 99, 132, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                precision: 0
                            }
                        }
                    }
                });
            }
        });
}

function graficarEquiposPorTipoInactivos() {
    fetch('../controllers/equipo.php?op=contar_tipos_equipo_inactivos')
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                const nombres = data.data.map(item => item.nombre_equipo);
                const totales = data.data.map(item => parseInt(item.total_tipo_equipo_inactivo, 10));

                const ctx = document.getElementById('graficoEquiposTipoInactivos').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: nombres,
                        datasets: [{
                            label: 'Equipos Inactivos',
                            data: totales,
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.6)',
                                'rgba(255, 206, 86, 0.6)',
                                'rgba(75, 192, 192, 0.6)',
                                'rgba(255, 99, 132, 0.6)'
                            ],
                            borderColor: [
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 99, 132, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                precision: 0
                            }
                        }
                    }
                });
            }
        })
}

function graficarEquiposPorTipoBaja() {
    fetch('../controllers/equipo.php?op=contar_tipos_equipo_baja')
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                const nombres = data.data.map(item => item.nombre_equipo);
                const totales = data.data.map(item => parseInt(item.total_tipo_equipo_baja, 10));

                const ctx = document.getElementById('graficoEquiposTipoBaja').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: nombres,
                        datasets: [{
                            label: 'Equipos Dados de Baja',
                            data: totales,
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.6)',
                                'rgba(255, 206, 86, 0.6)',
                                'rgba(75, 192, 192, 0.6)',
                                'rgba(255, 99, 132, 0.6)'
                            ],
                            borderColor: [
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 99, 132, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                precision: 0
                            }
                        }
                    }
                });
            }
        })
}

function graficarEquiposPorSedeActivos() {
    fetch('../controllers/equipo.php?op=contar_equipos_sedes')
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                const nombres = data.data.map(item => item.nombre_sede);
                const totales = data.data.map(item => parseInt(item.total_sede_activo, 10));

                const ctx = document.getElementById('graficoEquiposSedeActivos').getContext('2d');
                new Chart(ctx, {
                    type: 'polarArea',
                    data: {
                        labels: nombres,
                        datasets: [{
                            label: 'Equipos Activos',
                            data: totales,
                            backgroundColor: [
                                'rgba(54, 162, 235, 0.6)',
                                'rgba(255, 206, 86, 0.6)',
                                'rgba(75, 192, 192, 0.6)',
                                'rgba(255, 99, 132, 0.6)'
                            ],
                            borderColor: [
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(255, 99, 132, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                precision: 0
                            }
                        }
                    }
                });
            }
        })
}

//Funciones para Mantenimientos
function contarMntosTotal() {
    fetch('../controllers/mantenimiento.php?op=contar_mantenimientos_total')
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                document.getElementById('totalMntos').textContent = data.data;
            }
        })
}

function contarMntosPreventivos() {
    fetch('../controllers/mantenimiento.php?op=contar_mantenimientos_preventivos')
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                document.getElementById('totalMntosPreventivos').textContent = data.data;
            }
        })
}

function contarMntosCorrectivos() {
    fetch('../controllers/mantenimiento.php?op=contar_mantenimientos_correctivos')
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                document.getElementById('totalMntosCorrectivos').textContent = data.data;
            }
        })
}

function graficarMntosPorMes(anio, tipo = '') {
    const url = `../controllers/mantenimiento.php?op=mantenimientos_por_mes&anio=${anio}&tipo=${tipo}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                const mesesNombres = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

                const datosMeses = new Array(12).fill(0);
                let total = 0;

                data.data.forEach(item => {
                    const index = item.mes - 1;
                    datosMeses[index] = item.total_mantenimientos;
                    total += parseInt(item.total_mantenimientos);
                });

                // Mostrar total acumulado
                const totalText = tipo
                    ? `Total de mantenimientos ${tipo.toLowerCase()}s en ${anio}: ${total}`
                    : `Total de mantenimientos en ${anio}: ${total}`;
                document.getElementById('totalMantenimientos').textContent = totalText;

                // Destruir gráfico anterior si existe
                if (window.graficoMntosPorMes instanceof Chart) {
                    window.graficoMntosPorMes.destroy();
                }

                const ctx = document.getElementById('graficoMntosPorMes').getContext('2d');
                window.graficoMntosPorMes = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: mesesNombres,
                        datasets: [{
                            label: `Mantenimientos por mes (${anio}) ${tipo ? '- ' + tipo : ''}`,
                            data: datosMeses,
                            borderWidth: 1,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            fill: true,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                precision: 0
                            }
                        }
                    }
                });
            }
        });
}

function graficoMntosPorTecnico(mes, anio) {
    fetch(`../controllers/mantenimiento.php?op=mantenimientos_por_tecnico&mes=${mes}&anio=${anio}`)
        .then(res => res.json())
        .then(data => {
            if (!data.status) return;

            const raw = data.data;
            const tecnicos = [...new Set(raw.map(r => r.tecnico))];
            const preventivos = tecnicos.map(tecnico => {
                const item = raw.find(r => r.tecnico === tecnico && r.tipo === 'Preventivo');
                return item ? item.total : 0;
            });
            const correctivos = tecnicos.map(tecnico => {
                const item = raw.find(r => r.tecnico === tecnico && r.tipo === 'Correctivo');
                return item ? item.total : 0;
            });

            const ctx = document.getElementById('graficoMntosPorTecnico').getContext('2d');
            if (window.mntoChart) window.mntoChart.destroy();

            const mesNombre = document.querySelector(`#filtroMes option[value="${mes}"]`)?.textContent || `Mes ${mes}`;
            window.mntoChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: tecnicos,
                    datasets: [
                        {
                            label: 'Preventivos',
                            data: preventivos,
                            backgroundColor: 'rgba(75, 192, 192, 0.7)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Correctivos',
                            data: correctivos,
                            backgroundColor: 'rgba(255, 99, 132, 0.7)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: `Mantenimientos por técnico - ${mesNombre} ${anio}`
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        });
}