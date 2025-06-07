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


