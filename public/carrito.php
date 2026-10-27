<?php

session_start();

require_once dirname(__DIR__) . '/config/database.php';


/* =========================================================
   OBTENER CARRITO
========================================================= */

$carrito = isset($_SESSION['carrito'])
    ? $_SESSION['carrito']
    : [];


/* =========================================================
   CALCULAR TOTAL Y CANTIDAD
========================================================= */

$total = 0;
$totalProductos = 0;

foreach ($carrito as $item) {

    $precio = floatval($item['precio']);
    $cantidad = intval($item['cantidad']);

    $total += $precio * $cantidad;

    $totalProductos += $cantidad;

}

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Carrito | BOXER HOUSE
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >


    <link rel="stylesheet" href="assets/css/pages/carrito.css">

</head>


<body>


<!-- =========================================================
     HEADER
========================================================= -->

<header class="header">

    <div class="container">


        <!-- LOGO -->

        <a
            href="index.html"
            class="logo"
        >

            <img
                src="assets/images/branding/LOGO(2).jpg.jpeg"
                alt="BOXER HOUSE"
            >

        </a>


        <!-- =================================================
             NAVEGACIÓN
        ================================================== -->

        <nav
            class="navbar"
            id="navbar"
        >

            <a href="index.html">
                Inicio
            </a>

            <a href="productos.php">
                Productos
            </a>

            <a href="marcas.html">
                Marcas
            </a>

            <a href="nosotros.html">
                Nosotros
            </a>

            <a href="contacto.html">
                Contacto
            </a>

        </nav>


        <!-- =================================================
             ACCIONES
        ================================================== -->

        <div class="header-actions">


            <!-- CARRITO -->

            <button
                class="cart-btn"
                id="cartBtn"
                type="button"
                aria-label="Abrir carrito"
            >

                🛒

                <span id="cartCount">

                    <?php
                    echo $totalProductos;
                    ?>

                </span>

            </button>


            <!-- MENÚ MÓVIL -->

            <button
                class="menu-btn"
                id="menuBtn"
                type="button"
                aria-label="Abrir menú"
            >

                ☰

            </button>


        </div>

    </div>

</header>


<!-- =========================================================
     MENÚ MÓVIL
========================================================= -->

<div
    class="mobile-menu"
    id="mobileMenu"
>


    <button
        class="close-menu"
        id="closeMenu"
        type="button"
        aria-label="Cerrar menú"
    >

        ×

    </button>


    <div class="mobile-menu-logo">

        <img
            src="assets/images/branding/LOGO(2).jpg.jpeg"
            alt="BOXER HOUSE"
        >

    </div>


    <nav class="mobile-nav">

        <a href="index.html">
            Inicio
        </a>

        <a href="productos.php">
            Productos
        </a>

        <a href="marcas.html">
            Marcas
        </a>

        <a href="nosotros.html">
            Nosotros
        </a>

        <a href="contacto.html">
            Contacto
        </a>

    </nav>

</div>


<!-- =========================================================
     CARRITO
========================================================= -->

<main class="cart-page">


    <div class="cart-container">


        <!-- =================================================
             ENCABEZADO
        ================================================== -->

        <div class="cart-header">

            <span class="cart-label">
                BOXER HOUSE
            </span>

            <h1>
                CARRITO
            </h1>

            <p>
                Revisa los productos que seleccionaste.
            </p>

        </div>


        <?php if (empty($carrito)): ?>


            <!-- =================================================
                 CARRITO VACÍO
            ================================================== -->

            <div class="cart-empty">

                

                <h2>
                    TU CARRITO ESTÁ VACÍO
                </h2>

                <p>
                    Aún no has agregado ningún producto.
                </p>

                <a href="productos.php">
                    VER PRODUCTOS
                </a>

            </div>


        <?php else: ?>


            <div class="cart-content">


                <!-- =================================================
                     PRODUCTOS
                ================================================== -->

                <div class="cart-items">


                    <?php foreach (
                        $carrito
                        as $clave => $item
                    ): ?>


                        <article class="cart-item">


                            <!-- IMAGEN -->

                            <div class="cart-item-image">

                                <?php if (
                                    !empty(
                                        $item['imagen']
                                    )
                                ): ?>

                                    <img
                                        src="<?php
                                            echo htmlspecialchars(
                                                $item['imagen']
                                            );
                                        ?>"
                                        alt="<?php
                                            echo htmlspecialchars(
                                                $item['marca']
                                            );
                                        ?>"
                                    >

                                <?php endif; ?>

                            </div>


                            <!-- INFORMACIÓN -->

