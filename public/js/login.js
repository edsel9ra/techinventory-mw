document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form_login');
    const correo = document.getElementById('correo_usr');
    const rememberMe = document.getElementById('rememberMe');

    if(localStorage.getItem('recordarCorreo')){
        correo.value = localStorage.getItem('recordarCorreo');
        rememberMe.checked = true;
    }

    if(localStorage.getItem('recordarCheckbox') === 'true'){
        rememberMe.checked = true;
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        if(rememberMe.checked){
            localStorage.setItem('recordarCorreo', correo.value);
            localStorage.setItem('recordarCheckbox', 'true');
        } else {
            localStorage.removeItem('recordarCorreo');
            localStorage.setItem('recordarCheckbox', 'false');
        }

        const formData = new FormData(form);

        fetch('../controllers/usuario.php?op=login', {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error("Error de red o del servidor");
                }
                return response.text();
            })
            .then(text => {
                try {
                    const data = JSON.parse(text);

                    if (data.status) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: data.message,
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = data.redirect;
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
                        });
                    }
                } catch (err) {
                    Swal.fire({
                        icon: 'warning',
                        title: err.message || 'Respuesta no válida',
                        text: '⚠️ Error inesperado en la respuesta del servidor.'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: error.message || 'Error de conexión',
                    text: '⚠️ No se pudo completar la solicitud.'
                });
            });
    });
});
