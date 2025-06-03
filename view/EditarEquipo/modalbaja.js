document.addEventListener('DOMContentLoaded', () => {
    const estadoSelect = document.getElementById('estado');
    const modal = new bootstrap.Modal(document.getElementById('modalBajaEquipo'));
    let bajaConfirmada = false;

    // Mostrar/ocultar campo "Otro"
    const motivoSelect = document.getElementById('motivo_baja');
    motivoSelect.addEventListener('change', (e) => {
        const container = document.getElementById('otroMotivoContainer');
        container.style.display = e.target.value === 'Otro' ? 'block' : 'none';
    });

    // Interceptar cambio a "Baja"
    estadoSelect.addEventListener('change', (e) => {
        if (e.target.value === 'Baja' && !bajaConfirmada) {
            modal.show();
        }
    });

    // Manejo de envío del modal
    document.getElementById('formBajaEquipo').addEventListener('submit', (e) => {
        e.preventDefault();

        const formData = new FormData(e.target);
        const motivoSeleccionado = formData.get('motivo_baja');

        const bajaData = {
            proceso_baja: formData.get('proceso_baja')?.trim(),
            motivo_baja: motivoSeleccionado, // debe coincidir con ENUM
            otro_motivo_baja: motivoSeleccionado === 'Otro' ? formData.get('otro_motivo_baja')?.trim() : null,
            concepto_tecnico_baja: formData.get('concepto_tecnico_baja')?.trim()
        };

        if (!bajaData.proceso_baja || !bajaData.motivo_baja || !bajaData.concepto_tecnico_baja) {
            Swal.fire('Campos requeridos', 'Debes completar todos los campos del formulario de baja.', 'warning');
            return;
        }

        // Limpiar campos previos
        ['proceso_baja', 'motivo_baja', 'otro_motivo_baja', 'concepto_tecnico_baja'].forEach(name => {
            const old = document.querySelector(`#formEditarEquipo input[name="${name}"]`);
            if (old) old.remove();
        });

        // Agregar los nuevos campos al formulario principal
        for (const key in bajaData) {
            if (bajaData[key] !== null && bajaData[key] !== undefined) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = bajaData[key];
                document.getElementById('formEditarEquipo').appendChild(input);
            }
        }

        console.log("Datos de baja recogidos:", bajaData);

        bajaConfirmada = true;
        modal.hide();
    });

    // Revertir si se cierra sin confirmar
    document.getElementById('modalBajaEquipo').addEventListener('hidden.bs.modal', () => {
        if (!bajaConfirmada) {
            estadoSelect.value = 'Activo';
        }
    });
});