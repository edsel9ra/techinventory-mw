let rolesCache = [];
let tablaUsuarios;

document.addEventListener('DOMContentLoaded', function () {
    cargarRoles(() => {
        comboRoles('rol_id');
    });
    listarUsuarios();
    crearUsuario();
});

function cargarRoles(callback) {
    if (rolesCache.length > 0) {
        callback(rolesCache);
    } else {
        fetch('../../controllers/usuario.php?op=combo_roles')
            .then(response => response.json())
            .then(data => {
                if (data.status === true && Array.isArray(data.data)) {
                    rolesCache = data.data;
                    callback(rolesCache);
                } else {
                    Swal.fire('Error', data.message || 'Error al cargar roles', 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', error.message || 'Error al cargar roles', 'error');
            });
    }
}

function comboRoles(selectId, selectedRolId = null) {
    const select = document.getElementById(selectId);
    if (!select) return;

    select.innerHTML = '<option value="" disabled selected >Seleccione un rol</option>';
    rolesCache.forEach(rol => {
        const option = document.createElement('option');
        option.value = rol.rol_id;
        option.textContent = rol.nombre_rol;
        if (selectedRolId && rol.rol_id == selectedRolId) {
            option.selected = true;
        }
        select.appendChild(option);
    });
}

function listarUsuarios() {
    if ($.fn.DataTable.isDataTable('#tablaUsuarios')) {
        tablaUsuarios.ajax.reload(null, false);
    } else {
        tablaUsuarios = $('#tablaUsuarios').DataTable({
            ajax: {
                url: "../../controllers/usuario.php?op=listar_usuarios",
                type: "GET",
                dataSrc: "aaData"
            },
            columns: [
                { title: "Nombre" },
                { title: "Correo" },
                { title: "Rol" },
                {
                    title: "Acciones",
                    orderable: false,
                    searchable: false
                }
            ],
            columnDefs: [
                { className: "text-center", targets: "_all" }
            ],
            language: {
                url: "https://cdn.datatables.net/plug-ins/2.3.0/i18n/es-MX.json"
            }
        });

        // Eventos de los botones
        $('#tablaUsuarios').on('click', '.btn-editar', function () {
            abrirModalEdicion(this);
        });

        $('#tablaUsuarios').on('click', '.btn-eliminar', function () {
            const user_id = $(this).data('user-id');
            eliminarUsuario(user_id);
        });
    }
}

function crearUsuario() {
    const form = document.getElementById('formCrearUsuario');
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(form);
        fetch("../../controllers/usuario.php?op=crear_usuario", {
            method: "POST",
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Usuario creado correctamente',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true,
                    });
                    form.reset();
                    listarUsuarios();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al crear usuario',
                        text: data.message
                    });
                }
            })
            .catch(error => {
                Swal.fire('Error', error.message || 'Error al crear usuario', 'error')
            });
    });
}

function abrirModalEdicion(button) {
    const user_id = button.dataset.userId;
    const nombre = button.dataset.nombre;
    const cargo = button.dataset.cargo;
    const correo = button.dataset.correo;
    const rol_id = button.dataset.rolId;

    document.getElementById('edit_user_id').value = user_id;
    document.getElementById('edit_nombre_usr').value = nombre;
    document.getElementById('edit_cargo_usr').value = cargo;
    document.getElementById('edit_correo_usr').value = correo;
    document.getElementById('edit_passwd_usr').value = ''; // Dejar vacío para que el usuario ingrese una nueva contraseña

    cargarRoles(() => {
        comboRoles('edit_rol_id', rol_id);
        const modal = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
        modal.show();
    });
}

document.getElementById('formEditarUsuario').addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    fetch("../../controllers/usuario.php?op=editar_usuario", {
        method: "POST",
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                Swal.fire({
                    icon: 'success',
                    title: 'Usuario editado correctamente',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true,
                }).then(() => {
                    // Cerrar modal después de que SweetAlert desaparezca
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarUsuario'));
                    modal.hide();

                    // Recargar tabla
                    listarUsuarios();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al editar usuario',
                    text: data.message
                });
            }
        })
        .catch(error => Swal.fire('Error', error.message || 'Error al editar usuario', 'error'));
});

function eliminarUsuario(user_id) {
    Swal.fire({
        title: '¿Estás seguro de eliminar este usuario?',
        text: "¡No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminarlo',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch("../../controllers/usuario.php?op=eliminar_usuario", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `user_id=${encodeURIComponent(user_id)}`
        })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Usuario eliminado correctamente',
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true,
                    });
                    listarUsuarios();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al eliminar usuario',
                        text: data.message
                    });
                }
            })
            .catch(error => Swal.fire('Error', error.message || 'Error al eliminar usuario', 'error'));
        }
    });
}
