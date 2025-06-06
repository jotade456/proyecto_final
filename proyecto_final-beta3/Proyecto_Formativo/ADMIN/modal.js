//------------------------MODAL AGREGAR PRODUCTO ---------------------
function abrirModal() {
  document.getElementById('modalProducto').classList.add('active');
}

function cerrarModal() {
  document.getElementById('modalProducto').classList.remove('active');
  limpiarFormularioProducto();
}

// Limpiar imagen e inputs
function limpiarFormularioProducto() {
  const preview = document.getElementById('previewImagen');
  const inputImagen = document.getElementById('inputImagen');
  const inputNombre = document.getElementById('nombreProducto');
  const inputValor = document.getElementById('valorProducto');

  preview.src = '';
  preview.style.display = 'none';
  inputImagen.value = '';
  inputNombre.value = '';
  inputValor.value = '';
}

// Mostrar vista previa de imagen
function mostrarImagen(event) {
  const file = event.target.files[0];
  const preview = document.getElementById('previewImagen');
  if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
          preview.src = e.target.result;
          preview.style.display = 'block';
      };
      reader.readAsDataURL(file);
  }
}

// Mostrar ventana emergente con animación
function mostrarVentana() {
  const ventana = document.getElementById('ventanaEmergente');
  ventana.style.display = 'flex';
  ventana.classList.remove('desapareciendo-');

  setTimeout(() => {
      ventana.classList.add('desapareciendo');
      setTimeout(() => {
          ventana.style.display = 'none';
          ventana.classList.remove('desapareciendo');
      }, 300);
  }, 2000);
}





//------------------------MODAL MODIFICAR PRODUCTO ---------------------

function abrirModalModificar() {
  document.getElementById('modalmodificar').classList.add('active');
}

function cerrarModalModificar() {
  document.getElementById('modalmodificar').classList.remove('active');
  limpiarBuscadorModificar();
}

function limpiarBuscadorModificar() {
  const inputModificar = document.querySelector('.modal-modificar input[type="text"]');
  const sugerenciasModificar = document.querySelector('.modal-modificar .contenedor-sugerencias');

  if (inputModificar) inputModificar.value = '';
  if (sugerenciasModificar) sugerenciasModificar.innerHTML = '';
}


function abrirModalMod() {
  document.getElementById('modalProductoModificar').classList.add('active');
}

function cerrarModalMod() {
  document.getElementById('modalProductoModificar').classList.remove('active');
  limpiarFormularioProducto();
}


function mostrarImagenMod(event) {
  const file = event.target.files[0];
  const preview = document.getElementById('previewImagen-mod');
  if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
          preview.src = e.target.result;
          preview.style.display = 'block';
      };
      reader.readAsDataURL(file);
  }
}

function mostrarVentanaModificar() {
  const ventana = document.getElementById('ventanaEmergenteModificar');
  ventana.style.display = 'flex';
  ventana.classList.remove('desapareciendo-mod');

  setTimeout(() => {
      ventana.classList.add('desapareciendo-mod');
      setTimeout(() => {
          ventana.style.display = 'none';
          ventana.classList.remove('desapareciendo-mod');
      }, 300);
  }, 2000);
}




document.addEventListener("DOMContentLoaded", () => {
  const formBuscar = document.querySelector("#modalmodificar form");

  if (formBuscar) {
      formBuscar.addEventListener("submit", function (e) {
          e.preventDefault();

          const nombre = document.getElementById("inputBuscarModificar").value.trim();

          fetch("controlador.php", {
              method: "POST",
              headers: {
                  "Content-Type": "application/x-www-form-urlencoded"
              },
              body: `ajax_buscar_nombre=${encodeURIComponent(nombre)}`
          })
          .then(res => res.json())
          .then(data => {
              if (data.error) {
                  alert("❌ " + data.error);
              } else {
                  document.getElementById("idProducto-mod").value = data.id;
                  document.getElementById("nombreProducto-mod").value = data.nombre;
                  document.getElementById("valorProducto-mod").value = data.valor_producto;
                  // mostrar la imagen actual del producto
                  const previewImagen = document.getElementById("previewImagen-mod");
                  previewImagen.src = data.imagen_producto;
                  previewImagen.style.display = "block";
                  document.getElementById("modalProductoModificar").classList.add("active");
              }
          })
          .catch(err => {
              console.error(err);
              alert("❌ Error al buscar producto");
          });
      });
  }
});



