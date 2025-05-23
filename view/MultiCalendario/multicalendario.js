document.addEventListener('DOMContentLoaded', function() {

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
      eventDidMount: function(info) {
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
  });