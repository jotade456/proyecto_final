const searchContainer = document.querySelector('.search-input-box');
const inputSearch = searchContainer.querySelector('input');
const boxSuggestions = document.querySelector('.container-suggestions');

// Mostrar las sugerencias en la lista
function showSuggestions(list) {
    boxSuggestions.innerHTML = list.join('');
  currentIndex = -1; // Reiniciar el índice al mostrar nuevas sugerencias

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

