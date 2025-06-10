//------------------------MODAL AGREGAR PRODUCTO ---------------------
function abrirModal() {
    document.getElementById('modalProducto').classList.add('active');
}

function cerrarModal() {
    document.getElementById('modalProducto').classList.remove('active');
    limpiarFormularioAgregarProducto();
}

function limpiarFormularioAgregarProducto() {
    const preview = document.getElementById('previewImagen');
    const inputImagen = document.getElementById('inputImagen');
    const inputNombre = document.getElementById('nombreProductoAgregar');
    const inputValor = document.getElementById('valorProductoAgregar');

    preview.src = '';
    preview.style.display = 'none';
    inputImagen.value = '';
    inputNombre.value = '';
    inputValor.value = '';
}


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


// Enviar formulario con AJAX
document.getElementById("formAgregarProducto").addEventListener("submit", function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    const nombre = formData.get('nombre_producto');
    const valor = formData.get('valor_producto');
    const imagen = formData.get('imagen_producto');

    if (!nombre || !valor || !imagen.name) {
        mostrarVentana("❌ Todos los campos son obligatorios", false);
        return;
    }

    fetch("../controlador/controlador.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(response => {
        if (response.includes("AGREGADO_OK")) {
            mostrarVentana("Producto agregado con éxito", true);
            limpiarFormularioAgregarProducto();
        } else if (response.includes("ERROR_PRODUCTO_EXISTE")) {
            mostrarVentana("❌ Ya existe un producto con ese nombre", false);
        } else if (response.includes("ERROR_VALOR_NEGATIVO")) {
            mostrarVentana("❌ El valor no puede ser negativo", false);
        } else {
            mostrarVentana("❌ Error al agregar producto", false);
        }
    })

    .catch(error => {
        console.error(error);
        mostrarVentana("❌ Error inesperado", false);
    });
    });

    function mostrarVentana(mensaje, exito = true) {
    const ventana = document.getElementById('ventanaEmergente');
    const cuerpo = ventana.querySelector(".ventana-cuerpo p");
    const encabezado = ventana.querySelector(".ventana-encabezado h2");

    cuerpo.textContent = mensaje;

    if (exito) {
        encabezado.textContent = "Agregar producto";
        encabezado.style.backgroundColor = "#17A595";
    } else {
        encabezado.textContent = "Error";
        encabezado.style.backgroundColor = "#17A595";
    }

    ventana.style.display = 'flex';
    ventana.classList.remove('desapareciendo');

    setTimeout(() => {
        ventana.classList.add('desapareciendo');
        setTimeout(() => {
        ventana.style.display = 'none';
        ventana.classList.remove('desapareciendo');
        }, 300);
    }, 2500);
}




//------------------------ MODAL MODIFICAR PRODUCTO ---------------------

function abrirModalModificar() {
    document.getElementById('modalmodificar').classList.add('active');
}

function cerrarModalModificar() {
    document.getElementById('modalmodificar').classList.remove('active');
    limpiarBuscadorModificar();
}

function limpiarBuscadorModificar() {
    const inputModificar = document.querySelector('.modal-modificar input[type="text"]');
    const sugerenciasModificar = document.querySelector('.modal-modificar .container-suggestions-modificar');

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

function limpiarFormularioProducto() {
    document.getElementById('idProducto-mod').value = '';
    document.getElementById('nombreProducto-mod').value = '';
    document.getElementById('valorProducto-mod').value = '';
    const previewImagen = document.getElementById('previewImagen-mod');
    previewImagen.src = '';
    previewImagen.style.display = 'none';

    document.querySelector('#ventanaEmergenteModificar .ventana-cuerpo-mod p').textContent = 'Producto Modificado con éxito';
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

            fetch("../controlador/controlador.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `ajax_buscar_nombre=${encodeURIComponent(nombre)}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    document.querySelector('#ventanaEmergenteModificar .ventana-cuerpo-mod p').textContent = data.error;
                    mostrarVentanaModificar();
                } else {
                    document.getElementById("idProducto-mod").value = data.id;
                    document.getElementById("nombreProducto-mod").value = data.nombre;
                    document.getElementById("valorProducto-mod").value = data.valor_producto;
                    let imagenMod = data.imagen_producto;

                    if (imagenMod && imagenMod.startsWith('imagenes_productos/')) {
                        imagenMod = '../controlador/' + imagenMod;
                    }

                    const previewImagen = document.getElementById("previewImagen-mod");
                    previewImagen.src = imagenMod;
                    previewImagen.style.display = "block";

                    abrirModalMod();
                }
            })
            .catch(err => {
                console.error(err);
                document.querySelector('#ventanaEmergenteModificar .ventana-cuerpo-mod p').textContent = 'Error al buscar producto';
                mostrarVentanaModificar();
            });
        });
    }

    // 2: Modificar producto (por AJAX)
    const formModificar = document.getElementById('formularioModificarProducto') || document.querySelector('.formulario-producto-mod');

    if (formModificar) {
        formModificar.addEventListener('submit', async function (e) {
            e.preventDefault();

            const idProducto = document.getElementById('idProducto-mod').value;
            const nuevoNombre = document.getElementById('nombreProducto-mod').value;
            const nuevoValor = document.getElementById('valorProducto-mod').value.replace(',', '.');

            const formData = new FormData();
            formData.append('id_producto', idProducto);
            formData.append('nuevo_nombre', nuevoNombre);
            formData.append('nuevo_valor', nuevoValor);

            const nuevaImagen = document.getElementById('inputImagen-mod').files[0];
            if (nuevaImagen) {
                formData.append('nueva_imagen', nuevaImagen);
            }

            try {
                const res = await fetch('../controlador/controlador.php', {
                    method: 'POST',
                    body: formData
                });
                

                const data = await res.json();

                if (data.success) {
                    document.querySelector('#ventanaEmergenteModificar .ventana-cuerpo-mod p').textContent = data.message || 'Producto Modificado con éxito';
                    mostrarVentanaModificar();
                    limpiarFormularioProducto(); 
                }
                else {
                    // Mostrar ventana emergente de error
                    document.querySelector('#ventanaEmergenteModificar .ventana-cuerpo-mod p').textContent = data.error || 'Error al modificar el producto';
                    mostrarVentanaModificar();
                }

            } catch (error) {
                console.error(error);
                document.querySelector('#ventanaEmergenteModificar .ventana-cuerpo-mod p').textContent = 'Error en la comunicación con el servidor';
                mostrarVentanaModificar();
            }
        });
    }

});

