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
                  document.getElementById("valorProducto-mod").value = data.precio_unitario;
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

document.querySelector('.formulario-P1 button').addEventListener('click', function (e) {
  e.preventDefault();
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

document.querySelector('.formulario-P2 button').addEventListener('click', function (e) {
  e.preventDefault();
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

function mostrarVentanaP3() {
  const ventana = document.getElementById('ventanaEmergenteP3');
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

document.querySelector('.formulario-P3 button').addEventListener('click', function (e) {
  e.preventDefault();
  mostrarVentanaP3();
});



