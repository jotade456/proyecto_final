// ------------- MODAL PAGINA PRINCIPAL 1 -----------------
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


document.querySelector('.formulario-P1 button').addEventListener('click', function (e) {
    e.preventDefault();

    const valorP1 = document.getElementById("valorProductoP1").value;
    const idProducto = document.getElementById("idProductoP1").value;
    const imagenProducto = document.getElementById("imagenProductoP1").value; 

    document.getElementById("valorProductoOcultoP2").value = valorP1;
    document.getElementById("idProductoOcultoP2").value = idProducto;

    // Poner la imagen en Modal 2
    const previewP2 = document.getElementById('previewImagenP2');
    previewP2.src = imagenProducto;
    previewP2.style.display = imagenProducto ? 'block' : 'none';

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

        const res = await fetch('../controlador/controlador.php', { method: 'POST', body: formData });
        const data = await res.json();

        if (!data.success) throw new Error(data.error || 'Error en el servidor');

        // Mostrar datos en Modal 3
        document.getElementById('nombreProductoP3').value = data.nombre_producto || 'Sin nombre';
        document.getElementById('precioTotalP3').value = data.total || 0;

        let imagenP3 = data.imagen_producto;

        if (imagenP3 && imagenP3.startsWith('imagenes_productos/')) {
            imagenP3 = '../controlador/' + imagenP3;
        }

        const previewP3 = document.getElementById('previewImagenP3');
        previewP3.src = imagenP3;
        previewP3.style.display = imagenP3 ? 'block' : 'none';

        // Cambiar de modal
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
  const nombre = inputSearch.value.trim();
  
  if (!nombre) {
    alert("Por favor ingresa el nombre del producto.");
    return;
  }

  fetch("../controlador/controlador.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `ajax_buscar_nombre=${encodeURIComponent(nombre)}`
  })
    .then(res => res.json())
    .then(data => {
      if (data.error) {
        mostrarVentanaError();
      } else {
        abrirModalP1();

        document.getElementById("nombreProductoP1").value = data.nombre;
        document.getElementById("valorProductoP1").value = data.valor_producto;
        document.getElementById("idProductoP1").value = data.id;
        document.getElementById("imagenProductoP1").value = data.imagen_producto;

        const imagenProducto = data.imagen_producto.startsWith("imagenes_productos/")
            ? "../controlador/" + data.imagen_producto
            : data.imagen_producto;

        document.getElementById("imagenProductoP1").value = imagenProducto;

        const preview = document.getElementById("previewImagenP1");
        preview.src = imagenProducto;
        preview.style.display = "block";
      }
    })
    .catch(err => {
      console.error(err);
      mostrarVentanaError();
    });
}
let currentIndex = -1;


inputSearch.addEventListener("keydown", function (event) {
  const items = boxSuggestions.querySelectorAll("li");


  if (event.key === "ArrowDown") {
    event.preventDefault();
    if (currentIndex < items.length - 1) {
      currentIndex++;
      updateSelection(items);
    }
  }

  // ↑ Flecha arriba
  else if (event.key === "ArrowUp") {
    event.preventDefault();
    if (currentIndex > 0) {
      currentIndex--;
      updateSelection(items);
    }
  }

  // Enter
  else if (event.key === "Enter") {
    event.preventDefault();
    if (currentIndex >= 0 && items[currentIndex]) {
      select(items[currentIndex]);
    } else {
      buscarProductoParaCotizar();
    }
  }
});

// Función para actualizar visualmente la sugerencia seleccionada
function updateSelection(items) {
  items.forEach((item, index) => {
    if (index === currentIndex) {
      item.classList.add("selected");
    } else {
      item.classList.remove("selected");
    }
  });
}

function mostrarVentanaError() {
  const ventana = document.getElementById('ventanaEmergenteError');
  ventana.style.display = 'flex';

  // Animación de salida después de 2.5 segundos
  setTimeout(() => {
    ventana.classList.add('desapareciendo');
    setTimeout(() => {
      ventana.style.display = 'none';
      ventana.classList.remove('desapareciendo');
    }, 300);
  }, 2500);
}