function mostrarVentanaErrorModificar() {
    const ventana = document.getElementById('ventanaEmergenteErrorModificar');
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

//------------------------ MODAL ELIMINAR PRODUCTO ---------------------

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
    limpiarFormularioProductoEliminar();
}

function limpiarFormularioProductoEliminar() {
    document.getElementById("idProducto-eli").value = '';
    document.getElementById("nombreProducto-eli").value = '';
    document.getElementById("valorProducto-eli").value = '';
    const previewImagen = document.getElementById("previewImagen-eli");
    previewImagen.src = '';
    previewImagen.style.display = 'none';
}

function mostrarVentanaEliminar(mensaje = "Producto eliminado con éxito") {
    const ventana = document.getElementById('ventanaEmergenteEliminar');
    const cuerpo = ventana.querySelector(".ventana-cuerpo-eli p");
    const encabezado = ventana.querySelector(".ventana-encabezado-eli");

    if (cuerpo) cuerpo.textContent = mensaje;
    if (encabezado) encabezado.textContent = "Eliminar producto";

    
    ventana.style.display = 'flex';
    ventana.classList.remove('desapareciendo-eli');

    // Ocultar con animación
    setTimeout(() => {
        ventana.classList.add('desapareciendo-eli');
        setTimeout(() => {
            ventana.style.display = 'none';
            ventana.classList.remove('desapareciendo-eli');
        }, 300); 
    }, 2500);
}


//------------------------ DOMContentLoaded ----------------------------

