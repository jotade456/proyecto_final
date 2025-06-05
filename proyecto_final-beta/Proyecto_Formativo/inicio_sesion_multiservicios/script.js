



document.addEventListener("DOMContentLoaded", function () {
    const buscarInput = document.getElementById("buscar");
    const listaResultados = document.getElementById("listaResultados");
    const cerrarBusqueda = document.getElementById("cerrarBusqueda");

    const productos = ["Laptop", "Teclado", "Mouse", "Monitor", "Impresora", "Cable HDMI"];

    buscarInput.addEventListener("input", function () {
        const filtro = buscarInput.value.toLowerCase();
        listaResultados.innerHTML = "";

        if (filtro !== "") {
            const resultadosFiltrados = productos.filter(producto => 
                producto.toLowerCase().includes(filtro)
            );

            resultadosFiltrados.forEach(producto => {
                let item = document.createElement("li");
                item.textContent = producto;
                item.addEventListener("click", function () {
                    buscarInput.value = producto;
                    listaResultados.style.display = "none";
                });
                listaResultados.appendChild(item);
            });

            listaResultados.style.display = "block";
            cerrarBusqueda.style.display = "inline";
        } else {
            listaResultados.style.display = "none";
            cerrarBusqueda.style.display = "none";
        }
    });

    cerrarBusqueda.addEventListener("click", function () {
        buscarInput.value = "";
        listaResultados.style.display = "none";
        cerrarBusqueda.style.display = "none";
    });
});

