<?php

if(session_status() == PHP_SESSION_NONE){
    session_start();
}

//comprobar si el usuario ya está logueado
if(!isset($_SESSION['id_usuario'])){
    //si está logueado redirigir a index
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<!-- Declara un documento HTML5. -->

<html lang="es">
<!-- Define el elemento raíz del documento con el idioma español. -->

<head>
  <meta charset="UTF-8" />
  <!-- Establece la codificación de caracteres UTF-8 para soportar caracteres especiales como tildes. -->

  <title>Catálogo de Coches</title>
  <!-- Define el título de la página que se muestra en la pestaña del navegador. -->

  <style>
    /* Define estilos CSS para la interfaz de usuario. */

    /* Estilos generales */
    body {
      margin: 0;
      /* Elimina los márgenes por defecto del navegador. */
      background: #f2f2f2;
      /* Establece un fondo gris claro para la página. */
      min-height: 100vh;
      /* Asegura que el cuerpo ocupe al menos toda la altura de la ventana. */
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      /* Define una familia de fuentes moderna y legible. */
      overflow-y: auto;
      /* Permite el desplazamiento vertical si el contenido excede la ventana. */
      color: #333;
      /* Establece el color del texto por defecto a un gris oscuro. */
    }

    .contenido-central {
      display: flex;
      flex-direction: column;
      align-items: center;
      /* Organiza el contenido en una columna, centrado horizontalmente. */
      padding: 1rem;
      /* Añade un relleno de 1rem alrededor del contenido. */
      max-width: 1200px;
      /* Limita el ancho máximo del contenedor a 1200 píxeles. */
      margin: 0 auto;
      /* Centra el contenedor horizontalmente en la página. */
      gap: 1rem;
      /* Añade un espacio de 1rem entre los elementos hijos. */
    }

    #coches-destacados {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      /* Crea una cuadrícula flexible que ajusta las columnas automáticamente,
         con un ancho mínimo de 250px por columna. */
      gap: 2rem;
      /* Añade un espacio de 2rem entre los elementos de la cuadrícula. */
      width: 100%;
      /* Ocupa todo el ancho disponible del contenedor padre. */
      max-width: 900px;
      /* Limita el ancho máximo de la cuadrícula a 900 píxeles. */
    }

    .coche-card {
      background: linear-gradient(135deg, #ffffff 0%, #e0e6f8 100%);
      /* Fondo con un degradado suave de blanco a azul claro. */
      border-radius: 15px;
      /* Añade esquinas redondeadas a las tarjetas de coches. */
      box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
      /* Añade una sombra suave para dar profundidad. */
      padding: 1rem;
      /* Añade un relleno interno de 1rem. */
      width: 100%;
      /* Ocupa todo el ancho disponible dentro de la cuadrícula. */
      cursor: pointer;
      /* Cambia el cursor a una mano para indicar que es interactivo. */
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      /* Añade transiciones suaves para transformaciones y sombras. */
      display: flex;
      flex-direction: column;
      align-items: center;
      /* Organiza el contenido de la tarjeta en una columna, centrado. */
    }

    .coche-card img {
      width: 100%;
      /* La imagen ocupa todo el ancho de la tarjeta. */
      height: 160px;
      /* Fija la altura de la imagen a 160 píxeles. */
      border-radius: 12px;
      /* Añade esquinas redondeadas a la imagen. */
      margin-bottom: 1rem;
      /* Añade un margen inferior de 1rem. */
      box-shadow: 0 4px 8px rgba(0,0,0,0.15);
      /* Añade una sombra suave a la imagen. */
      object-fit: cover;
      /* Asegura que la imagen cubra el área sin distorsionarse. */
      aspect-ratio: 3/2;
      /* Mantiene una proporción de 3:2 para la imagen. */
    }

    .coche-card:hover {
      transform: translateY(-5px);
      /* Eleva la tarjeta 5 píxeles hacia arriba al pasar el ratón. */
      box-shadow: 0 15px 25px rgba(0, 123, 255, 0.3);
      /* Aumenta la sombra con un tono azul al pasar el ratón. */
    }

    .coche-card h3 {
      margin: 0 0 0.5rem;
      /* Elimina márgenes superior/laterales y añade 0.5rem inferior. */
      font-weight: 700;
      /* Texto en negrita para el título. */
      color: #0d47a1;
      /* Color azul oscuro para el título. */
      text-align: center;
      /* Centra el texto del título. */
    }

    #toggleCatalogoBtn {
      background-color: #007BFF;
      /* Fondo azul para el botón. */
      color: white;
      /* Texto blanco. */
      border: none;
      /* Elimina el borde por defecto. */
      padding: 0.6rem 1.3rem;
      /* Añade relleno al botón. */
      font-size: 1.15rem;
      /* Tamaño de fuente ligeramente mayor. */
      font-weight: 700;
      /* Texto en negrita. */
      border-radius: 25px;
      /* Esquinas muy redondeadas. */
      cursor: pointer;
      /* Cursor de mano para indicar interactividad. */
      transition: background-color 0.3s ease, box-shadow 0.3s ease, transform 0.15s ease;
      /* Transiciones suaves para fondo, sombra y transformación. */
      box-shadow: 0 6px 12px rgba(0,123,255,0.35);
      /* Sombra azul suave. */
      user-select: none;
      /* Evita que el texto del botón sea seleccionable. */
      max-width: 220px;
      /* Limita el ancho máximo del botón. */
      text-align: center;
      /* Centra el texto dentro del botón. */
      letter-spacing: 0.03em;
      /* Añade un ligero espaciado entre letras. */
      margin-bottom: 1rem;
      /* Añade un margen inferior. */
    }

    #toggleCatalogoBtn:hover {
      background-color: #0056b3;
      /* Fondo azul más oscuro al pasar el ratón. */
      box-shadow: 0 8px 20px rgba(0,86,179,0.6);
      /* Sombra más intensa al pasar el ratón. */
      transform: translateY(-2px);
      /* Eleva el botón ligeramente al pasar el ratón. */
    }

    #catalogo-coches {
      display: none;
      /* Oculta el catálogo completo por defecto. */
      grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
      /* Cuadrícula flexible con columnas de al menos 140px. */
      gap: 1.2rem;
      /* Espacio de 1.2rem entre elementos de la cuadrícula. */
      margin-top: 0;
      /* Sin margen superior. */
      width: 120%;
      /* Ajusta el ancho al 120% del contenedor (puede necesitar revisión). */
      max-width: 900px;
      /* Limita el ancho máximo a 900 píxeles. */
    }

    .catalogo-card {
      background: #fff;
      /* Fondo blanco para las tarjetas del catálogo. */
      border-radius: 12px;
      /* Esquinas redondeadas. */
      box-shadow: 0 6px 12px rgba(0,0,0,0.08);
      /* Sombra suave. */
      padding: 0.8rem 0.6rem;
      /* Relleno interno. */
      cursor: pointer;
      /* Cursor de mano para interactividad. */
      text-align: center;
      /* Centra el texto. */
      transition: transform 0.25s ease, box-shadow 0.25s ease;
      /* Transiciones suaves para transformación y sombra. */
      display: flex;
      flex-direction: column;
      align-items: center;
      /* Organiza el contenido en una columna, centrado. */
    }

    .catalogo-card img {
      width: 70%;
      /* La imagen ocupa el 70% del ancho de la tarjeta. */
      height: 100px;
      /* Altura fija de 100 píxeles. */
      border-radius: 10px;
      /* Esquinas redondeadas. */
      margin-bottom: 0.5rem;
      /* Margen inferior. */
      object-fit: contain;
      /* Muestra la imagen completa sin recortarla. */
      box-shadow: 0 3px 8px rgba(0,0,0,0.1);
      /* Sombra suave para la imagen. */
    }

    .catalogo-card h4 {
      margin: 0 0 0.25rem;
      /* Margen inferior pequeño. */
      font-weight: 600;
      /* Texto semi-negrita. */
      font-size: 1rem;
      /* Tamaño de fuente estándar. */
      color: #0b3d91;
      /* Color azul oscuro. */
    }

    .catalogo-card p {
      margin: 0;
      /* Sin márgenes. */
      font-weight: 500;
      /* Texto medio. */
      color: #2a2a2a;
      /* Color gris oscuro. */
      font-size: 0.9rem;
      /* Tamaño de fuente más pequeño. */
    }

    .catalogo-card:hover {
      transform: translateY(-6px);
      /* Eleva la tarjeta al pasar el ratón. */
      box-shadow: 0 18px 30px rgba(0, 123, 255, 0.35);
      /* Sombra azul más intensa al pasar el ratón. */
    }

    #filtros-catalogo {
      display: none;
      /* Oculta los filtros por defecto. */
      gap: 1rem;
      /* Espacio entre elementos de los filtros. */
      margin-bottom: 1rem;
      /* Margen inferior. */
      flex-wrap: wrap;
      /* Permite que los filtros se ajusten a múltiples líneas si es necesario. */
      justify-content: center;
      /* Centra los filtros horizontalmente. */
      max-width: 900px;
      /* Ancho máximo de 900 píxeles. */
      width: 100%;
      /* Ocupa todo el ancho disponible. */
    }

    #filtros-catalogo select {
      padding: 0.4rem 0.8rem;
      /* Relleno interno para los menús desplegables. */
      font-size: 1rem;
      /* Tamaño de fuente estándar. */
      border-radius: 8px;
      /* Esquinas redondeadas. */
      border: 1px solid #ccc;
      /* Borde gris claro. */
      min-width: 140px;
      /* Ancho mínimo para los menús desplegables. */
    }

    #modalCoche {
      position: fixed;
      /* Fija el modal en la pantalla. */
      z-index: 10000;
      /* Asegura que el modal esté por encima de otros elementos. */
      left: 0;
      top: 0;
      width: 100vw;
      height: 100vh;
      /* Cubre toda la ventana del navegador. */
      background-color: rgba(0,0,0,0.6);
      /* Fondo oscuro semitransparente para el modal. */
      display: none;
      /* Oculta el modal por defecto. */
      align-items: center;
      justify-content: center;
      /* Centra el contenido del modal. */
      padding: 1rem;
      /* Relleno para evitar que el contenido toque los bordes. */
      overflow-y: auto;
      /* Permite desplazamiento vertical si el contenido excede la ventana. */
    }

    .modal-content {
      background-color: #ffffff;
      /* Fondo blanco para el contenido del modal. */
      padding: 2rem;
      /* Relleno interno generoso. */
      border-radius: 20px;
      /* Esquinas redondeadas. */
      width: 90%;
      /* Ocupa el 90% del ancho disponible. */
      max-width: 520px;
      /* Limita el ancho máximo a 520 píxeles. */
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
      /* Sombra pronunciada para profundidad. */
      position: relative;
      /* Permite posicionar elementos hijos (como el botón de cerrar) relativos al modal. */
      max-height: 90vh;
      /* Limita la altura máxima a 90% de la ventana. */
      overflow-y: auto;
      /* Permite desplazamiento vertical dentro del modal. */
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      /* Fuente consistente con el resto de la página. */
      color: #222;
      /* Color de texto oscuro. */
      line-height: 1.4;
      /* Espaciado entre líneas para mejor legibilidad. */
    }

    .modal-content img {
      width: 100%;
      /* La imagen ocupa todo el ancho del modal. */
      max-height: 280px;
      /* Limita la altura máxima de la imagen. */
      object-fit: cover;
      /* Cubre el área sin distorsionarse. */
      border-radius: 15px;
      /* Esquinas redondeadas. */
      margin-bottom: 1rem;
      /* Margen inferior. */
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
      /* Sombra suave para la imagen. */
    }

    .modal-content h2 {
      margin-top: 0;
      margin-bottom: 0.8rem;
      /* Ajusta márgenes para el título del modal. */
      color: #0a357e;
      /* Color azul oscuro para el título. */
      font-weight: 700;
      /* Texto en negrita. */
    }

    .modal-content p {
      margin: 0.3rem 0;
      /* Márgenes pequeños para los párrafos. */
      font-size: 1rem;
      /* Tamaño de fuente estándar. */
      font-weight: 500;
      /* Texto medio. */
      color: #444;
      /* Color gris oscuro para el texto. */
    }

    .cerrar {
      color: #888;
      /* Color gris para el botón de cerrar. */
      position: absolute;
      top: 1rem;
      right: 1.5rem;
      /* Posiciona el botón en la esquina superior derecha. */
      font-size: 30px;
      /* Tamaño grande para el símbolo de cerrar. */
      font-weight: 900;
      /* Texto muy grueso. */
      cursor: pointer;
      /* Cursor de mano para interactividad. */
      user-select: none;
      /* Evita que el símbolo sea seleccionable. */
      transition: color 0.25s ease;
      /* Transición suave para el cambio de color. */
    }

    .cerrar:hover {
      color: #0b3d91;
      /* Color azul oscuro al pasar el ratón. */
    }

    /* Carrito estilos */
    #carrito {
      position: fixed;
      top: 3.8rem;
      right: 1rem;
      /* Fija el carrito en la esquina superior derecha. */
      background: #007BFF;
      /* Fondo azul. */
      color: white;
      /* Texto blanco. */
      padding: 0.6rem 1rem;
      /* Relleno interno. */
      border-radius: 25px;
      /* Esquinas muy redondeadas. */
      cursor: pointer;
      /* Cursor de mano. */
      user-select: none;
      /* Evita selección de texto. */
      z-index: 11000;
      /* Asegura que esté por encima de otros elementos. */
      box-shadow: 0 6px 12px rgba(0,123,255,0.5);
      /* Sombra azul suave. */
      font-weight: 700;
      /* Texto en negrita. */
      display: flex;
      align-items: center;
      gap: 0.5rem;
      /* Organiza el contenido del carrito con un pequeño espacio entre elementos. */
    }

    #resumenCarrito {
      position: fixed;
      top: 3.5rem;
      right: 1rem;
      /* Fija el resumen del carrito en la esquina superior derecha. */
      background: white;
      /* Fondo blanco. */
      color: #222;
      /* Texto oscuro. */
      width: 320px;
      /* Ancho fijo. */
      max-height: 400px;
      /* Altura máxima con desplazamiento. */
      overflow-y: auto;
      /* Permite desplazamiento vertical. */
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
      /* Sombra pronunciada. */
      border-radius: 15px;
      /* Esquinas redondeadas. */
      padding: 1rem;
      /* Relleno interno. */
      display: none;
      /* Oculta el resumen por defecto. */
      z-index: 11000;
      /* Asegura que esté por encima de otros elementos. */
    }

    #resumenCarrito h3 {
      margin-top: 0;
      margin-bottom: 1rem;
      /* Ajusta márgenes para el título del carrito. */
    }

    .cerrar-carrito {
      color: #888;
      /* Color gris para el botón de cerrar. */
      position: absolute;
      top: 0.5rem;
      right: 1rem;
      /* Posiciona en la esquina superior derecha. */
      font-size: 24px;
      /* Tamaño del símbolo. */
      font-weight: 700;
      /* Texto en negrita. */
      cursor: pointer;
      /* Cursor de mano. */
    }

    .cerrar-carrito:hover {
      color: #333;
      /* Color más oscuro al pasar el ratón. */
    }

    #resumenCarrito ul {
      list-style: none;
      padding-left: 0;
      /* Elimina estilos de lista y relleno. */
      max-height: 300px;
      /* Altura máxima con desplazamiento. */
      overflow-y: auto;
      /* Permite desplazamiento vertical. */
      margin-bottom: 1rem;
      /* Margen inferior. */
    }

    #resumenCarrito ul li {
      padding: 0.3rem 0;
      /* Relleno vertical para los elementos de la lista. */
      border-bottom: 1px solid #ccc;
      /* Línea divisoria entre elementos. */
      font-weight: 600;
      /* Texto semi-negrita. */
      font-size: 0.95rem;
      /* Tamaño de fuente pequeño. */
    }

    #vaciarCarritoBtn {
      background-color: #dc3545;
      /* Fondo rojo para el botón de vaciar. */
      color: white;
      /* Texto blanco. */
      border: none;
      /* Sin borde. */
      padding: 0.7rem 1.3rem;
      /* Relleno interno. */
      font-weight: 600;
      /* Texto semi-negrita. */
      border-radius: 12px;
      /* Esquinas redondeadas. */
      cursor: pointer;
      /* Cursor de mano. */
      width: 100%;
      /* Ocupa todo el ancho. */
      transition: background-color 0.25s ease;
      /* Transición suave para el fondo. */
    }

    #vaciarCarritoBtn:hover {
      background-color: #b02a37;
      /* Fondo rojo más oscuro al pasar el ratón. */
    }

    .footer {
      background-color: #0d47a1;
      /* Fondo azul oscuro para el pie de página. */
      color: #fff;
      /* Texto blanco. */
      padding: 2rem 1rem;
      /* Relleno generoso. */
      text-align: center;
      /* Centra el texto. */
      box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.15);
      /* Sombra hacia arriba para profundidad. */
      margin-top: 2rem;
      /* Margen superior para separar del contenido. */
      font-size: 1rem;
      /* Tamaño de fuente estándar. */
    }

    .footer-contenido {
      max-width: 1000px;
      /* Ancho máximo del contenido del pie. */
      margin: 0 auto;
      /* Centra el contenido horizontalmente. */
      display: flex;
      flex-direction: column;
      gap: 1rem;
      align-items: center;
      /* Organiza el contenido en una columna, centrado. */
    }

    .footer-redes {
      display: flex;
      gap: 1.2rem;
      justify-content: center;
      align-items: center;
      /* Organiza los íconos de redes sociales en una fila, centrados. */
    }

    .footer-redes img {
      width: 32px;
      height: 32px;
      /* Tamaño fijo para los íconos. */
      filter: brightness(0) invert(1);
      /* Convierte los íconos a blanco para contraste. */
      opacity: 0.9;
      /* Ligeramente transparente. */
    }

    .footer-contacto p {
      margin: 0;
      /* Sin márgenes. */
      font-weight: 500;
      /* Texto medio. */
      color: #e3f2fd;
      /* Color azul claro para el texto. */
    }

    /* Barra de navegación */
    .navbar {
      position: fixed;
      top: 0;
      width: 100%;
      /* Fija la barra de navegación en la parte superior, ocupando todo el ancho. */
      background-color: #0d47a1;
      /* Fondo azul oscuro. */
      color: #fff;
      /* Texto blanco. */
      padding: 0.8rem 1.5rem;
      /* Relleno interno. */
      box-shadow: 0 2px 12px rgba(0, 0, 0, 0.2);
      /* Sombra para profundidad. */
      z-index: 1000;
      /* Asegura que esté por encima de otros elementos. */
    }

    .navbar-contenido {
      max-width: 1200px;
      margin: 0 auto;
      /* Centra el contenido de la barra. */
      display: flex;
      justify-content: space-between;
      align-items: center;
      /* Organiza el logo y los enlaces en una fila, espaciados. */
    }

    .navbar .logo {
      font-size: 1.3rem;
      font-weight: 700;
      /* Estiliza el logo con texto grande y negrita. */
    }

    .navbar-links {
      list-style: none;
      display: flex;
      gap: 2rem;
      margin: 0;
      padding: 0;
      /* Organiza los enlaces en una fila sin estilos de lista. */
    }

    .navbar-links li a {
      color: #e3f2fd;
      /* Color azul claro para los enlaces. */
      text-decoration: none;
      /* Sin subrayado. */
      font-weight: 600;
      /* Texto semi-negrita. */
      transition: color 0.3s ease;
      /* Transición suave para el cambio de color. */
    }

    .navbar-links li a:hover {
      color: #ffffff;
      /* Color blanco al pasar el ratón. */
    }

    /* Espaciado para que el contenido no quede oculto por la barra fija */
    body {
      scroll-behavior: smooth;
      /* Desplazamiento suave al navegar con enlaces ancla. */
      padding-top: 70px;
      /* Espacio superior para evitar que la barra fija cubra el contenido. */
    }

    #logoutBtn {
      position: fixed;
      top: 65px;
      left: 20px;
      /* Fija el botón de cerrar sesión en la esquina superior izquierda. */
      padding: 8px 16px;
      /* Relleno interno. */
      background-color: #dc3545;
      /* Fondo rojo. */
      color: white;
      /* Texto blanco. */
      border: none;
      /* Sin borde. */
      border-radius: 6px;
      /* Esquinas redondeadas. */
      cursor: pointer;
      /* Cursor de mano. */
      font-weight: 600;
      /* Texto semi-negrita. */
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
      /* Sombra suave. */
      z-index: 1000;
      /* Asegura que esté por encima de otros elementos. */
      transition: background-color 0.3s ease;
      /* Transición suave para el fondo. */
    }

    #logoutBtn:hover {
      background-color: #b02a37;
      /* Fondo rojo más oscuro al pasar el ratón. */
    }

    @keyframes fadeOut {
      from { opacity: 1; }
      to { opacity: 0; }
    }

    .fade-out {
      animation: fadeOut 2s forwards;
    }
  </style>