<div class="cart-item-info">


    <!-- MARCA / TIPO -->

    <span class="cart-item-brand">

        <?php

        if (
            isset($item['tipo_producto']) &&
            !empty($item['tipo_producto'])
        ) {

            echo htmlspecialchars(
                $item['tipo_producto']
            );

        } else {

            echo "BOXER HOUSE";

        }

        ?>

    </span>


    <!-- NOMBRE DEL PRODUCTO -->

    <h2>

        <?php

        if (
            isset($item['tipo_producto']) &&
            !empty($item['tipo_producto'])
        ) {

            echo htmlspecialchars(
                $item['tipo_producto']
            );

        } else {

            echo "BOXER X1";

        }

        ?>

    </h2>


    <!-- DETALLES -->

    <div class="cart-item-details">


        <!-- TALLA -->

        <span class="cart-detail">

            Talla:

            <?php

            echo htmlspecialchars(
                $item['talla']
            );

            ?>

        </span>


    </div>


    <!-- PRECIO -->

    <div class="cart-item-price">

        $

        <?php

        echo number_format(
            $item['precio'],
            0,
            ',',
            '.'
        );

        ?>

    </div>


</div>

                            <!-- =================================================
                                 CANTIDAD
                            ================================================== -->

                            <div class="cart-item-quantity">


                                <span>
                                    CANTIDAD
                                </span>


                                <div class="quantity-controls">


                                    <!-- RESTAR -->

                                    <a
                                        href="actualizar-carrito.php?accion=disminuir&clave=<?php echo urlencode($clave); ?>"
                                        class="quantity-btn"
                                    >

                                        −

                                    </a>


                                    <!-- CANTIDAD -->

                                    <span class="quantity-number">

                                        <?php
                                        echo intval(
                                            $item['cantidad']
                                        );
                                        ?>

                                    </span>


                                    <!-- SUMAR -->

                                    <a
                                        href="actualizar-carrito.php?accion=aumentar&clave=<?php echo urlencode($clave); ?>"
                                        class="quantity-btn"
                                    >

                                        +

                                    </a>


                                </div>


                                <!-- ELIMINAR -->

                                <a
                                    href="actualizar-carrito.php?accion=eliminar&clave=<?php echo urlencode($clave); ?>"
                                    class="remove-product"
                                    onclick="
                                        return confirm(
                                            '¿Quieres eliminar este producto del carrito?'
                                        );
                                    "
                                >

                                    ELIMINAR

                                </a>


                            </div>


                        </article>


                    <?php endforeach; ?>


                </div>


                <!-- =================================================
                     RESUMEN
                ================================================== -->

                <aside class="cart-summary">


                    <span class="cart-summary-label">
                        RESUMEN
                    </span>


                    <h2>
                        TU PEDIDO
                    </h2>


                    <div class="cart-summary-row">

                        <span>
                            Productos
                        </span>

                        <strong>

                            <?php
                            echo $totalProductos;
                            ?>

                        </strong>

                    </div>


                    <div class="cart-summary-row">

                        <span>
                            Envío
                        </span>

                        <strong>
                            Gratis
                        </strong>

                    </div>


                    <div class="cart-summary-total">

                        <span>
                            Total
                        </span>

                        <strong>

                            $

                            <?php

                            echo number_format(
                                $total,
                                0,
                                ',',
                                '.'
                            );

                            ?>

                        </strong>

                    </div>


                    <!-- =================================================
                         BOTÓN PEDIR POR WHATSAPP
                    ================================================== -->

                    <button
                        type="button"
                        class="cart-btn-primary"
                        onclick="mostrarFormularioWhatsApp();"
                    >

                        PEDIR POR WHATSAPP

                        <span>
                            →
                        </span>

                    </button>


                    <!-- =================================================
                         SEGUIR COMPRANDO
                    ================================================== -->

                    <a
                        href="productos.php"
                        class="cart-btn-secondary"
                    >

                        ← SEGUIR COMPRANDO

                    </a>


                    <!-- =================================================
                         VACIAR
                    ================================================== -->

                    <a
                        href="actualizar-carrito.php?accion=vaciar"
                        class="clear-cart"
                        onclick="
                            return confirm(
                                '¿Quieres vaciar todo el carrito?'
                            );
                        "
                    >

                        VACIAR CARRITO

                    </a>


                </aside>


            </div>


        <?php endif; ?>


    </div>

</main>


<!-- =========================================================
     FORMULARIO WHATSAPP
========================================================= -->

<div
    id="whatsappForm"
    class="whatsapp-form-overlay"
>


    <div class="whatsapp-form-box">


        <!-- CERRAR -->

        <button
            type="button"
            class="whatsapp-form-close"
            onclick="cerrarFormularioWhatsApp();"
        >

            ×

        </button>


        <span class="cart-summary-label">
            BOXER HOUSE
        </span>


        <h2>
            DATOS DE ENTREGA
        </h2>


        <p>
            Completa tus datos para enviar el pedido por WhatsApp.
        </p>


        <!-- =================================================
             FORMULARIO
        ================================================== -->

        <form
            onsubmit="enviarPedidoWhatsApp(event);"
        >


            <!-- NOMBRE -->

            <div class="whatsapp-form-group">

                <label for="clienteNombre">
                    NOMBRE COMPLETO
                </label>

                <input
                    type="text"
                    id="clienteNombre"
                    autocomplete="name"
                    required
                >

            </div>


            <!-- TELÉFONO -->

            <div class="whatsapp-form-group">

                <label for="clienteTelefono">
                    TELÉFONO
                </label>

                <input
                    type="tel"
                    id="clienteTelefono"
                    autocomplete="tel"
                    required
                >

            </div>


            <!-- DIRECCIÓN -->

            <div class="whatsapp-form-group">

                <label for="clienteDireccion">
                    DIRECCIÓN
                </label>

                <input
                    type="text"
                    id="clienteDireccion"
                    autocomplete="street-address"
                    required
                >

            </div>


            <!-- CIUDAD -->

            <div class="whatsapp-form-group">

                <label for="clienteCiudad">
                    CIUDAD
                </label>

                <input
                    type="text"
                    id="clienteCiudad"
                    autocomplete="address-level2"
                    required
                >

            </div>


            <!-- ENVIAR -->

            <button
                type="submit"
                class="whatsapp-submit"
            >

                ENVIAR PEDIDO POR WHATSAPP

                <span>
                    →
                </span>

            </button>


        </form>


    </div>

