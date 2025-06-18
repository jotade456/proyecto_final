
let filaSeleccionada = null;

// Modal Inhabilitar
function abrirModalEliminar(boton) {
    filaSeleccionada = boton.closest('.fila');
    document.getElementById('modalEliminar').style.display = 'flex';
}

function cerrarModalEliminar() {
    document.getElementById('modalEliminar').style.display = 'none';
    filaSeleccionada = null;
}

// Modal Activar
function abrirModalActivar(boton) {
    filaSeleccionada = boton.closest('.fila');
    document.getElementById('modalActivar').style.display = 'flex';
}

function cerrarModalActivar() {
    document.getElementById('modalActivar').style.display = 'none';
    filaSeleccionada = null;
}

// Mostrar mensaje flotante
function mostrarMensaje(texto) {
    const mensaje = document.getElementById('mensajeExito');
    mensaje.textContent = texto;
    mensaje.classList.add('mostrar');
    setTimeout(() => {
        mensaje.classList.remove('mostrar');
    }, 1500);
}

// Aceptar inhabilitación
document.querySelector('.btn-aceptar').addEventListener('click', () => {
    if (filaSeleccionada) {
        const estadoSpan = filaSeleccionada.querySelector('.estado');
        estadoSpan.textContent = 'Inactivo';
        estadoSpan.classList.remove('activo');
        estadoSpan.classList.add('inactivo');

        const botonEliminar = filaSeleccionada.querySelector('.eliminar');
        const icono = botonEliminar.querySelector('i');
        icono.classList.remove('fa-trash-alt');
        icono.classList.add('fa-rotate-left');
        botonEliminar.title = 'Activar';

        // Clonar botón como activar
        const nuevoBoton = botonEliminar.cloneNode(true);
        nuevoBoton.classList.remove('eliminar');
        nuevoBoton.classList.add('activar');
        nuevoBoton.addEventListener('click', function () {
            abrirModalActivar(this);
        });
        botonEliminar.parentNode.replaceChild(nuevoBoton, botonEliminar);

        mostrarMensaje('Empleado inhabilitado correctamente');
    }

    cerrarModalEliminar();
});

// Aceptar activación
document.querySelector('.btn-aceptar-activar').addEventListener('click', () => {
    if (filaSeleccionada) {
        const estadoSpan = filaSeleccionada.querySelector('.estado');
        estadoSpan.textContent = 'Activo';
        estadoSpan.classList.remove('inactivo');
        estadoSpan.classList.add('activo');

        const botonActivar = filaSeleccionada.querySelector('.activar');
        const icono = botonActivar.querySelector('i');
        icono.classList.remove('fa-rotate-left');
        icono.classList.add('fa-trash-alt');
        botonActivar.title = 'Eliminar';

        // Clonar botón como eliminar
        const nuevoBoton = botonActivar.cloneNode(true);
        nuevoBoton.classList.remove('activar');
        nuevoBoton.classList.add('eliminar');
        nuevoBoton.addEventListener('click', function () {
            abrirModalEliminar(this);
        });
        botonActivar.parentNode.replaceChild(nuevoBoton, botonActivar);

        mostrarMensaje('Empleado activado correctamente');
    }

    cerrarModalActivar();
});

// Eventos iniciales
document.querySelectorAll('.eliminar').forEach(boton => {
    boton.addEventListener('click', function () {
        abrirModalEliminar(this);
    });
});