</head>

<body>
  <!-- Comienza el contenido visible de la página HTML. -->

  <nav class="navbar">
    <!-- Barra de navegación fija en la parte superior. -->
    <div class="navbar-contenido">
      <span class="logo">Catálogo de Coches</span>
      <!-- Logo de la página. -->
      <ul class="navbar-links">
        <li><a href="#inicio">Inicio</a></li>
        <li><a href="#catalogo">Catálogo</a></li>
        <li><a href="#contacto">Contacto</a></li>
        <!-- Enlaces de navegación a secciones de la página. -->
      </ul>
    </div>
  </nav>

  <div id="welcome-message" style="text-align: center; padding: 10px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px auto; max-width: 80%;">
        <?php
        if (isset($_SESSION['user_name'])) {
            echo "¡Bienvenido, " . htmlspecialchars($_SESSION['user_name']) . "!";
        }
        ?>
    </div>

  <button id="logoutBtn">Cerrar sesión</button>
  <!-- Botón para cerrar sesión, fijo en la esquina superior izquierda. -->

  <div class="contenido-central" id="inicio">
    <!-- Contenedor principal para el contenido, con ID para navegación ancla. -->
    <h1>Coches más destacados</h1>
    <!-- Título para la sección de coches destacados. -->
    <div id="coches-destacados"></div>
    <!-- Contenedeor para las tarjetas de coches destacados, llenado dinámicamente con JavaScript. -->

    <h2 id="catalogo">Catálogo de coches</h2>
    <!-- Título para la sección del catálogo completo, con ID para navegación ancla. -->
    <button id="toggleCatalogoBtn">Ver catálogo completo</button>
    <!-- Botón para mostrar u ocultar el catálogo completo. -->

    <div id="filtros-catalogo"></div>
    <!-- Contenedor para los filtros del catálogo, llenado dinámicamente con JavaScript. -->

    <div id="catalogo-coches"></div>
    <!-- Contenedor para las tarjetas del catálogo completo, llenado dinámicamente con JavaScript. -->
  </div>

  <!-- Carrito fijo arriba a la derecha -->
  <div id="carrito">🛒 <span id="contadorCarrito">0</span> coches</div>
  <!-- Icono y contador del carrito, fijo en la esquina superior derecha. -->

  <div id="resumenCarrito">
    <!-- Contenedor para el resumen del carrito, oculto por defecto. -->
    <span class="cerrar-carrito" title="Cerrar">×</span>
    <!-- Botón para cerrar el resumen del carrito. -->
    <h3>Carrito de compra</h3>
    <!-- Título del resumen del carrito. -->
    <ul id="listaCarrito"></ul>
    <!-- Lista para mostrar los elementos del carrito, llenada dinámicamente. -->
    <button id="vaciarCarritoBtn">Vaciar carrito</button>
    <!-- Botón para vaciar el carrito. -->
  </div>

  <!-- Modal -->
  <div id="modalCoche">
    <!-- Contenedor para el modal de detalles del coche, oculto por defecto. -->
    <div class="modal-content">
      <span class="cerrar" title="Cerrar modal">×</span>
      <!-- Botón para cerrar el modal. -->
      <div id="contenidoModal"></div>
      <!-- Contenedor para el contenido del modal, llenado dinámicamente con JavaScript. -->
    </div>
  </div>

  <footer class="footer" id="contacto">
    <!-- Pie de página con ID para navegación ancla. -->
    <div class="footer-contenido">
      <p>© 2025 Catálogo de Coches. Todos los derechos reservados.</p>
      <!-- Texto de derechos de autor. -->
      <div class="footer-redes">
        <img src="https://img.icons8.com/ios-filled/50/ffffff/instagram-new.png" alt="Instagram" />
        <img src="https://img.icons8.com/ios-filled/50/ffffff/gmail.png" alt="Gmail" />
        <!-- Iconos de redes sociales (Instagram y Gmail). -->
      </div>
      <div class="footer-contacto">
        <p><strong>Contacto:</strong> +34 662 906 545</p>
        <!-- Información de contacto. -->
      </div>
    </div>
  </footer>

  <script>
    /* Script JavaScript para manejar el botón de cerrar sesión. */
    document.getElementById('logoutBtn').addEventListener('click', () => {
      /* Añade un evento de clic al botón de cerrar sesión. */
      window.location.href = 'logout.php';
      /* Redirige al usuario a logout.php para cerrar la sesión. */
    });
  </script>

  <script src="funciones.js"></script>
  <!-- Incluye un archivo JavaScript externo (funciones.js) que contiene
       el código para cargar coches, manejar el catálogo, filtros, carrito y modal. -->

  <script>
    /* Script para ocultar el mensaje de bienvenida después de 3 segundos con animación. */
    document.addEventListener('DOMContentLoaded', () => {
      const welcomeMessage = document.getElementById('welcome-message');
      if (welcomeMessage) {
        setTimeout(() => {
          welcomeMessage.classList.add('fade-out');
          // Espera a que la animación termine para ocultar el elemento
          setTimeout(() => {
            welcomeMessage.style.display = 'none';
          }, 2000); // Duración de la animación
        }, 3000); // 3000 milisegundos = 3 segundos
      }
    });
  </script>
</body>
</html>