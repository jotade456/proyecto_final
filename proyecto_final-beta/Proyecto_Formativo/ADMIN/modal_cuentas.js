// Variable global para saber cuál está seleccionado
let usuarioSeleccionadoId = null;

function abrirModalCuentas() {
    document.getElementById('modalCuentas').classList.add('active');
    cargarUsuarios(); // 🚀 Cargar usuarios al abrir
}

function cerrarModalCuentas() {
    document.getElementById('modalCuentas').classList.remove('active');
}

function cargarUsuarios() {
    console.log('🚀 cargarUsuarios llamada');

    fetch('api_usuarios.php')
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            console.error('❌ Error:', data.error);
            return;
        }

        console.log('✅ Usuarios recibidos:', data.usuarios);

        const contenedor = document.getElementById('usuariosContenedor');
        contenedor.innerHTML = ''; // Limpiar
        usuarioSeleccionadoId = null; // Resetear selección

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

// Event listener para el botón Eliminar
document.querySelector('.boton-eliminar').addEventListener('click', () => {
    if (!usuarioSeleccionadoId) {
        alert('Por favor selecciona un usuario para eliminar.');
        return;
    }

    if (!confirm('¿Estás seguro de que deseas inhabilitar este usuario?')) {
        return;
    }

    // Enviar petición al backend
    fetch('api_usuarios.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `eliminar_usuario=${usuarioSeleccionadoId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ Usuario inhabilitado.');
            cargarUsuarios(); // Recargar la lista
        } else {
            alert('❌ Error: ' + data.error);
        }
    })
    .catch(error => console.error('❌ Error al eliminar usuario:', error));
});


