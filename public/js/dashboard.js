document.addEventListener('DOMContentLoaded', function () {
    fetch('../controllers/evento.php?op=listar_eventos_proximos_en_curso')
      .then(response => response.json())
      .then(data => {
        if (!data.status || data.mensajes.length === 0) return;
  
        const hoy = new Date().toISOString().split('T')[0];
        let storage = JSON.parse(localStorage.getItem('eventos_mostrados') || '{}');
  
        if (storage.fecha !== hoy) {
          storage = {
            fecha: hoy,
            mensajes: []
          };
        }
  
        const yaMostrados = storage.mensajes || [];
  
        // Filtrar mensajes nuevos
        const nuevos = data.mensajes.filter(msg => !yaMostrados.includes(msg));
  
        if (nuevos.length > 0) {
          const contenido = nuevos.map(msg => {
            const esProximo = msg.includes('comienza en');
            const icono = esProximo
              ? '<i class="fa-regular fa-calendar-check text-primary me-1"></i>'
              : '<i class="fa-regular fa-calendar-check text-warning me-1"></i>';
            return `<div class="mb-1">${icono}${msg}</div>`;
          }).join('');
  
          // Mostrar todos los mensajes en un solo toast
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: 'Eventos importantes',
            html: contenido,
            showConfirmButton: false,
            timer: 10000,
            timerProgressBar: true,
            customClass: {
              popup: 'text-start'
            }
          });
  
          // Guardar mensajes como mostrados
          nuevos.forEach(msg => yaMostrados.push(msg));
          localStorage.setItem('eventos_mostrados', JSON.stringify({
            fecha: hoy,
            mensajes: yaMostrados
          }));
        }
      });
  });  