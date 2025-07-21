
// Espera a que la página esté completamente cargada antes de ejecutar el código
document.addEventListener("DOMContentLoaded", function () {
  // --- Variables para elementos de la página ---
  // Busca en la página un elemento con id "coches-destacados" para mostrar los coches principales
  const destacadosContenedor = document.getElementById("coches-destacados");
  // Busca el contenedor donde se mostrará el catálogo completo de coches
  const catalogoContenedor = document.getElementById("catalogo-coches");
  // Busca el contenedor donde estarán los filtros (como marca o precio)
  const filtrosContenedor = document.getElementById("filtros-catalogo");
  // Busca el botón que muestra u oculta el catálogo completo
  const toggleCatalogoBtn = document.getElementById("toggleCatalogoBtn");
  // Busca el elemento que será la ventana emergente (modal) para los detalles del coche
  const modal = document.getElementById("modalCoche");
  // Busca el lugar dentro del modal donde pondremos la información del coche
  const contenidoModal = document.getElementById("contenidoModal");
  // Busca el botón con clase "cerrar" para cerrar la ventana emergente
  const cerrarModalBtn = document.querySelector(".cerrar");

  // Crea una lista vacía para guardar todos los coches que obtendremos del servidor
  let coches = [];
  // Variable que indica si el catálogo completo está visible (true) o no (false)
  let catalogoVisible = false;

  // --- Función para abrir la ventana emergente (modal) ---
  // Muestra los detalles de un coche en una ventana emergente
  function abrirModal(coche) {
    // Llena el contenido del modal con la información del coche
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
    // Explicación: Aquí creamos el contenido de la ventana emergente con los datos del coche,
    // como su marca, modelo, imagen, precio (formateado con comas), etc., y un botón para comprar.

    // Hace visible la ventana emergente cambiando su estilo a "flex"
    modal.style.display = "flex";
    // Evita que el usuario pueda desplazarse por la página mientras la ventana está abierta
    document.body.style.overflow = "hidden";

    // Busca el botón de comprar que acabamos de crear
    const btnComprar = document.getElementById("btnComprar");
    // Cuando el usuario hace clic en "Comprar", ejecuta esta función
    btnComprar.onclick = async () => {
      // Añade el coche al carrito de compras
      agregarAlCarrito(coche);
      // Muestra un mensaje al usuario confirmando que se añadió el coche
      alert(`¡Has añadido ${coche.marca} ${coche.modelo} al carrito!`);
      // Cierra la ventana emergente
      cerrarModal();
    };
  }

  // --- Función para cerrar la ventana emergente ---
  // Oculta la ventana emergente y restaura el desplazamiento de la página
  function cerrarModal() {
    // Oculta la ventana emergente
    modal.style.display = "none";
    // Permite de nuevo el desplazamiento de la página
    document.body.style.overflow = "";
  }

  // --- Función para obtener el ID del usuario ---
  // Pide al servidor el ID del usuario que está usando la página
  async function obtenerIdUsuario() {
    try {
      // Envía una solicitud al archivo "getUserId.php" en el servidor
      const response = await fetch('getUserId.php');
      // Verifica si la respuesta del servidor es correcta
      if (!response.ok) {
        // Si hay un error, lanza una excepción
        throw new Error('Error al obtener el ID del usuario');
      }
      // Convierte la respuesta del servidor a un formato que podemos usar (JSON)
      const data = await response.json();
      // Devuelve el ID del usuario
      return data.id_usuario;
    } catch (error) {
      // Si ocurre un error, lo muestra en la consola y devuelve null
      console.error("Error en obtenerIdUsuario:", error);
      return null;
    }
  }

  // --- Función para mostrar coches destacados ---
  // Muestra los primeros 6 coches en la sección de destacados
  function mostrarDestacados() {
    // Toma los primeros 6 coches de la lista
    const destacados = coches.slice(0, 6);
    // Limpia el contenedor de destacados para empezar de cero
    destacadosContenedor.innerHTML = "";

    // Recorre cada coche destacado
    destacados.forEach(coche => {
      // Crea un nuevo elemento div para mostrar el coche
      const div = document.createElement("div");
      // Le añade la clase "coche-card" para darle estilos
      div.classList.add("coche-card");
      // Llena el div con la imagen, marca, modelo y precio del coche
      div.innerHTML = `
        <img src="img/${coche.imagen}" alt="${coche.marca} ${coche.modelo}">
        <h3>${coche.marca} ${coche.modelo}</h3>
        <p>€${parseInt(coche.precio).toLocaleString()}</p>
      `;
      // Cuando el usuario hace clic en el coche, abre la ventana emergente
      div.onclick = () => abrirModal(coche);
      // Añade el div al contenedor de destacados
      destacadosContenedor.appendChild(div);
    });
  }

  // --- Función para mostrar el catálogo completo ---
  // Muestra todos los coches o los que cumplen con los filtros
  function mostrarCatalogo(cochesFiltrados) {
    // Limpia el contenedor del catálogo
    catalogoContenedor.innerHTML = "";
    // Si no hay coches filtrados, usa todos los coches
    if (!cochesFiltrados) cochesFiltrados = coches;

    // Recorre cada coche para mostrarlo
    cochesFiltrados.forEach(coche => {
      // Crea un nuevo elemento div para el coche
      const div = document.createElement("div");
      // Le añade la clase "catalogo-card" para darle estilos
      div.classList.add("catalogo-card");
      // Llena el div con la imagen, marca, modelo y precio
      div.innerHTML = `
        <img src="img/${coche.imagen}" alt="${coche.marca} ${coche.modelo}">
        <h4>${coche.marca} ${coche.modelo}</h4>
        <p>€${parseInt(coche.precio).toLocaleString()}</p>
      `;
      // Cuando el usuario hace clic, abre la ventana emergente
      div.onclick = () => abrirModal(coche);
      // Añade el div al contenedor del catálogo
      catalogoContenedor.appendChild(div);
    });
  }

  // --- Función para crear los filtros ---
  // Crea menús desplegables para filtrar los coches por marca, precio, año y estado
  function crearFiltros() {
    // Llena el contenedor de filtros con menús desplegables
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
    // Explicación: Aquí creamos menús desplegables. El de marca muestra todas las marcas
    // únicas de los coches, ordenadas alfabéticamente.

    // Añade un evento a cada menú para que al cambiarlo se apliquen los filtros
    document.getElementById("filtroMarca").addEventListener("change", filtrarCatalogo);
    document.getElementById("filtroPrecio").addEventListener("change", filtrarCatalogo);
    document.getElementById("filtroAño").addEventListener("change", filtrarCatalogo);
    document.getElementById("filtroEstado").addEventListener("change", filtrarCatalogo);
  }

  // --- Función para filtrar el catálogo ---
  // Aplica los filtros seleccionados por el usuario
  function filtrarCatalogo() {
    // Obtiene los valores seleccionados en los menús
    const marca = document.getElementById("filtroMarca").value;
    const precio = document.getElementById("filtroPrecio").value;
    const año = document.getElementById("filtroAño").value;
    const estado = document.getElementById("filtroEstado").value;

    // Crea una copia de la lista de coches para no modificar la original
    let filtrados = [...coches];

    // Si se seleccionó una marca, muestra solo los coches de esa marca
    if (marca) filtrados = filtrados.filter(c => c.marca === marca);

    // Si se seleccionó ordenar por precio, organiza la lista
    if (precio === "menor") filtrados.sort((a, b) => a.precio - b.precio);
    else if (precio === "mayor") filtrados.sort((a, b) => b.precio - a.precio);

    // Si se seleccionó ordenar por año, organiza la lista
    if (año === "nuevo") filtrados.sort((a, b) => b.año - a.año);
    else if (año === "viejo") filtrados.sort((a, b) => a.año - b.año);

    // Si se seleccionó un estado, muestra solo los coches con ese estado
    if (estado) filtrados = filtrados.filter(c => c.estado === estado);

    // Muestra en la consola los coches filtrados (para depuración)
    console.log("Coches filtrados (filtrarCatalogo):", filtrados);
    // Muestra los coches filtrados en el catálogo
    mostrarCatalogo(filtrados);
  }

  // --- Mostrar u ocultar el catálogo completo ---
  // Cambia la visibilidad del catálogo y los filtros al hacer clic en el botón
  toggleCatalogoBtn.addEventListener("click", () => {
    // Cambia el estado de visibilidad (true a false o viceversa)
    catalogoVisible = !catalogoVisible;

    // Si el catálogo está visible
    if (catalogoVisible) {
      // Muestra los filtros
      filtrosContenedor.style.display = "flex";
      // Muestra el catálogo en forma de cuadrícula
      catalogoContenedor.style.display = "grid";
      // Cambia el texto del botón
      toggleCatalogoBtn.textContent = "Ocultar catálogo completo";
      // Muestra todos los coches
      mostrarCatalogo();
      // Crea los menús de filtros
      crearFiltros();
    } else {
      // Oculta los filtros
      filtrosContenedor.style.display = "none";
      // Oculta el catálogo
      catalogoContenedor.style.display = "none";
      // Cambia el texto del botón
      toggleCatalogoBtn.textContent = "Ver catálogo completo";
    }
  });

  // --- Cerrar la ventana emergente ---
  // Cierra la ventana al hacer clic en el botón de cerrar o fuera del contenido
  cerrarModalBtn.onclick = cerrarModal;
  // Cierra la ventana si el usuario hace clic fuera del contenido del modal
  modal.onclick = function (e) {
    if (e.target === modal) cerrarModal();
  };

  // --- Función para obtener los coches del servidor ---
  // Pide la lista de coches al servidor
  async function obtenerCoches() {
    try {
      // Envía una solicitud al archivo "obtenerCoches.php"
      const response = await fetch("obtenerCoches.php");
      // Muestra la respuesta en la consola (para depuración)
      console.log("Respuesta raw de obtenerCoches.php:", response);
      // Verifica si la respuesta es correcta
      if (!response.ok) {
        throw new Error("Error HTTP al obtener coches: " + response.status);
      }
      // Convierte la respuesta a un formato que podemos usar (JSON)
      coches = await response.json();
      // Asegura que el estado de cada coche tenga la primera letra en mayúscula
      coches = coches.map(coche => ({
        ...coche,
        estado: coche.estado.charAt(0).toUpperCase() + coche.estado.slice(1).toLowerCase()
      }));
      // Muestra los coches cargados en la consola
      console.log("Coches cargados desde el servidor:", coches);

      // Muestra los coches destacados en la página
      mostrarDestacados();
    } catch (error) {
      // Si hay un error, lo muestra en la consola
      console.error("Error al cargar coches en obtenerCoches():", error);
      // Muestra un mensaje de error en la página
      destacadosContenedor.innerHTML = "<p>Error cargando coches.</p>";
    }
  }

  // Llama a la función para cargar los coches cuando la página se inicia
  obtenerCoches();

  // --- Carrito de compras ---
  // Crea una lista vacía para guardar los coches en el carrito
  let carrito = [];
  // Variable que indica si el resumen del carrito está visible
  let carritoVisible = false;

  // --- Función para añadir un coche al carrito ---
  // Añade un coche al carrito o aumenta su cantidad si ya está
  function agregarAlCarrito(auto) {
    // Busca si el coche ya está en el carrito comparando marca, modelo, año y precio
    const index = carrito.findIndex(item =>
      item.marca === auto.marca &&
      item.modelo === auto.modelo &&
      item.año === auto.año &&
      item.precio === auto.precio
    );

    // Si el coche ya está en el carrito, aumenta su cantidad
    if (index >= 0) {
      carrito[index].cantidad++;
    } else {
      // Si no está, lo añade con cantidad 1
      carrito.push({ ...auto, cantidad: 1 });
    }

    // Actualiza el número que muestra cuántos ítems hay en el carrito
    actualizarContador();
    // Muestra el resumen del carrito
    mostrarResumen();
  }

  // --- Función para eliminar un coche del carrito ---
  // Quita un coche del carrito
  function eliminarDelCarrito(auto) {
    // Crea una nueva lista sin el coche especificado
    carrito = carrito.filter(item =>
      !(item.marca === auto.marca &&
        item.modelo === auto.modelo &&
        item.año === auto.año &&
        item.precio === auto.precio)
    );

    // Actualiza el contador del carrito
    actualizarContador();
    // Muestra el resumen actualizado
    mostrarResumen();
  }

  // --- Función para actualizar el contador del carrito ---
  // Muestra cuántos ítems hay en el carrito
  function actualizarContador() {
    // Busca el elemento que muestra el contador
    const contador = document.getElementById("contadorCarrito");
    // Suma la cantidad de todos los ítems en el carrito
    const totalCantidad = carrito.reduce((acc, item) => acc + item.cantidad, 0);
    // Muestra el total o nada si el carrito está vacío
    contador.textContent = totalCantidad > 0 ? totalCantidad : "";
  }

  // --- Función para mostrar el resumen del carrito ---
  // Muestra los ítems del carrito y el total
  function mostrarResumen(toggle = false) {
    // Busca el contenedor del resumen y la lista de ítems
    const resumen = document.getElementById("resumenCarrito");
    const lista = document.getElementById("listaCarrito");
    // Limpia la lista
    lista.innerHTML = "";

    // Si el carrito está vacío, muestra un mensaje
    if (carrito.length === 0) {
      lista.innerHTML = "<li>Tu carrito está vacío.</li>";
    } else {
      // Recorre cada ítem del carrito
      carrito.forEach(item => {
        // Crea un elemento de lista para el ítem
        const li = document.createElement("li");
        // Añade estilos para que se vea bien
        li.style.marginBottom = "0.5rem";
        li.style.display = "flex";
        li.style.justifyContent = "space-between";
        li.style.alignItems = "center";

        // Crea un texto con los detalles del ítem
        const texto = document.createElement("span");
        texto.textContent = `${item.marca} ${item.modelo} (${item.año}) x${item.cantidad} - €${(item.precio * item.cantidad).toLocaleString()}`;

        // Crea un botón para eliminar el ítem
        const btnEliminar = document.createElement("button");
        btnEliminar.textContent = "Eliminar";
        // Añade estilos al botón
        btnEliminar.style.padding = "0.2rem 0.5rem";
        btnEliminar.style.fontSize = "0.8rem";
        btnEliminar.style.backgroundColor = "#dc3545";
        btnEliminar.style.color = "white";
        btnEliminar.style.border = "none";
        btnEliminar.style.borderRadius = "6px";
        btnEliminar.style.cursor = "pointer";

        // Cuando se hace clic en el botón, elimina el ítem
        btnEliminar.onclick = () => eliminarDelCarrito(item);

        // Añade el texto y el botón al elemento de lista
        li.appendChild(texto);
        li.appendChild(btnEliminar);
        // Añade el elemento a la lista
        lista.appendChild(li);
      });

      // Calcula el precio total de todos los ítems
      const totalGeneral = carrito.reduce((acc, item) => acc + (item.precio * item.cantidad), 0);
      // Crea un elemento para mostrar el total
      const totalLi = document.createElement("li");
      totalLi.style.fontWeight = "700";
      totalLi.style.marginTop = "1rem";
      totalLi.textContent = `Total: €${totalGeneral.toLocaleString()}`;
      // Añade el total a la lista
      lista.appendChild(totalLi);

      // Crea un botón para realizar la compra
      const btnComprarTodo = document.createElement("button");
      btnComprarTodo.textContent = "Realizar compra";
      // Añade estilos al botón
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
      // Cuando se hace clic, procesa la compra
      btnComprarTodo.onclick = procesarCompraCarrito;
      // Añade el botón a la lista
      lista.appendChild(btnComprarTodo);
    }

    // Muestra u oculta el resumen según el parámetro toggle
    if (toggle) {
      carritoVisible = !carritoVisible;
      resumen.style.display = carritoVisible ? "block" : "none";
    } else {
      resumen.style.display = "block";
      carritoVisible = true;
    }
  }

  // --- Función para procesar la compra ---
  // Envía los ítems del carrito al servidor para procesar la compra
  async function procesarCompraCarrito() {
    // Obtiene el ID del usuario
    const idUsuario = await obtenerIdUsuario();
    // Si no hay ID, muestra un mensaje y termina
    if (!idUsuario) {
      alert("Debes iniciar sesión para realizar la compra.");
      return;
    }

    // Si el carrito está vacío, muestra un mensaje y termina
    if (carrito.length === 0) {
      alert("Tu carrito está vacío.");
      return;
    }

    // Contadores para seguir el éxito o fallo de las compras
    let comprasExitosas = 0;
    let comprasFallidas = 0;

    // Recorre cada ítem del carrito
    for (const item of carrito) {
      // Procesa cada cantidad del ítem
      for (let i = 0; i < item.cantidad; i++) {
        // Muestra en la consola qué coche se está procesando
        console.log(`Procesando ${item.marca} ${item.modelo}. ID: ${item.id}, Precio: ${item.precio}`);
        try {
          // Envía una solicitud al servidor para procesar la compra
          const response = await fetch('procesarCompra.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              id_usuario: idUsuario,
              id_coche: item.id,
              precio_coche: item.precio
            })
          });
          // Convierte la respuesta a JSON
          const data = await response.json();

          // Si hay un error en la respuesta, lo registra
          if (data.error) {
            console.error(`Error al procesar la compra de ${item.marca} ${item.modelo}:`, data.error);
            comprasFallidas++;
          } else {
            // Si no hay error, incrementa el contador de éxito
            comprasExitosas++;
          }
        } catch (error) {
          // Si hay un error de red, lo registra
          console.error(`Error de red al procesar la compra de ${item.marca} ${item.modelo}:`, error);
          comprasFallidas++;
        }
      }
    }

    // Si hubo compras exitosas, muestra un mensaje y limpia el carrito
    if (comprasExitosas > 0) {
      alert(`Compra realizada con éxito para ${comprasExitosas} artículo(s).`);
      // Vacía el carrito
      vaciarCarrito();
      // Cierra la ventana emergente
      cerrarModal();
      // Vuelve a cargar los coches para actualizar la lista
      obtenerCoches();
    }

    // Si hubo compras fallidas, muestra un mensaje
    if (comprasFallidas > 0) {
      alert(`Hubo errores al procesar la compra de ${comprasFallidas} artículo(s). Revisa la consola para más detalles.`);
    }
  }

  // --- Función para vaciar el carrito ---
  // Elimina todos los ítems del carrito
  function vaciarCarrito() {
    // Reinicia la lista del carrito
    carrito = [];
    // Actualiza el contador
    actualizarContador();
    // Muestra el resumen vacío
    mostrarResumen();
  }

  // --- Eventos de la página ---
  // Muestra el resumen del carrito al hacer clic en el ícono del carrito
  document.getElementById("carrito").addEventListener("click", () => mostrarResumen(true));
  // Vacía el carrito al hacer clic en el botón de vaciar
  document.getElementById("vaciarCarritoBtn").addEventListener("click", vaciarCarrito);
  // Cierra el resumen del carrito al hacer clic en el botón de cerrar
  document.querySelector(".cerrar-carrito").addEventListener("click", () => {
    document.getElementById("resumenCarrito").style.display = "none";
    carritoVisible = false;
  });
});