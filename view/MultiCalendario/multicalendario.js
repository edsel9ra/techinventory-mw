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

document.addEventListener('DOMContentLoaded', function () {

  var Calendar = window.FullCalendar && window.FullCalendar.Calendar;

  var calendarEl = document.getElementById('multicalendar');

  var calendar = new Calendar(calendarEl, {
    timeZone: 'America/Bogota',
    initialView: 'multiMonthYear',
    editable: false,
    eventSources: [
      {
        url: '../../controllers/evento.php?op=listar',
        method: 'GET',
        failure: function () {
          alertaError('Error al cargar los eventos');
        }
      }
    ],
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
    eventClick: function (info) {
      const now = new Date(); // Fecha actual
      const eventoFin = info.event.end || info.event.start; // Usa end si existe, si no, usa start

      if (eventoFin < now) {
        alertaAdvertencia('Este evento ya ha finalizado.');
        return; // Bloquea el clic si ya pasó
      }

      const sedeId = info.event.extendedProps.sede_id;
      if (!sedeId) {
        alertaError('No hay sede asociada a este evento');
        return;
      }
      if (sedeId) {
        window.open(`../ListarEquipo/?sede_id=${sedeId}`, '_blank');
      } else {
        alertaError('No hay URL asociada a este evento');
      }
    },
  });

  calendar.render();
});