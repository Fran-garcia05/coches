document.addEventListener("DOMContentLoaded", function () {
  // Variables DOM
  const destacadosContenedor = document.getElementById("coches-destacados");
  const catalogoContenedor = document.getElementById("catalogo-coches");
  const filtrosContenedor = document.getElementById("filtros-catalogo");
  const toggleCatalogoBtn = document.getElementById("toggleCatalogoBtn");
  const modal = document.getElementById("modalCoche");
  const contenidoModal = document.getElementById("contenidoModal");
  const cerrarModalBtn = document.querySelector(".cerrar");

  let coches = []; // Todos los coches recibidos
  let catalogoVisible = false;

  // Función para abrir modal
  function abrirModal(coche) {
    contenidoModal.innerHTML = `
      <h2>${coche.marca} ${coche.modelo}</h2>
      <img src="img/${coche.imagen}" alt="${coche.marca} ${coche.modelo}">
      <p><strong>Año:</strong> ${coche.año}</p>
      <p><strong>Precio:</strong> €${parseInt(coche.precio).toLocaleString()}</p>
      <p><strong>Kilometraje:</strong> ${coche.kilometraje} km</p>
      <p><strong>Combustible:</strong> ${coche.combustible}</p>
      <p><strong>Estado:</strong> ${coche.estado}</p>
      <p><strong>Fecha de publicación:</strong> ${coche.fecha_publicacion}</p>
      <button id="btnComprar" style="
        margin-top: 1rem;
        padding: 0.7rem 1.3rem;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 25px;
        cursor: pointer;
        font-weight: 700;
        font-size: 1rem;
        width: 100%;
        transition: background-color 0.3s ease;
      ">Comprar</button>
    `;

    modal.style.display = "flex";
    document.body.style.overflow = "hidden";

    const btnComprar = document.getElementById("btnComprar");
    btnComprar.onclick = async () => {
      // Añadir al carrito localmente
      agregarAlCarrito(coche);
      alert(`¡Has añadido ${coche.marca} ${coche.modelo} al carrito!`);
      cerrarModal(); // Cerrar modal después de añadir al carrito
    };
  }

  // Función para cerrar modal
  function cerrarModal() {
    modal.style.display = "none";
    document.body.style.overflow = "";
  }

  // Función para obtener el ID del usuario (implementación)
  async function obtenerIdUsuario() {
    try {
      const response = await fetch('getUserId.php');
      if (!response.ok) {
        throw new Error('Error al obtener el ID del usuario');
      }
      const data = await response.json();
      return data.id_usuario;
    } catch (error) {
      console.error("Error en obtenerIdUsuario:", error);
      return null;
    }
  }

  // Mostrar coches destacados
  function mostrarDestacados() {
    const destacados = coches.slice(0, 6);
    destacadosContenedor.innerHTML = "";

    destacados.forEach(coche => {
      const div = document.createElement("div");
      div.classList.add("coche-card");
      div.innerHTML = `
        <img src="img/${coche.imagen}" alt="${coche.marca} ${coche.modelo}">
        <h3>${coche.marca} ${coche.modelo}</h3>
        <p>€${parseInt(coche.precio).toLocaleString()}</p>
      `;
      div.onclick = () => abrirModal(coche);
      destacadosContenedor.appendChild(div);
    });
  }

  // Mostrar catálogo completo
  function mostrarCatalogo(cochesFiltrados) {
    catalogoContenedor.innerHTML = "";
    if (!cochesFiltrados) cochesFiltrados = coches;

    cochesFiltrados.forEach(coche => {
      const div = document.createElement("div");
      div.classList.add("catalogo-card");
      div.innerHTML = `
        <img src="img/${coche.imagen}" alt="${coche.marca} ${coche.modelo}">
        <h4>${coche.marca} ${coche.modelo}</h4>
        <p>€${parseInt(coche.precio).toLocaleString()}</p>
      `;
      div.onclick = () => abrirModal(coche);
      catalogoContenedor.appendChild(div);
    });
  }

  // Crear filtros para catálogo
  function crearFiltros() {
    filtrosContenedor.innerHTML = `
      <select id="filtroMarca">
        <option value="">Marca</option>
        ${[...new Set(coches.map(c => c.marca))].sort().map(marca => `<option value="${marca}">${marca}</option>`).join('')}
      </select>

      <select id="filtroPrecio">
        <option value="">Precio</option>
        <option value="menor">Menor a Mayor</option>
        <option value="mayor">Mayor a Menor</option>
      </select>

      <select id="filtroAño">
        <option value="">Año</option>
        <option value="nuevo">Más nuevo</option>
        <option value="viejo">Más viejo</option>
      </select>

      <select id="filtroEstado">
        <option value="">Estado</option>
        <option value="Disponible">Disponible</option>
        <option value="Vendido">Vendido</option>
        <option value="Reservado">Reservado</option>
      </select>
    `;

    document.getElementById("filtroMarca").addEventListener("change", filtrarCatalogo);
    document.getElementById("filtroPrecio").addEventListener("change", filtrarCatalogo);
    document.getElementById("filtroAño").addEventListener("change", filtrarCatalogo);
    document.getElementById("filtroEstado").addEventListener("change", filtrarCatalogo);
  }

  // Filtrar catálogo
  function filtrarCatalogo() {
    const marca = document.getElementById("filtroMarca").value;
    const precio = document.getElementById("filtroPrecio").value;
    const año = document.getElementById("filtroAño").value;
    const estado = document.getElementById("filtroEstado").value;

    let filtrados = [...coches];

    if (marca) filtrados = filtrados.filter(c => c.marca === marca);

    if (precio === "menor") filtrados.sort((a,b) => a.precio - b.precio);
    else if (precio === "mayor") filtrados.sort((a,b) => b.precio - a.precio);

    if (año === "nuevo") filtrados.sort((a,b) => b.año - a.año);
    else if (año === "viejo") filtrados.sort((a,b) => a.año - b.año);

    if (estado) filtrados = filtrados.filter(c => c.estado === estado);

    console.log("Coches filtrados (filtrarCatalogo):", filtrados);
    mostrarCatalogo(filtrados);
  }

  // Toggle catálogo visible
  toggleCatalogoBtn.addEventListener("click", () => {
    catalogoVisible = !catalogoVisible;

    if (catalogoVisible) {
      filtrosContenedor.style.display = "flex";
      catalogoContenedor.style.display = "grid";
      toggleCatalogoBtn.textContent = "Ocultar catálogo completo";
      mostrarCatalogo();
      crearFiltros();
    } else {
      filtrosContenedor.style.display = "none";
      catalogoContenedor.style.display = "none";
      toggleCatalogoBtn.textContent = "Ver catálogo completo";
    }
  });

  // Cerrar modal al hacer click en la X o fuera contenido
  cerrarModalBtn.onclick = cerrarModal;
  modal.onclick = function (e) {
    if (e.target === modal) cerrarModal();
  };

  // Obtener coches desde el servidor
  async function obtenerCoches() {
    try {
      const response = await fetch("obtenerCoches.php");
      console.log("Respuesta raw de obtenerCoches.php:", response);
      if (!response.ok) {
        throw new Error("Error HTTP al obtener coches: " + response.status);
      }
      coches = await response.json();
      // Estandarizar el estado de los coches
      coches = coches.map(coche => ({
        ...coche,
        estado: coche.estado.charAt(0).toUpperCase() + coche.estado.slice(1).toLowerCase()
      }));
      console.log("Coches cargados desde el servidor:", coches);

      mostrarDestacados();
    } catch (error) {
      console.error("Error al cargar coches en obtenerCoches():", error);
      destacadosContenedor.innerHTML = "<p>Error cargando coches.</p>";
    }
  }

  obtenerCoches();

  // --- Carrito de compra ---
  let carrito = [];
  let carritoVisible = false;

  function agregarAlCarrito(auto) {
    const index = carrito.findIndex(item =>
      item.marca === auto.marca &&
      item.modelo === auto.modelo &&
      item.año === auto.año &&
      item.precio === auto.precio
    );

    if (index >= 0) {
      carrito[index].cantidad++;
    } else {
      carrito.push({ ...auto, cantidad: 1 });
    }

    actualizarContador();
    mostrarResumen();
  }

  function eliminarDelCarrito(auto) {
    carrito = carrito.filter(item =>
      !(item.marca === auto.marca &&
        item.modelo === auto.modelo &&
        item.año === auto.año &&
        item.precio === auto.precio)
    );

    actualizarContador();
    mostrarResumen();
  }

  function actualizarContador() {
    const contador = document.getElementById("contadorCarrito");
    const totalCantidad = carrito.reduce((acc, item) => acc + item.cantidad, 0);
    contador.textContent = totalCantidad > 0 ? totalCantidad : "";
  }

  function mostrarResumen(toggle = false) {
    const resumen = document.getElementById("resumenCarrito");
    const lista = document.getElementById("listaCarrito");
    lista.innerHTML = "";

    if (carrito.length === 0) {
      lista.innerHTML = "<li>Tu carrito está vacío.</li>";
    } else {
      carrito.forEach(item => {
        const li = document.createElement("li");
        li.style.marginBottom = "0.5rem";
        li.style.display = "flex";
        li.style.justifyContent = "space-between";
        li.style.alignItems = "center";

        const texto = document.createElement("span");
        texto.textContent = `${item.marca} ${item.modelo} (${item.año}) x${item.cantidad} - €${(item.precio * item.cantidad).toLocaleString()}`;

        const btnEliminar = document.createElement("button");
        btnEliminar.textContent = "Eliminar";
        btnEliminar.style.padding = "0.2rem 0.5rem";
        btnEliminar.style.fontSize = "0.8rem";
        btnEliminar.style.backgroundColor = "#dc3545";
        btnEliminar.style.color = "white";
        btnEliminar.style.border = "none";
        btnEliminar.style.borderRadius = "6px";
        btnEliminar.style.cursor = "pointer";

        btnEliminar.onclick = () => eliminarDelCarrito(item);

        li.appendChild(texto);
        li.appendChild(btnEliminar);
        lista.appendChild(li);
      });

      const totalGeneral = carrito.reduce((acc, item) => acc + (item.precio * item.cantidad), 0);
      const totalLi = document.createElement("li");
      totalLi.style.fontWeight = "700";
      totalLi.style.marginTop = "1rem";
      totalLi.textContent = `Total: €${totalGeneral.toLocaleString()}`;
      lista.appendChild(totalLi);

      // Añadir botón "Realizar compra"
      const btnComprarTodo = document.createElement("button");
      btnComprarTodo.textContent = "Realizar compra";
      btnComprarTodo.style.marginTop = "1rem";
      btnComprarTodo.style.padding = "0.7rem 1.3rem";
      btnComprarTodo.style.backgroundColor = "#28a745";
      btnComprarTodo.style.color = "white";
      btnComprarTodo.style.border = "none";
      btnComprarTodo.style.borderRadius = "25px";
      btnComprarTodo.style.cursor = "pointer";
      btnComprarTodo.style.fontWeight = "700";
      btnComprarTodo.style.width = "100%";
      btnComprarTodo.style.transition = "background-color 0.3s ease";
      btnComprarTodo.onclick = procesarCompraCarrito; // Llama a la nueva función
      lista.appendChild(btnComprarTodo);
    }

    if (toggle) {
      carritoVisible = !carritoVisible;
      resumen.style.display = carritoVisible ? "block" : "none";
    } else {
      resumen.style.display = "block";
      carritoVisible = true;
    }
  }

  // Nueva función para procesar la compra de todos los elementos del carrito
  async function procesarCompraCarrito() {
    const idUsuario = await obtenerIdUsuario();
    if (!idUsuario) {
      alert("Debes iniciar sesión para realizar la compra.");
      return;
    }

    if (carrito.length === 0) {
      alert("Tu carrito está vacío.");
      return;
    }

    let comprasExitosas = 0;
    let comprasFallidas = 0;

    for (const item of carrito) {
      // Para cada cantidad del mismo artículo en el carrito
      for (let i = 0; i < item.cantidad; i++) {
        console.log(`Procesando ${item.marca} ${item.modelo}. ID: ${item.id}, Precio: ${item.precio}`);
        try {
          const response = await fetch('procesarCompra.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
              id_usuario: idUsuario,
              id_coche: item.id,
              precio_coche: item.precio // Asegúrate de que el precio esté disponible en el objeto item
            })
          });
          const data = await response.json();

          if (data.error) {
            console.error(`Error al procesar la compra de ${item.marca} ${item.modelo}:`, data.error);
            comprasFallidas++;
          } else {
            comprasExitosas++;
          }
        } catch (error) {
          console.error(`Error de red al procesar la compra de ${item.marca} ${item.modelo}:`, error);
          comprasFallidas++;
        }
      }
    }

    if (comprasExitosas > 0) {
      alert(`Compra realizada con éxito para ${comprasExitosas} artículo(s).`);
      vaciarCarrito(); // Vaciar el carrito después de la compra exitosa
      cerrarModal(); // Cerrar el modal del carrito si está abierto
      obtenerCoches(); // Actualizar la lista de coches para reflejar los vendidos
    }

    if (comprasFallidas > 0) {
      alert(`Hubo errores al procesar la compra de ${comprasFallidas} artículo(s). Revisa la consola para más detalles.`);
    }
  }

  function vaciarCarrito() {
    carrito = [];
    actualizarContador();
    mostrarResumen();
  }

  // Eventos
  document.getElementById("carrito").addEventListener("click", () => mostrarResumen(true));
  document.getElementById("vaciarCarritoBtn").addEventListener("click", vaciarCarrito);
});