//------------------------MODAL ELIMINAR PRODUCTO ---------------------

function abrirModalEliminar() {
  document.getElementById('modaleliminar').classList.add('active');
}

function cerrarModalEliminar() {
  document.getElementById('modaleliminar').classList.remove('active');
  limpiarBuscadorEliminar();
}

function limpiarBuscadorEliminar() {
  const inputEliminar = document.querySelector('.modal-eliminar input[type="text"]');
  const sugerenciasEliminar = document.querySelector('.modal-eliminar .contenedor-sugerencias-eliminar');

  if (inputEliminar) inputEliminar.value = '';
  if (sugerenciasEliminar) sugerenciasEliminar.innerHTML = '';
}


function abrirModalEli() {
  document.getElementById('modalProductoEliminar').classList.add('active');
}

function cerrarModalEli() {
  document.getElementById('modalProductoEliminar').classList.remove('active');
  limpiarFormularioProducto();
}


function mostrarImagenEli(event) {
  const file = event.target.files[0];
  const preview = document.getElementById('previewImagen-eli');
  if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
          preview.src = e.target.result;
          preview.style.display = 'block';
      };
      reader.readAsDataURL(file);
  }
}

function mostrarVentanaEliminar() {
  const ventana = document.getElementById('ventanaEmergenteEliminar');
  ventana.style.display = 'flex';
  ventana.classList.remove('desapareciendo-eli');

  setTimeout(() => {
      ventana.classList.add('desapareciendo-eli');
      setTimeout(() => {
          ventana.style.display = 'none';
          ventana.classList.remove('desapareciendo-eli');
      }, 300);
  }, 2000);
}

document.addEventListener("DOMContentLoaded", () => {
  const formBuscar = document.querySelector("#modaleliminar form");

  if (formBuscar) {
      formBuscar.addEventListener("submit", function (e) {
          e.preventDefault();

          const nombre = document.getElementById("inputBuscarEliminar").value.trim();

          fetch("controlador.php", {
              method: "POST",
              headers: {
                  "Content-Type": "application/x-www-form-urlencoded"
              },
              body: `ajax_buscar_nombre=${encodeURIComponent(nombre)}`
          })
          .then(res => res.json())
          .then(data => {
              if (data.error) {
                  alert("❌ " + data.error);
              } else {
                  document.getElementById("idProducto-eli").value = data.id;
                  document.getElementById("nombreProducto-eli").value = data.nombre;
                  document.getElementById("valorProducto-eli").value = data.valor_producto;
                  document.getElementById("modalProductoEliminar").classList.add("active");
              }
          })
          .catch(err => {
              console.error(err);
              alert("❌ Error al buscar producto");
          });
      });
  }
});



//------------- MODAL PAGINA PRINCIPAL 1 -----------------

function abrirModalP1() {
  document.getElementById('modalPrincipal1').classList.add('active');
}

function cerrarModalP1() {
  document.getElementById('modalPrincipal1').classList.remove('active');
}

