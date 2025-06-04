let sedesCache = [];
const modal = new bootstrap.Modal(document.getElementById('modalEvento'));

// Funciones de alerta
function mostrarAlerta({ icon = 'info', title = '', text = '', timer = null }) {
  const config = { icon, title, text };
  if (timer) config.timer = timer;
  Swal.fire(config);
}

function alertaError(text = 'Ha ocurrido un error.') {
  mostrarAlerta({ icon: 'error', title: 'Error', text });
}

function alertaExito(text = 'Operación realizada con éxito.') {
  mostrarAlerta({ icon: 'success', title: 'Éxito', text });
}

function alertaAdvertencia(text = 'Verifica los datos.') {
  mostrarAlerta({ icon: 'warning', title: 'Advertencia', text });
}

// Eventos
document.addEventListener('DOMContentLoaded', function () {

  cargarSedes(() => {
    comboSedes('sede_id');
  });

  const allDayCheckbox = document.getElementById('all_day');
  const fechaInicioInput = document.getElementById('fecha_inicio');
  const fechaFinInput = document.getElementById('fecha_fin');

  function toggleDateTimeInputs() {
    if (allDayCheckbox.checked) {
      fechaInicioInput.type = 'date';
      fechaFinInput.type = 'date';
      if (fechaInicioInput.value) fechaInicioInput.value = fechaInicioInput.value.split('T')[0];
      if (fechaFinInput.value) fechaFinInput.value = fechaFinInput.value.split('T')[0];
    } else {
      fechaInicioInput.type = 'datetime-local';
      fechaFinInput.type = 'datetime-local';
    }
  }

  allDayCheckbox.addEventListener('change', toggleDateTimeInputs);
  toggleDateTimeInputs();

  var Calendar = window.FullCalendar && window.FullCalendar.Calendar;
  var Draggable = window.FullCalendar && window.FullCalendar.Draggable;

  if (!Calendar || !Draggable) {
    alertaError('FullCalendar no se ha cargado. Por favor, incluye los scripts de FullCalendar antes de este script.');
    return;
  }

  var containerEl = document.getElementById('external-events');
  var calendarEl = document.getElementById('calendar');
  //var checkbox = document.getElementById('drop-remove');

  // Funciones de formato
  function formatDateTimeLocal(date) {
    const d = new Date(date);
    return d.getFullYear() + '-' +
      String(d.getMonth() + 1).padStart(2, '0') + '-' +
      String(d.getDate()).padStart(2, '0') + 'T' +
      String(d.getHours()).padStart(2, '0') + ':' +
      String(d.getMinutes()).padStart(2, '0');
  }

  function formatDateOnly(date) {
    const d = new Date(date);
    return d.getFullYear() + '-' +
      String(d.getMonth() + 1).padStart(2, '0') + '-' +
      String(d.getDate()).padStart(2, '0');
  }

  // Eventos externos (arrastrables)
  new Draggable(containerEl, {
    itemSelector: '.fc-event',
    eventData: function (eventEl) {
      return {
        title: eventEl.dataset.title,
        descripcion: 'Evento predeterminado',
        color: eventEl.dataset.color || '#3788d8',
        backgroundColor: eventEl.dataset.color || '#3788d8',
        allDay: 1,
      };
    }
  });

  // Calendario
  var calendar = new Calendar(calendarEl, {
    timeZone: 'local',
    //Barra superior
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    //Permite editar eventos
    editable: true,
    //Permite editar la duracion de los eventos
    eventDurationEditable: true,
    //Permite arrastrar eventos
    droppable: true,
    //Permite seleccionar eventos
    selectable: true,
    //Permite seleccionar todo el dia
    selectMirror: true,
    //URL de los eventos
    events: {
      url: '../../controllers/evento.php?op=listar',
      method: 'GET',
      failure: function () {
        alertaError('Error al cargar los eventos');
      }/*,
      success: function (data) {
        console.log(data);
      }*/
    },
    //Función para arrastrar y crear eventos
    drop: function (info) {
      const form = document.getElementById('formEvento');
      form.reset();

      const titulo = info.draggedEl.dataset.title;
      const color = info.draggedEl.dataset.color || '#3788d8';

      const inicio = new Date(info.date);
      if (inicio.getHours() === 0 && inicio.getMinutes() === 0) {
        inicio.setHours(8, 0);
      }
      const fin = new Date(inicio);
      fin.setHours(inicio.getHours() + 1);

      document.getElementById('titulo').value = titulo;
      document.getElementById('descripcion').value = 'Evento predeterminado';
      document.getElementById('fecha_inicio').value = allDayCheckbox.checked ? formatDateOnly(inicio) : formatDateTimeLocal(inicio);
      document.getElementById('fecha_fin').value = allDayCheckbox.checked ? formatDateOnly(fin) : formatDateTimeLocal(fin);
      document.getElementById('color').value = color;

      const sedeSelect = document.getElementById('sede_id');
      if (sedeSelect) {
        sedeSelect.value = sedeSelect.options[1]?.value || null;
      }

      modal.show();

      const oldBtn = document.getElementById('btnGuardarEvento');
      const newBtn = oldBtn.cloneNode(true);
      oldBtn.parentNode.replaceChild(newBtn, oldBtn);
      newBtn.textContent = 'Guardar Evento';

      newBtn.addEventListener('click', function (e) {
        e.preventDefault();
        if (!form.checkValidity()) {
          form.reportValidity();
          return;
        }

        const formData = new FormData(form);

        fetch('../../controllers/evento.php?op=insert', {
          method: 'POST',
          body: new URLSearchParams(formData),
        })
          .then(res => res.json())
          .then(data => {
            if (data.status) {
              alertaExito(data.message || 'Evento guardado correctamente');
              modal.hide();
            } else {
              alertaError(data.message || 'Error al guardar el evento');
            }
          })
          .catch(error => {
            alertaError(error.message || 'Error al guardar el evento');
          });
      });
    },
    //Redimensionar y actualizar evento
    eventResize: function (info) {
      actualizarEvento(info.event);
    },
    //Arrastrar evento y actualizar
    eventDrop: function (info) {
      actualizarEvento(info.event);
    },
    //Función para clickear eventos y mostrarlos en el modal para editarlos
    eventClick: function (info) {
      info.el.style.cursor = 'pointer';
      const event = info.event;
      console.log(event);

      document.getElementById('evento_id').value = event.id;
      document.getElementById('titulo').value = event.title;
      document.getElementById('descripcion').value = event.extendedProps.descripcion || '';
      document.getElementById('fecha_inicio').value = event.allDay ? formatDateOnly(event.start) : formatDateTimeLocal(event.start);
      document.getElementById('fecha_fin').value = event.end ? (event.allDay ? formatDateOnly(event.end) : formatDateTimeLocal(event.end)) : (event.allDay ? formatDateOnly(event.start) : formatDateTimeLocal(event.start));
      document.getElementById('all_day').checked = event.allDay;
      document.getElementById('color').value = event.backgroundColor || '#3788d8';
      document.getElementById('sede_id').value = event.extendedProps.sede_id || '';

      // Cambiar texto del botón y asegurarse de que no haya múltiples listeners
      const tituloModal = document.getElementById('modalEventoLabel');
      tituloModal.textContent = 'Actualizar Evento';
      const btnGuardar = document.getElementById('btnGuardarEvento');
      btnGuardar.textContent = 'Actualizar Evento';

      const newBtn = btnGuardar.cloneNode(true);
      btnGuardar.parentNode.replaceChild(newBtn, btnGuardar);

      const guardarHandler = function (e) {
        e.preventDefault();
        guardarEvento();
      };

      newBtn.addEventListener('click', guardarHandler, { once: true });
      document.getElementById('btnEliminarEvento').style.display = 'inline-block';
      modal.show();
    },
    //Quitar cursor
    eventMouseLeave: function (info) {
      info.el.style.cursor = 'default';
    },
    //Mostrar descripción utilizando tippy
    eventDidMount: function (info) {
      const now = new Date();
      const eventoFin = info.event.end || info.event.start;
      if (eventoFin < now) {
        info.el.style.opacity = '0.4';
        info.el.style.pointerEvents = 'none'; // bloquea clics también si quieres
      }

      if (info.event.extendedProps.descripcion) {
        tippy(info.el, {
          content: info.event.extendedProps.descripcion,
          theme: 'light',
          placement: 'top',
          arrow: true,
        });
      }
    },
  });

  calendar.render();

  // Función para cargar sedes
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
            alertaError('Error en formato de sedes.');
          }
        })
        .catch(error => {
          alertaError(error);
        });
    }
  }

  // Función para cargar sedes en un select
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

  // Función para crear eventos
  function crearEvento() {
    const form = document.getElementById("formEvento");
    if (!form) return;
  
    form.reset();
  
    const eventoIdInput = document.getElementById("evento_id");
    if (eventoIdInput) eventoIdInput.value = "";
  
    const colorInput = document.getElementById("color");
    if (colorInput) colorInput.value = "#3788d8";
  
    const btnGuardar = document.getElementById("btnGuardarEvento");
    if (btnGuardar) btnGuardar.textContent = "Guardar Evento";
  
    const btnEliminar = document.getElementById("btnEliminarEvento");
    if (btnEliminar) btnEliminar.style.display = "none";
  
    modal.show();
  }
  

  // Función para guardar eventos
  function guardarEvento() {
    const form = document.getElementById('formEvento');
    const formData = new FormData(form);
    const idEvento = document.getElementById('evento_id').value;
    const btnGuardarEvento = document.getElementById('btnGuardarEvento');

    const titulo = formData.get('titulo')?.trim();
    const fechaInicio = formData.get('fecha_inicio');
    const fechaFin = formData.get('fecha_fin');

    if (!titulo || !fechaInicio || !fechaFin) {
      alertaAdvertencia('Por favor completa todos los campos obligatorios.');
      return;
    }

    const start = new Date(fechaInicio);
    const end = new Date(fechaFin);
    if (isNaN(start.getTime()) || isNaN(end.getTime()) || end <= start) {
      alertaAdvertencia('La fecha de fin debe ser posterior a la de inicio.');
      return;
    }

    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    btnGuardarEvento.disabled = true;
    btnGuardarEvento.textContent = 'Guardando...';

    fetch('../../controllers/evento.php?op=' + (idEvento ? 'update' : 'insert'), {
      method: 'POST',
      body: formData,
    })
      .then(response => response.json())
      .then(data => {
        try {
          if (data.status) {
            if (idEvento) {
              const eventoForm = getEventoDesdeForm();
              actualizarEvento(eventoForm);
            }
            form.reset();
            modal.hide();
            alertaExito(`Evento ${idEvento ? 'actualizado' : 'creado'} correctamente`);

            // Solo recargar eventos desde el servidor
            calendar.refetchEvents();
          } else {
            alertaError(data.message || 'Error al guardar el evento');
          }
        } catch (error) {
          alertaError(error.message || 'Error al guardar el evento');
        }
      })
      .catch(error => {
        alertaError(error.message || 'Error al guardar el evento');
      })
      .finally(() => {
        btnGuardarEvento.disabled = false;
        btnGuardarEvento.textContent = 'Guardar';
      });
  }

  // Función para actualizar eventos
  function actualizarEvento(event) {
    const fechaInicio = new Date(event.start);
    const fechaFin = event.end ? new Date(event.end) : fechaInicio;

    if (isNaN(fechaInicio.getTime()) || isNaN(fechaFin.getTime())) {
      alertaError('Fechas inválidas al actualizar el evento.');
      return;
    }

    const descripcion = event.extendedProps?.descripcion || '';
    const sede = event.extendedProps?.sede_id ?? event.sede_id ?? null;

    if (!sede) {
      alertaAdvertencia('Este evento debe estar asociado a una sede antes de modificarlo.');
      calendar.refetchEvents();
      return;
    }

    const datos = new URLSearchParams({
      evento_id: event.id,
      titulo: event.title,
      fecha_inicio: event.allDay ? formatDateOnly(fechaInicio) : formatDateTimeLocal(fechaInicio),
      fecha_fin: event.allDay ? formatDateOnly(fechaFin) : formatDateTimeLocal(fechaFin),
      color: event.backgroundColor || event.color || '#3788d8',
      all_day: event.allDay ? 1 : 0,
      descripcion: descripcion,
      sede_id: sede
    });

    fetch('../../controllers/evento.php?op=update', {
      method: 'POST',
      body: datos,
    })
      .then(res => res.json())
      .then(data => {
        if (!data.status) {
          alertaError(data.message || 'No se pudo actualizar el evento.');
        } else {
          calendar.refetchEvents();
          alertaExito(data.message || 'Evento actualizado correctamente.');
          modal.hide();
        }
      })
      .catch(error => {
        alertaError(error.message || 'Hubo un error al actualizar el evento.');
      });
  }

  // Eventos de los botones
  document.getElementById("btnNuevoEvento").addEventListener("click", crearEvento);
  document.getElementById('btnGuardarEvento').addEventListener('click', guardarEvento);

  document.getElementById('btnEliminarEvento').addEventListener('click', () => {
    const idEvento = document.getElementById('evento_id').value;
    if (!idEvento) {
      alertaError('No se ha seleccionado ningún evento para eliminar.');
      return;
    }

    Swal.fire({
      title: '¿Desea eliminar el evento?',
      text: 'Esta acción no se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        eliminarEvento(idEvento);
      }
    });
  });

  // Función para eliminar eventos
  function eliminarEvento(id) {
    const btnEliminar = document.getElementById('btnEliminarEvento');
    btnEliminar.disabled = true;
    btnEliminar.textContent = 'Eliminando...';

    fetch('../../controllers/evento.php?op=delete', {
      method: 'POST',
      body: new URLSearchParams({ evento_id: id }),
    })
      .then(response => response.json())
      .then(data => {
        if (!data.status) {
          alertaError(data.message || 'No se pudo eliminar el evento.');
        } else {
          // Eliminar el evento directamente del calendario si está cargado
          const evento = calendar.getEventById(data.id);
          if (evento) evento.remove();
          alertaExito(data.message || 'Evento eliminado correctamente.');
          calendar.refetchEvents();
          modal.hide();
        }
      })
      .catch(error => {
        alertaError(error.message || 'Hubo un error al eliminar el evento.');
      })
      .finally(() => {
        btnEliminar.disabled = false;
        btnEliminar.textContent = 'Eliminar';
      });
  }

  // Función para obtener los datos del formulario
  function getEventoDesdeForm() {
    const form = document.getElementById('formEvento');
    const formData = new FormData(form);
    const formDataObj = Object.fromEntries(formData.entries());

    return {
      id: formDataObj.evento_id,
      title: formDataObj.titulo,
      start: formDataObj.fecha_inicio,
      end: formDataObj.fecha_fin,
      allDay: formDataObj.all_day,
      color: formDataObj.color,
      backgroundColor: formDataObj.color,
      extendedProps: {
        descripcion: formDataObj.descripcion || '',
        sede_id: formDataObj.sede_id || null
      }
    };
  }
});