</div>


<!-- =========================================================
     WHATSAPP FLOTANTE
========================================================= -->

<a
    href="https://wa.me/573009407144"
    class="whatsapp-float"
    target="_blank"
    rel="noopener noreferrer"
    aria-label="Contactar por WhatsApp"
>

    <img
        src="https://img.icons8.com/color/96/whatsapp--v1.png"
        alt="WhatsApp"
    >

</a>


<!-- =========================================================
     JAVASCRIPT WHATSAPP
========================================================= -->

<script>

function mostrarFormularioWhatsApp() {

    const formulario =
        document.getElementById(
            "whatsappForm"
        );


    if (formulario) {

        formulario.style.display =
            "flex";

    }

}


function cerrarFormularioWhatsApp() {

    const formulario =
        document.getElementById(
            "whatsappForm"
        );


    if (formulario) {

        formulario.style.display =
            "none";

    }

}


/* =========================================================
   ENVIAR PEDIDO POR WHATSAPP
========================================================= */

function enviarPedidoWhatsApp(event) {

    event.preventDefault();


    /* =====================================================
       OBTENER DATOS
    ===================================================== */

    const nombre =
        document
            .getElementById(
                "clienteNombre"
            )
            .value
            .trim();


    const telefono =
        document
            .getElementById(
                "clienteTelefono"
            )
            .value
            .trim();


    const direccion =
        document
            .getElementById(
                "clienteDireccion"
            )
            .value
            .trim();


    const ciudad =
        document
            .getElementById(
                "clienteCiudad"
            )
            .value
            .trim();


    /* =====================================================
       CREAR MENSAJE
    ===================================================== */

    let mensaje =
        "🛍️ *NUEVO PEDIDO - BOXER HOUSE*\n\n";


    mensaje +=
        "👤 *DATOS DEL CLIENTE*\n";

    mensaje +=
        "Nombre: " +
        nombre +
        "\n";

    mensaje +=
        "Teléfono: " +
        telefono +
        "\n\n";


    mensaje +=
        "📍 *DATOS DE ENTREGA*\n";

    mensaje +=
        "Dirección: " +
        direccion +
        "\n";

    mensaje +=
        "Ciudad: " +
        ciudad +
        "\n\n";


    /* =====================================================
       PRODUCTOS
    ===================================================== */

    mensaje +=
        "🩳 *PRODUCTOS*\n\n";


    <?php foreach ($carrito as $item): ?>

        mensaje +=
            "📦 *<?php
                echo htmlspecialchars(
                    $item['marca'],
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>*\n";

        mensaje +=
            "Talla: <?php
                echo htmlspecialchars(
                    $item['talla'],
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>\n";

        mensaje +=
            "Color: <?php
                echo htmlspecialchars(
                    $item['color'] ?? '',
                    ENT_QUOTES,
                    'UTF-8'
                );
            ?>\n";

        mensaje +=
            "Cantidad: <?php
                echo intval(
                    $item['cantidad']
                );
            ?>\n";

        mensaje +=
            "Subtotal: $<?php
                echo number_format(
                    $item['precio'] *
                    $item['cantidad'],
                    0,
                    ',',
                    '.'
                );
            ?>\n\n";

    <?php endforeach; ?>


    /* =====================================================
       TOTAL
    ===================================================== */

    mensaje +=
        "💰 *TOTAL: $<?php
            echo number_format(
                $total,
                0,
                ',',
                '.'
            );
        ?>*\n\n";


    mensaje +=
        "Hola, quiero realizar este pedido.";


    /* =====================================================
       NÚMERO DE WHATSAPP
    ===================================================== */

    const numeroWhatsApp =
        "573009407144";


    /* =====================================================
       CREAR URL
    ===================================================== */

    const url =
        "https://wa.me/" +
        numeroWhatsApp +
        "?text=" +
        encodeURIComponent(
            mensaje
        );


    /* =====================================================
       ABRIR WHATSAPP
    ===================================================== */

    window.location.href =
        url;

}

</script>


<!-- =========================================================
     SCRIPT GENERAL
========================================================= -->

<script src="assets/js/main.js"></script>


</body>

</html>