//------------- MODAL HISTORIAL DE COTIZACIONES -----------------
function abrirModalHistorial() {
    document.getElementById('modalHistorial').classList.add('active');

    fetch('../ADMIN/historial.php')
        .then(function(res) { return res.json(); })
        .then(function(historial) {
            console.log('✅ Historial recibido:', historial);
            historial.sort((a, b) => b.id - a.id);

            var contenedor = document.getElementById('historial');

            contenedor.innerHTML = historial.length
                ? historial.map(function(item) {

                    const productosHTML = `
                    <div class="producto-item">
                        <p><strong>Producto:</strong> ${item.nombre_producto}</p>
                        <p><strong>Cantidad:</strong> ${item.cantidad}</p>
                        <p><strong>Precio Unitario:</strong> $${item.precio_unitario}</p>
                        <p><strong>Subtotal:</strong> $${item.subtotal}</p>
                        <hr>
                    </div>
                    `;

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

                            <a href="../ADMIN/generar_pdf.php?cotizacion_id=${item.id}" target="_blank" class="boton-pdf">
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

//  Abrir modal de perfil
function abrirModalPerfil() {
    document.getElementById('modalPerfil').classList.add('active');
}

//  Cerrar modal de perfil
function cerrarModalPerfil() {
    document.getElementById('modalPerfil').classList.remove('active');
}

//  Cambiar imagen de perfil
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

//  Cargar datos del perfil
function cargarDatosPerfil() {
    fetch('../controlador/controlador.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'obtener_datos_usuario=1'
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            document.querySelector('#ventanaEmergentePerfil .ventana-cuerpo-perfil p').textContent = '❌ ' + data.error;
            mostrarVentanaPerfil();
            return;
        }

        document.getElementById('nombre').value = data.nombre_usuario;
        document.getElementById('correo').value = data.correo;

        const imgPreview = document.querySelector('.perfil-img');
        imgPreview.src = data.foto_perfil || 'https://cdn-icons-png.flaticon.com/512/2922/2922510.png';

        const navImg = document.getElementById('imgPerfilNav');
        if (navImg) {
            navImg.src = data.foto_perfil || 'https://cdn-icons-png.flaticon.com/512/2922/2922510.png';
        }
        document.getElementById('nombrePerfil').textContent = data.nombre_usuario;
        document.getElementById('rolPerfil').textContent = data.rol;

        const bienvenidaNombre = document.getElementById('bienvenidaNombre');
        if (bienvenidaNombre) {
            bienvenidaNombre.textContent = data.nombre_usuario;
        }
    })
    .catch(error => {
        console.error('Error al cargar perfil:', error);
        document.querySelector('#ventanaEmergentePerfil .ventana-cuerpo-perfil p').textContent = '❌ Error al cargar perfil';
        mostrarVentanaPerfil();
    });
}

//  Guardar cambios del perfil
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

    fetch('../controlador/controlador.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector('#ventanaEmergentePerfil .ventana-cuerpo-perfil p').textContent = data.message || '✅ Perfil actualizado correctamente';
            mostrarVentanaPerfil();

            document.getElementById('nombrePerfil').textContent = nombre;
            document.getElementById('rolPerfil').textContent = document.getElementById('rolPerfil').textContent;

            const bienvenidaNombre = document.getElementById('bienvenidaNombre');
            if (bienvenidaNombre) {
                bienvenidaNombre.textContent = nombre;
            }

            cargarDatosPerfil();

            setTimeout(() => {
                cerrarModalPerfil();
            }, 2300);
        }
    })
    .catch(error => {
        console.error('Error al actualizar perfil:', error);
        document.querySelector('#ventanaEmergentePerfil .ventana-cuerpo-perfil p').textContent = '❌ Error en la comunicación con el servidor';
        mostrarVentanaPerfil();
    });
});

function mostrarVentanaPerfil() {
    const ventana = document.getElementById('ventanaEmergentePerfil');
    ventana.style.display = 'flex';
    ventana.classList.remove('desapareciendo-perfil');

    setTimeout(() => {
        ventana.classList.add('desapareciendo-perfil');
        setTimeout(() => {
            ventana.style.display = 'none';
            ventana.classList.remove('desapareciendo-perfil');
        }, 300);
    }, 2000);
}

document.addEventListener("DOMContentLoaded", cargarDatosPerfil);



