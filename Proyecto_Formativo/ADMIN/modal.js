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

          fetch("index.php", {
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

          fetch("index.php", {
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
  if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
          preview.src = e.target.result;
          preview.style.display = 'block';
      };
      reader.readAsDataURL(file);
  }
}

// Botón dentro del formulario P1, evita submit y abre modal 2
document.querySelector('.formulario-P1 button').addEventListener('click', function (e) {
  e.preventDefault();
  // Pasar valor del producto de modal 1 a modal 2
  const valorP1 = document.getElementById("valorProductoP1").value;
  document.getElementById("valorProductoOcultoP2").value = valorP1;

  cerrarModalP1();
  abrirModalP2(); 
});



//------------- MODAL PAGINA PRINCIPAL 2 -----------------

function abrirModalP2() {
  document.getElementById('modalPrincipal2').classList.add('active');
}

function cerrarModalP2() {
  document.getElementById('modalPrincipal2').classList.remove('active');
}

function mostrarImagenP2(event) {
  const file = event.target.files[0];
  const preview = document.getElementById('previewImagenP2');
  if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
          preview.src = e.target.result;
          preview.style.display = 'block';
      };
      reader.readAsDataURL(file);
  }
}

// Botón dentro del formulario P2, evita submit y abre modal 3
document.querySelector('.formulario-P2 button').addEventListener('click', function (e) {
  e.preventDefault();
  
  // Obtener valores del formulario P2
  const cantidad = document.getElementById("cantidadProductoP2").value;
  const valorImpresion = document.getElementById("valorImpresionP2").value || 0;
  const valorDiseño = document.getElementById("valorDiseñoP2").value;
  const valorProducto = document.getElementById("valorProductoOcultoP2").value;
  
  // Calcular total
  const total = (cantidad * valorProducto) + Number(valorImpresion) + Number(valorDiseño);
  
  // Pasar datos al modal 3
  document.getElementById("precioTotalP3").value = total;
  
  // Mantener el nombre del producto
  const nombreProducto = document.getElementById("nombreProductoP1").value;
  document.getElementById("nombreProductoP3").value = nombreProducto;
  
  // Mostrar imagen si existe
  const previewP2 = document.getElementById("previewImagenP2");
  const previewP3 = document.getElementById("previewImagenP3");
  if (previewP2.src) {
    previewP3.src = previewP2.src;
    previewP3.style.display = 'block';
  }
  
  cerrarModalP2();
  abrirModalP3();
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

// Función para mostrar la ventana emergente P3 (mensaje de confirmación o similar)
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

// Botón dentro del formulario P3 que muestra la ventana emergente
document.querySelector('.formulario-P3 button').addEventListener('click', function (e) {
  e.preventDefault();
  
  // Obtener datos necesarios
  const cantidad = document.getElementById("cantidadProductoP2").value;
  const valorProducto = document.getElementById("valorProductoOcultoP2").value;
  const valorDiseño = document.getElementById("valorDiseñoP2").value;
  const valorImpresion = document.getElementById("valorImpresionP2").value || 0;
  
  // Enviar datos al servidor
  const formData = new FormData();
  formData.append('cantidad_producto_cotizar', cantidad);
  formData.append('valor_producto_cotizar', valorProducto);
  formData.append('valor_diseño_cotizar', valorDiseño);
  formData.append('valor_impresion_cotizar', valorImpresion);
  
  // Si hay imagen, añadirla
  const inputImagen = document.getElementById("inputImagenP2");
  if (inputImagen.files.length > 0) {
    formData.append('imagen_producto_cotizada', inputImagen.files[0]);
  }
  
  fetch("index.php", {
    method: "POST",
    body: formData
  })
  .then(response => response.text())
  .then(data => {
    mostrarVentanaP3();
    // Opcional: cerrar el modal después de un tiempo
    setTimeout(cerrarModalP3, 2000);
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Error al enviar la cotización');
  });
});


//------------- FUNCION PARA BUSCAR PRODUCTO (AJAX) -----------------

function buscarProductoParaCotizar() {
    const nombre = document.getElementById("buscadorCotizar").value.trim();

    if (!nombre) {
        alert("Por favor ingresa el nombre del producto.");
        return;
    }

    fetch("index.php", {
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
            // Mostrar modal 1
            abrirModalP1();

            // Rellenar datos en el formulario del modal 1
            document.getElementById("nombreProductoP1").value = data.nombre;
            document.getElementById("valorProductoP1").value = data.valor_producto;

            const preview = document.getElementById("previewImagenP1");
            preview.src = data.imagen_producto;
            preview.style.display = "block";

            // También llenar campos ocultos para modal 2 si los usas
            const valorProductoP2 = document.getElementById("valorProductoP2");
            if (valorProductoP2) {
                valorProductoP2.value = data.valor_producto;
            }
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
}

function cerrarModalHistorial() {
  document.getElementById('modalHistorial').classList.remove('active');
}

function descargarPDF(numero) {
  alert(`Descargando Cotización ${numero} en PDF...`);
}
