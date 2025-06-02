const searchContainer = document.querySelector('.search-input-box');
const inputSearch = searchContainer.querySelector('input');
const boxSuggestions = document.querySelector('.container-suggestions');


// Función para mostrar las sugerencias
function showSuggestions(list) {
    boxSuggestions.innerHTML = list.join('');
    const allList = boxSuggestions.querySelectorAll('li');
    allList.forEach(li => {
        li.addEventListener('click', () => select(li));
    });
}

// Función que se ejecuta cuando el usuario selecciona una sugerencia
function select(element) {
    let selectedData = element.textContent;
    inputSearch.value = selectedData;
    searchContainer.classList.remove('active');
}



// ----- MODIFICAR PRODUCTO ------
const searchContainerModificar = document.querySelector('.buscador-modificar');
const inputSearchModificar = searchContainerModificar.querySelector('input');
const boxSuggestionsModificar = document.querySelector('.contenedor-sugerencias');


function showSuggestionsModificar(list) {
    boxSuggestionsModificar.innerHTML = list.join('');
    const allList = boxSuggestionsModificar.querySelectorAll('li');
    allList.forEach(li => {
        li.addEventListener('click', () => selectModificar(li));
    });
}

function selectModificar(element) {
    let selectedData = element.textContent;
    inputSearchModificar.value = selectedData;
    searchContainerModificar.classList.remove('active');
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







  
  
  

