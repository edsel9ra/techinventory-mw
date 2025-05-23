document.addEventListener('DOMContentLoaded', function () {
    const selectAnio = document.getElementById('filtroAnio');
    const anioActual = new Date().getFullYear();
    
    // Generar opciones para los últimos 5 años
    for(let i = 0; i < 5; i++) {
        const anio = anioActual - i;
        const option = document.createElement('option');
        option.value = anio;
        option.textContent = anio;
        if (anio === anioActual) option.selected = true;
        selectAnio.appendChild(option);
    }

    contarEquiposTotal();
    contarEquiposActivos();
    contarEquiposInactivos();
    contarEquiposBaja();
    graficarEquiposPorTipoActivos();
    graficarEquiposPorTipoInactivos();
    graficarEquiposPorTipoBaja();
    graficarEquiposPorSedeActivos();
    contarMntosTotal();
    contarMntosPreventivos();
    contarMntosCorrectivos();
    graficarMntosPorMes(anioActual);

    selectAnio.addEventListener('change', () => {
        const anioSeleccionado = selectAnio.value;
        graficarMntosPorMes(anioSeleccionado);
    });
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

function graficarMntosPorMes(anio) {
    fetch(`../controllers/mantenimiento.php?op=mantenimientos_por_mes&anio=${anio}`)
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                const mesesNombres = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

                const datosMeses = new Array(12).fill(0);
                data.data.forEach(item => {
                    datosMeses[item.mes - 1] = item.total_mantenimientos;
                });

                // Destruir gráfico anterior si ya existe
                if (window.graficoMntosPorMes instanceof Chart) {
                    window.graficoMntosPorMes.destroy();
                }

                const ctx = document.getElementById('graficoMntosPorMes').getContext('2d');
                window.graficoMntosPorMes = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: mesesNombres,
                        datasets: [{
                            label: `Mantenimientos por mes (${anio})`,
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