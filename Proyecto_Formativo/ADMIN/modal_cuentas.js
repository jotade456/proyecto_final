let usuarioSeleccionadoId = null;

// Abrir modal Cuentas
function abrirModalCuentas() {
    document.getElementById('modalCuentas').classList.add('active');
    cargarUsuarios(); 
}

// Cerrar modal Cuentas
function cerrarModalCuentas() {
    document.getElementById('modalCuentas').classList.remove('active');
}

// Cargar usuarios
function cargarUsuarios() {

    fetch('manejo_usuarios.php')
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error('❌ Error:', data.error);
            return;
        }

        const contenedor = document.getElementById('usuariosContenedor');
        contenedor.innerHTML = ''; 
        usuarioSeleccionadoId = null; 

        data.usuarios.forEach(user => {
            const tarjeta = document.createElement('div');
            tarjeta.className = 'tarjeta-usuario';
            tarjeta.dataset.id = user.id;

            tarjeta.innerHTML = `
                <img src="https://cdn-icons-png.flaticon.com/512/2922/2922510.png" alt="avatar">
                <h4 class="nombre_usuario">${user.nombre_usuario}</h4>
                <p>${user.rol}</p>
            `;

            tarjeta.addEventListener('click', () => {
                // Desmarcar todos
                document.querySelectorAll('.tarjeta-usuario').forEach(t => t.classList.remove('seleccionado'));

                // Marcar esta
                tarjeta.classList.add('seleccionado');
                usuarioSeleccionadoId = user.id;

                console.log('✅ Usuario seleccionado:', usuarioSeleccionadoId);
            });

            contenedor.appendChild(tarjeta);
        });
    })
    .catch(error => console.error('❌ Error al cargar usuarios:', error));
}

// Mostrar ventana de confirmación
function mostrarVentanaConfirmacion() {
    const ventana = document.getElementById('ventanaConfirmacionCuentas');
    ventana.style.display = 'flex';
}

// Cerrar ventana de confirmación
function cerrarVentanaConfirmacion() {
    const ventana = document.getElementById('ventanaConfirmacionCuentas');
    ventana.style.display = 'none';
}

// Mostrar ventana "Cuenta eliminada con éxito"
function mostrarVentanaCuentas() {
    const ventana = document.getElementById('ventanaEmergenteCuentas');
    ventana.style.display = 'flex';
    ventana.classList.remove('desapareciendo-cuentas');

    setTimeout(() => {
        ventana.classList.add('desapareciendo-cuentas');
        setTimeout(() => {
            ventana.style.display = 'none';
            ventana.classList.remove('desapareciendo-cuentas');
        }, 300);
    }, 2000);
}

function mostrarVentanaSeleccion() {
    const ventana = document.getElementById('ventanaSeleccionCuentas');
    ventana.style.display = 'flex';
}


document.getElementById('btnCerrarSeleccionCuentas').addEventListener('click', () => {
    const ventana = document.getElementById('ventanaSeleccionCuentas');
    ventana.style.display = 'none';
});

document.querySelector('.boton-eliminar').addEventListener('click', () => {
    if (!usuarioSeleccionadoId) {
        mostrarVentanaSeleccion();
        return;
    }

    mostrarVentanaConfirmacion();
});

document.getElementById('btnConfirmarEliminarCuenta').addEventListener('click', () => {
    cerrarVentanaConfirmacion();

    fetch('manejo_usuarios.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `eliminar_usuario=${usuarioSeleccionadoId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarVentanaCuentas();
            cargarUsuarios(); // Recargar lista de usuarios
        } else {
            alert('❌ Error: ' + data.error);
        }
    })
    .catch(error => console.error('❌ Error al eliminar usuario:', error));
});

// Botón "Cancelar" en la ventana de confirmación
document.getElementById('btnCancelarEliminarCuenta').addEventListener('click', () => {
    cerrarVentanaConfirmacion();
});
