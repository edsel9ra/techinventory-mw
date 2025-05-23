document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const equipoId = urlParams.get('equipo_id');

    if (equipoId) {
        // Obtener datos del equipo
        fetch(`../../controllers/equipo.php?op=listar_detalle&equipo_id=${equipoId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    const equipo = data.data;

                    document.getElementById('sede').value = equipo.nombre_sede;
                    document.getElementById('nombre_equipo').value = equipo.nombre_equipo;
                    document.getElementById('cod_equipo').value = equipo.cod_equipo;
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', error.message || 'Hubo un error al cargar los datos del equipo.', 'error');
            });

        // Registrar mantenimiento con confirmación
        document.getElementById('formMantenimiento').addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: '¿Registrar mantenimiento?',
                text: "¿Estás seguro de que deseas registrar este mantenimiento?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, registrar',
                cancelButtonText: 'Revisar formulario'
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData(e.target);

                    fetch('../../controllers/mantenimiento.php?op=insert', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status) {
                                Swal.fire({
                                    title: '¡Éxito!',
                                    text: 'Mantenimiento registrado correctamente.',
                                    icon: 'success',
                                    timer: 2000,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = `../../view/HojaVida/?equipo_id=${equipoId}`;
                                });
                            } else {
                                Swal.fire('Error', 'Error al registrar el mantenimiento: ' + data.message, 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error', error.message || 'Hubo un error al registrar el mantenimiento.', 'error');
                        });
                }
            });
        });
    } else {
        Swal.fire('Advertencia', 'No se proporcionó un ID de equipo.', 'warning');
    }
});

// Botón regresar
document.addEventListener('DOMContentLoaded', () => {
    const btnRegresar = document.getElementById('btnRegresar');
    if (btnRegresar) {
        btnRegresar.addEventListener('click', (event) => {
            event.preventDefault();
            window.location.href = `../../view/ListarEquipo/`;
        });
    }
});
