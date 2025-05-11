const searchContainer = document.querySelector('.search-input-box');
const inputSearch = searchContainer.querySelector('input');
const boxSuggestions = document.querySelector('.container-suggestions');

inputSearch.onkeyup = e => {
    let userData = e.target.value;
    let filteredSuggestions = [];

    if (userData) {
        filteredSuggestions = suggestions.filter(data => {
            return data.toLocaleLowerCase().startsWith(userData.toLocaleLowerCase());
        });

        // Si no hay sugerencias, mostrar el valor del usuario
        if (filteredSuggestions.length === 0) {
            filteredSuggestions = [`<li>No se encontraron resultados para "${userData}"</li>`];
        } else {
            // Mapeo de los datos para crear los elementos li
            filteredSuggestions = filteredSuggestions.map(data => `<li>${data}</li>`);
        }

        searchContainer.classList.add('active');
        showSuggestions(filteredSuggestions);
    } else {
        searchContainer.classList.remove('active');
    }
};

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

inputSearchModificar.onkeyup = e => {
    let userData = e.target.value;
    let filteredSuggestions = [];

    if (userData) {
        filteredSuggestions = suggestions.filter(data => {
            return data.toLocaleLowerCase().startsWith(userData.toLocaleLowerCase());
        });

        if (filteredSuggestions.length === 0) {
            filteredSuggestions = [`<li>No se encontraron resultados para "${userData}"</li>`];
        } else {
            filteredSuggestions = filteredSuggestions.map(data => `<li>${data}</li>`);
        }

        searchContainerModificar.classList.add('active');
        showSuggestionsModificar(filteredSuggestions);
    } else {
        searchContainerModificar.classList.remove('active');
    }
};

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







  
  
  