function mostrarImagenP1(event) {
  const file = event.target.files[0];
  const preview = document.getElementById('previewImagenP1');
  if (file && file.type.startsWith('image/')) {
    const reader = new FileReader();
    reader.onload = function (e) {
      preview.src = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
  } else {
    alert("Selecciona una imagen válida");
  }
}

// Botón dentro del formulario P1, evita submit y abre modal 2
document.querySelector('.formulario-P1 button').addEventListener('click', function (e) {
  e.preventDefault();
  const valorP1 = document.getElementById("valorProductoP1").value;
  const idProducto = document.getElementById("idProductoP1").value;
  
  document.getElementById("valorProductoOcultoP2").value = valorP1;
  document.getElementById("idProductoOcultoP2").value = idProducto; // Pasar el ID

  cerrarModalP1();
  abrirModalP2();
});


// ------------- MODAL PAGINA PRINCIPAL 2 -----------------
function abrirModalP2() {
    document.getElementById('modalPrincipal2').classList.add('active');
}

function cerrarModalP2() {
    document.getElementById('modalPrincipal2').classList.remove('active');
}

function mostrarImagenP2(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById('previewImagenP2');
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

async function handleCotizacion(e) {
    e.preventDefault();

    const btn = e.target;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';

    try {
        const getValue = id => document.getElementById(id).value;
        const parseNum = id => parseFloat(getValue(id)) || 0;

        const cantidad = parseNum('cantidadProductoP2');
        const valorDiseño = parseNum('valorDiseñoP2');
        const valorProducto = parseNum('valorProductoOcultoP2');
        const valorImpresion = parseNum('valorImpresionP2');
        const idProducto = getValue('idProductoOcultoP2');

        if (cantidad <= 0) throw new Error('Ingrese una cantidad válida (mayor que 0)');
        if (valorDiseño < 0) throw new Error('Ingrese un valor de diseño válido');

        const formData = new FormData();
        formData.append('cantidad_producto_cotizar', cantidad);
        formData.append('valor_producto_cotizar', valorProducto);
        formData.append('valor_diseño_cotizar', valorDiseño);
        formData.append('valor_impresion_cotizar', valorImpresion);
        formData.append('id_producto', idProducto);

        const res = await fetch('controlador.php', { method: 'POST', body: formData });
        const data = await res.json();

        if (!data.success) throw new Error(data.error || 'Error en el servidor');

        // Aquí podrías mostrar el modalP3 si todo sale bien
        // abrirModalP3(); (solo si ya tienes la función hecha)
        document.getElementById('nombreProductoP3').value = data.nombre_producto || 'Sin nombre';
        document.getElementById('precioTotalP3').value = data.total || 0;

        cerrarModalP2();
        abrirModalP3();
        mostrarVentanaP3();

    } catch (error) {
        alert(error.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const btn = document.querySelector('.formulario-P2 button[type="submit"]');
    if (btn) btn.addEventListener('click', handleCotizacion);

    const inputImagenP1 = document.getElementById("inputImagenP1");
    if (inputImagenP1) {
        inputImagenP1.addEventListener("change", mostrarImagenP1);
    }
});

//------------- MODAL PAGINA PRINCIPAL 3 -----------------

function abrirModalP3() {
  document.getElementById('modalPrincipal3').classList.add('active');
}

function cerrarModalP3() {
  document.getElementById('modalPrincipal3').classList.remove('active');
}

function mostrarImagenP3(event) {
  const file = event.target.files[0];
  const preview = document.getElementById('previewImagenP3');
  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      preview.src = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
  }
}

function mostrarVentanaP3() {
  const ventana = document.getElementById('ventanaEmergenteP3');
  ventana.style.display = 'flex';
  ventana.classList.remove('desapareciendo');

  setTimeout(() => {
    ventana.classList.add('desapareciendo');
    setTimeout(() => {
      ventana.style.display = 'none';
      ventana.classList.remove('desapareciendo');
    }, 300);
  }, 2000);
}


//------------- FUNCION PARA BUSCAR PRODUCTO (AJAX) -----------------

function buscarProductoParaCotizar() {
  const nombre = document.getElementById("buscadorCotizar").value.trim();
  
  if (!nombre) {
    alert("Por favor ingresa el nombre del producto.");
    return;
  }

  fetch("controlador.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `ajax_buscar_nombre=${encodeURIComponent(nombre)}`
  })
    .then(res => res.json())
    .then(data => {
      if (data.error) {
        alert("❌ " + data.error);
      } else {
        abrirModalP1();

        document.getElementById("nombreProductoP1").value = data.nombre;
        document.getElementById("valorProductoP1").value = data.valor_producto;
        document.getElementById("idProductoP1").value = data.id; // Asegurar que se guarda el ID

        const preview = document.getElementById("previewImagenP1");
        preview.src = data.imagen_producto;
        preview.style.display = "block";
      }
    })
    .catch(err => {
      console.error(err);
      alert("❌ Error al buscar producto");
    });
}



//------------- MODAL HISTORIAL DE COTIZACIONES -----------------
function abrirModalHistorial() {
  document.getElementById('modalHistorial').classList.add('active');

  fetch('historial.php')
    .then(function(res) { return res.json(); })
    .then(function(historial) {
      console.log('✅ Historial recibido:', historial); // 🚀 Debug

      // 🚀 El contenedor AHORA es el .historial-contenedor con id="historial"
      var contenedor = document.getElementById('historial');

      contenedor.innerHTML = historial.length
        ? historial.map(function(item) {

            // 🚀 ProductosHTML → mostramos el producto individual
            const productosHTML = `
              <div class="producto-item">
                <p><strong>Producto:</strong> ${item.nombre_producto}</p>
                <p><strong>Cantidad:</strong> ${item.cantidad}</p>
                <p><strong>Precio Unitario:</strong> $${item.precio_unitario}</p>
                <p><strong>Subtotal:</strong> $${item.subtotal}</p>
                <hr>
              </div>
            `;

            // 🚀 TARJETA COMPLETA:
            return `
              <div class="tarjeta-cotizacion">
                <p class="titulo-cotizacion"><strong>Cotización</strong> ${item.id}</p>

                <div class="info-cotizacion">
                  <p><strong>ID Usuario:</strong> ${item.id_usuario}</p>
                  <p><strong>Fecha:</strong> ${item.fecha}</p>
                  <p><strong>Total Cotización:</strong> $${item.total_cotizacion}</p>
                </div>

                <div class="detalle-productos">
                  ${productosHTML}

                  <a href="generar_pdf.php?cotizacion_id=${item.id}" target="_blank" class="boton-pdf">
                    📄 Descargar PDF
                  </a>
                </div>
              </div>
            `;
        }).join('')
        : '<p>No hay cotizaciones en el historial.</p>';
    })
    .catch(function(err) {
      console.error('Error al cargar historial:', err);
    });
}


function cerrarModalHistorial() {
  document.getElementById('modalHistorial').classList.remove('active');
}









//------------- MODAL PERFIL -----------------

// ✅ Abrir modal de perfil
function abrirModalPerfil() {
  document.getElementById('modalPerfil').classList.add('active');
}

// ✅ Cerrar modal de perfil
function cerrarModalPerfil() {
  document.getElementById('modalPerfil').classList.remove('active');
}

// ✅ Cambiar imagen de perfil (preview)
const imagenPerfil = document.getElementById('imagenPerfil');
const imgPreview = document.querySelector('.perfil-img');

imagenPerfil.addEventListener('change', function () {
  const archivo = this.files[0];
  if (archivo) {
    const reader = new FileReader();
    reader.onload = function (e) {
      imgPreview.src = e.target.result;
    };
    reader.readAsDataURL(archivo);
  }
});

// 🚀 Cargar datos del perfil (cuando abres el modal)
function cargarDatosPerfil() {
  fetch('controlador.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'obtener_datos_usuario=1'
  })
  .then(response => response.json())
  .then(data => {
    if (data.error) {
      alert('❌ ' + data.error);
      return;
    }

    // 🧠 Llenar campos del formulario
    document.getElementById('nombre').value = data.nombre_usuario;
    document.getElementById('correo').value = data.correo;

    // ✅ Mostrar imagen en el modal
    const imgPreview = document.querySelector('.perfil-img');
    imgPreview.src = data.foto_perfil || 'https://cdn-icons-png.flaticon.com/512/2922/2922510.png';

    // ✅ Mostrar imagen en el menú superior derecho
    const navImg = document.getElementById('imgPerfilNav');
    if (navImg) {
      navImg.src = data.foto_perfil || 'https://cdn-icons-png.flaticon.com/512/2922/2922510.png';
    }

    // ✅ Mostrar nombre y rol en el perfil
    document.getElementById('nombrePerfil').textContent = data.nombre_usuario;
    document.getElementById('rolPerfil').textContent = data.rol;

  })
  .catch(error => console.error('Error al cargar perfil:', error));
}


// 🚀 Guardar cambios del perfil (al hacer clic en "Aplicar cambios")
document.querySelector('.btn-perfil').addEventListener('click', () => {
  const nombre = document.getElementById('nombre').value.trim();
  const correo = document.getElementById('correo').value.trim();
  const contrasena = document.getElementById('contraseña').value.trim();
  const imagen = document.getElementById('imagenPerfil').files[0];

  const formData = new FormData();
  formData.append('actualizar_datos_usuario', 1);
  formData.append('nombre', nombre);
  formData.append('correo', correo);
  formData.append('contrasena', contrasena);
  if (imagen) formData.append('foto_perfil', imagen);

  fetch('controlador.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert('✅ Perfil actualizado correctamente');
      cerrarModalPerfil();
      cargarDatosPerfil(); // Recargar imagen y datos actualizados en el menú
    } else {
      alert('❌ Error al actualizar perfil: ' + data.error);
    }
  })
  .catch(error => console.error('Error al actualizar perfil:', error));
});
document.addEventListener("DOMContentLoaded", cargarDatosPerfil);



