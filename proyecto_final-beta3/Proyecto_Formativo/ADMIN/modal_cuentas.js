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

            const contenedor = document.querySelector('.tabla-usuarios');

            // Elimina todas las filas excepto el encabezado
            contenedor.querySelectorAll('.fila:not(.encabezado)').forEach(f => f.remove());
            usuarioSeleccionadoId = null;

            data.usuarios.forEach(user => {
                const fila = document.createElement('div');
                fila.className = 'fila';

                fila.innerHTML = `
                    <div class="col nombre">${user.nombre_usuario}</div>
                    <div class="col correo">${user.correo}</div>
                    <div class="col fecha">${user.fecha_registro}</div>
                    <div class="col estado"><span class="estado ${user.estado === 'activo' ? 'activo' : 'inactivo'}">${user.estado}</span></div>
                    <div class="col acciones">
                        ${user.estado === 'activo' ? `
                            <button class="eliminar" title="Inhabilitar" data-id="${user.id}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        ` : `
                            <button class="activar" title="Activar" data-id="${user.id}">
                                <i class="fas fa-check-circle"></i>
                            </button>
                        `}
                    </div>
                `;

                // ✅ Evento para inhabilitar
                const btnEliminar = fila.querySelector('.eliminar');
                if (btnEliminar) {
                    btnEliminar.addEventListener('click', () => {
                        usuarioSeleccionadoId = user.id;
                        mostrarVentanaConfirmacion();
                    });
                }

                // ✅ Evento para activar
                const btnActivar = fila.querySelector('.activar');
                if (btnActivar) {
                    btnActivar.addEventListener('click', () => {
                    usuarioSeleccionadoId = user.id;
                    mostrarModalActivar();
                });

                }

                contenedor.appendChild(fila);
            });
        })
        .catch(error => console.error('❌ Error al cargar usuarios:', error));
}

const btnAceptarActivar = document.querySelector('.btn-aceptar-activar');
if (btnAceptarActivar) {
    btnAceptarActivar.addEventListener('click', () => {
        cerrarModalActivar();
        fetch('manejo_usuarios.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `activar_usuario=${usuarioSeleccionadoId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cargarUsuarios();
            } else {
                alert('❌ Error al activar: ' + data.error);
            }
        })
        .catch(error => console.error('❌ Error al activar usuario:', error));
    });
}



// Mostrar ventana de confirmación
function mostrarVentanaConfirmacion() {
    const ventana = document.getElementById('modalEliminar');
    ventana.style.display = 'flex';
}

// Cerrar ventana de confirmación
function cerrarVentanaConfirmacion() {
    const ventana = document.getElementById('modalEliminar');
    ventana.style.display = 'none';
}

// Mostrar ventana "Cuenta eliminada con éxito"
function mostrarVentanaCuentas() {
    const ventana = document.getElementById('ventanaEmergenteCuentas');
    if (!ventana) {
        console.warn("⚠️ ventanaEmergenteCuentas no encontrada en el DOM.");
        return;
    }

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


function mostrarModalActivar() {
    const ventana = document.getElementById('modalActivar');
    ventana.style.display = 'flex';
}

function cerrarModalActivar() {
    const ventana = document.getElementById('modalActivar');
    ventana.style.display = 'none';
}

function mostrarVentanaSeleccion() {
    const ventana = document.getElementById('ventanaSeleccionCuentas');
    ventana.style.display = 'flex';
}


const cerrarSeleccionBtn = document.getElementById('btnCerrarSeleccionCuentas');
if (cerrarSeleccionBtn) {
    cerrarSeleccionBtn.addEventListener('click', () => {
        document.getElementById('ventanaSeleccionCuentas').style.display = 'none';
    });
}

const eliminarBtn = document.querySelector('.boton-eliminar');
if (eliminarBtn) {
    eliminarBtn.addEventListener('click', () => {
        if (!usuarioSeleccionadoId) {
            mostrarVentanaSeleccion();
            return;
        }
        mostrarVentanaConfirmacion();
    });
}

const confirmarEliminarBtn = document.getElementById('btnConfirmarEliminarCuenta');
if (confirmarEliminarBtn) {
    confirmarEliminarBtn.addEventListener('click', () => {
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
                cargarUsuarios();
            } else {
                alert('❌ Error: ' + data.error);
            }
        })
        .catch(error => console.error('❌ Error al eliminar usuario:', error));
    });
}

const cancelarEliminarBtn = document.getElementById('btnCancelarEliminarCuenta');
if (cancelarEliminarBtn) {
    cancelarEliminarBtn.addEventListener('click', cerrarVentanaConfirmacion);
}
