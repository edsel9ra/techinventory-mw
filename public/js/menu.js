const btnToggleMenu = document.getElementById('btnToggleMenu');
const menu = document.querySelector('.main-menu');
const icono = document.getElementById('iconoToggle');
const overlay = document.getElementById('menuOverlay');

btnToggleMenu.addEventListener('click', function (e) {
    e.stopPropagation();
    const activo = menu.classList.toggle('active');
    overlay.classList.toggle('d-none', !activo);
    icono.classList.toggle('bi-list', !activo);
    icono.classList.toggle('bi-x', activo);
});

document.addEventListener('click', function (e) {
    const esClickEnMenu = menu.contains(e.target);
    const esClickEnBoton = btnToggleMenu.contains(e.target);

    if (!esClickEnMenu && !esClickEnBoton && menu.classList.contains('active')) {
        menu.classList.remove('active');
        overlay.classList.add('d-none');
        icono.classList.remove('bi-x');
        icono.classList.add('bi-list');
    }
});

overlay.addEventListener('click', function () {
    menu.classList.remove('active');
    overlay.classList.add('d-none');
    icono.classList.remove('bi-x');
    icono.classList.add('bi-list');
});