document.addEventListener("DOMContentLoaded", () => {
    const formBuscar = document.getElementById("formBuscarEliminar");
    const inputBuscarEliminar = document.getElementById("inputBuscarEliminar");
    const contenedorSugerenciasEliminar = document.getElementById("contenedorSugerenciasEliminar");
    const buscadorEliminar = document.querySelector(".buscador-eliminar");
    const formEliminar = document.getElementById("formularioEliminarProducto");

    let currentIndexEliminar = -1;

    function buscarProductoEliminar() {
        const nombre = inputBuscarEliminar.value.trim();

        if (nombre.length === 0) {
            alert("Ingrese un nombre de producto.");
            return;
        }

        fetch("../controlador/controlador.php", {
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

                let imagenEli = data.imagen_producto;

                if (imagenEli && imagenEli.startsWith('imagenes_productos/')) {
                    imagenEli = '../controlador/' + imagenEli;
                }

                const previewImagen = document.getElementById("previewImagen-eli");
                previewImagen.src = imagenEli;
                previewImagen.style.display = "block";

                abrirModalEli();
            }
        })
        .catch(err => {
            console.error(err);
            alert("❌ Error al buscar producto");
        });
    }

    if (formBuscar) {
        formBuscar.addEventListener("submit", function (e) {
            e.preventDefault();
            buscarProductoEliminar();
        });
    }

    if (inputBuscarEliminar) {
        inputBuscarEliminar.addEventListener("input", function () {
            const keyword = inputBuscarEliminar.value.trim();

            if (keyword.length === 0) {
                contenedorSugerenciasEliminar.innerHTML = "";
                buscadorEliminar.classList.remove("active");
                currentIndexEliminar = -1;
                return;
            }

            buscadorEliminar.classList.add("active");

            fetch("../controlador/controlador.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `ajax_sugerencias=${encodeURIComponent(keyword)}`
            })
            .then(res => res.json())
            .then(data => {
                contenedorSugerenciasEliminar.innerHTML = "";
                currentIndexEliminar = -1;

                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(sugerencia => {
                        const li = document.createElement("li");
                        li.textContent = sugerencia;
                        li.addEventListener("click", function () {
                            inputBuscarEliminar.value = sugerencia;
                            contenedorSugerenciasEliminar.innerHTML = "";
                            buscadorEliminar.classList.remove("active");
                            currentIndexEliminar = -1;

                            buscarProductoEliminar();
                        });
                        contenedorSugerenciasEliminar.appendChild(li);
                    });
                }
            })
            .catch(err => {
                console.error(err);
            });
        });

        inputBuscarEliminar.addEventListener("keydown", function (event) {
            const items = contenedorSugerenciasEliminar.querySelectorAll("li");

            if (event.key === "ArrowDown") {
                event.preventDefault();
                if (currentIndexEliminar < items.length - 1) {
                    currentIndexEliminar++;
                    updateSelectionEliminar(items);
                }
            }
            else if (event.key === "ArrowUp") {
                event.preventDefault();
                if (currentIndexEliminar > 0) {
                    currentIndexEliminar--;
                    updateSelectionEliminar(items);
                }
            }
            else if (event.key === "Enter") {
                event.preventDefault();
                if (currentIndexEliminar >= 0 && items[currentIndexEliminar]) {
                    inputBuscarEliminar.value = items[currentIndexEliminar].textContent;
                    contenedorSugerenciasEliminar.innerHTML = "";
                    buscadorEliminar.classList.remove("active");
                    currentIndexEliminar = -1;
                    buscarProductoEliminar();
                }
            }
        });
    }

    
    function updateSelectionEliminar(items) {
        items.forEach((item, index) => {
            if (index === currentIndexEliminar) {
                item.classList.add("selected");
            } else {
                item.classList.remove("selected");
            }
        });
    }

    // Eliminar producto
    if (formEliminar) {
        formEliminar.addEventListener("submit", async function (e) {
            e.preventDefault();

            const idProducto = document.getElementById("idProducto-eli").value;

            const formData = new FormData();
            formData.append("eliminar_producto", idProducto);

            try {
                const res = await fetch("../controlador/controlador.php", {
                    method: "POST",
                    body: formData
                });

                const data = await res.json();

                if (data.success) {
                    mostrarVentanaEliminar();

                    limpiarFormularioProductoEliminar();

                    cerrarModalEli();

                } else {
                    alert("❌ Error al eliminar: " + (data.error || "Error desconocido"));
                }

            } catch (error) {
                console.error(error);
                alert("❌ Error en la comunicación con el servidor");
            }
        });
    }
});

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

// Botón de Cotizar: pasar datos de Modal 1 a Modal 2
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
            return;
        }

        abrirModalP1();

        document.getElementById("nombreProductoP1").value = data.nombre;
        document.getElementById("valorProductoP1").value = data.valor_producto;
        document.getElementById("idProductoP1").value = data.id;

        const imagenProducto = data.imagen_producto && data.imagen_producto.startsWith("imagenes_productos/")
            ? "../controlador/" + data.imagen_producto
            : data.imagen_producto || '';

        document.getElementById("imagenProductoP1").value = imagenProducto;

        const preview = document.getElementById("previewImagenP1");
        preview.src = imagenProducto;
        preview.style.display = imagenProducto ? "block" : "none";
    })
    .catch(err => {
        console.error(err);
        mostrarVentanaError();
    });
}



let currentIndex = -1; // Índice actual de sugerencia seleccionada

// Manejo de teclas en el input
inputSearch.addEventListener("keydown", function (event) {
    const items = boxSuggestions.querySelectorAll("li");

    if (event.key === "ArrowDown") {
        event.preventDefault();
        if (currentIndex < items.length - 1) {
        currentIndex++;
        updateSelection(items);
        }
    }

    else if (event.key === "ArrowUp") {
        event.preventDefault();
        if (currentIndex > 0) {
        currentIndex--;
        updateSelection(items);
        }
    }

    else if (event.key === "Enter") {
        event.preventDefault();
        if (currentIndex >= 0 && items[currentIndex]) {
        select(items[currentIndex]);
        } else {
        buscarProductoParaCotizar();
        }
    }
});


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

    fetch('historial.php')
        .then(function(res) { return res.json(); })
        .then(function(historial) {
            //  Ordenar por fecha descendente
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

function abrirModalPerfil() {
    document.getElementById('modalPerfil').classList.add('active');
}

function cerrarModalPerfil() {
    document.getElementById('modalPerfil').classList.remove('active');
}

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
            document.getElementById('rolPerfil').textContent = document.getElementById('rolPerfil').textContent; // El rol no cambia

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







