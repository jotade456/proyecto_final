const searchContainer = document.querySelector('.search-input-box');
const inputSearch = searchContainer.querySelector('input');
const boxSuggestions = document.querySelector('.container-suggestions');


function showSuggestions(list) {
  boxSuggestions.innerHTML = list.join('');
  currentIndex = -1;

  const allList = boxSuggestions.querySelectorAll('li');
  allList.forEach(li => {
    li.addEventListener('click', () => select(li));
  });
}


function select(element) {
    let selectedData = element.textContent;
    inputSearch.value = selectedData;
    searchContainer.classList.remove('active');
    boxSuggestions.innerHTML = "";
}


inputSearch.addEventListener("input", () => {
    const texto = inputSearch.value.trim();

    if (texto.length === 0) {
        boxSuggestions.innerHTML = "";
        searchContainer.classList.remove("active");
        return;
    }

    fetch("../controlador/controlador.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `ajax_sugerencias=${encodeURIComponent(texto)}`
    })
    .then(response => response.json())
    .then(sugerencias => {
        if (Array.isArray(sugerencias) && sugerencias.length > 0) {
            const listItems = sugerencias.map(nombre => `<li>${nombre}</li>`);
            showSuggestions(listItems);
            searchContainer.classList.add("active");
        } else {
            boxSuggestions.innerHTML = "<li>Sin resultados</li>";
            searchContainer.classList.add("active");
        }
    })
    .catch(error => {
        console.error("Error en sugerencias:", error);
    });
});




// ----- MODIFICAR PRODUCTO -----

const searchContainerModificar = document.querySelector('.buscador-modificar');
const inputSearchModificar = document.getElementById('inputBuscarModificar');
const boxSuggestionsModificar = document.querySelector('.container-suggestions-modificar');

let currentIndexModificar = -1; // Para navegación con flechas

function showSuggestionsModificar(list) {
  boxSuggestionsModificar.innerHTML = list.join('');
  currentIndexModificar = -1; // reset

  const allList = boxSuggestionsModificar.querySelectorAll('li');
  allList.forEach((li, index) => {
    li.addEventListener('click', () => selectModificar(li));
  });
}

function selectModificar(element) {
  const selectedData = element.textContent;
  inputSearchModificar.value = selectedData;
  searchContainerModificar.classList.remove('active');
  boxSuggestionsModificar.innerHTML = "";
}

inputSearchModificar.addEventListener("input", () => {
  const texto = inputSearchModificar.value.trim();

  if (texto.length === 0) {
    boxSuggestionsModificar.innerHTML = "";
    searchContainerModificar.classList.remove("active");
    return;
  }

  fetch("../controlador/controlador.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `ajax_sugerencias=${encodeURIComponent(texto)}`
  })
    .then(response => response.json())
    .then(sugerencias => {
      if (Array.isArray(sugerencias) && sugerencias.length > 0) {
        const listItems = sugerencias.map(nombre => `<li>${nombre}</li>`);
        showSuggestionsModificar(listItems);
        searchContainerModificar.classList.add("active");
      } else {
        boxSuggestionsModificar.innerHTML = "<li>Sin resultados</li>";
        searchContainerModificar.classList.add("active");
      }
    })
    .catch(error => {
      console.error("Error en sugerencias modificar:", error);
    });
});

inputSearchModificar.addEventListener("keydown", (e) => {
  const items = boxSuggestionsModificar.querySelectorAll('li');
  if (items.length === 0) return;

  if (e.key === "ArrowDown") {
    e.preventDefault();
    currentIndexModificar++;
    if(currentIndexModificar >= items.length) currentIndexModificar = 0;
    highlightSuggestionModificar(items, currentIndexModificar);
  } else if (e.key === "ArrowUp") {
    e.preventDefault();
    currentIndexModificar--;
    if(currentIndexModificar < 0) currentIndexModificar = items.length - 1;
    highlightSuggestionModificar(items, currentIndexModificar);
  } else if (e.key === "Enter") {
    e.preventDefault();
    if (currentIndexModificar >= 0 && currentIndexModificar < items.length) {
      selectModificar(items[currentIndexModificar]);
    } else {
      buscarProductoModificar();
    }
  }
});

function highlightSuggestionModificar(items, index) {
  items.forEach(item => item.classList.remove('highlighted'));
  if (index >= 0 && index < items.length) {
    items[index].classList.add('highlighted');
  }
}

// Función para manejar la búsqueda al presionar Enter o submit
function buscarProductoModificar(event) {
  if(event) event.preventDefault();

  const nombreBuscado = inputSearchModificar.value.trim();
  if(nombreBuscado.length === 0) return false;

  fetch("../controlador/controlador.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `ajax_buscar_nombre=${encodeURIComponent(nombreBuscado)}`
  })
  .then(res => res.json())
  .then(producto => {
    if(producto.error) {
      alert(producto.error);
    } else {
      
      console.log('Producto encontrado:', producto);
      cerrarModalModificar();
      abrirModalMod();
    }
  })
  .catch(error => {
    console.error('Error al buscar producto:', error);
  });

  return false;
}

function limpiarBuscadorModificar() {
  const inputModificar = document.getElementById('inputBuscarModificar');
  const sugerenciasModificar = document.querySelector('.container-suggestions-modificar');

  if (inputModificar) inputModificar.value = '';
  if (sugerenciasModificar) sugerenciasModificar.innerHTML = '';
}



// ----- ELIMINAR PRODUCTO ------
const searchContainerEliminar = document.querySelector('.buscador-eliminar');
const inputSearchEliminar = searchContainerEliminar.querySelector('input');
const boxSuggestionsEliminar = document.querySelector('.contenedor-sugerencias-eliminar');


function showSuggestionsEliminar(list) {
    boxSuggestionsEliminar.innerHTML = list.join('');
    const allList = boxSuggestionsEliminar.querySelectorAll('li');
    allList.forEach(li => {
        li.addEventListener('click', () => selectEliminar(li));
    });
}

function selectEliminar(element) {
    let selectedData = element.textContent;
    inputSearchEliminar.value = selectedData;
    searchContainerEliminar.classList.remove('active');
}







  
  
  

