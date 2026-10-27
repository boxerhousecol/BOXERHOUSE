<?php

session_start();


// =========================================================
// COMPROBAR CARRITO
// =========================================================

if (
    !isset($_SESSION['carrito']) ||
    empty($_SESSION['carrito'])
) {

    header("Location: carrito.php");
    exit;

}


// =========================================================
// CALCULAR TOTAL
// =========================================================

$total = 0;

foreach ($_SESSION['carrito'] as $producto) {

    $total +=
        floatval($producto['precio']) *
        intval($producto['cantidad']);

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
        Finalizar compra | BOXER HOUSE
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

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


        <!-- NAVEGACIÓN -->

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


        <!-- ACCIONES -->

        <div class="header-actions">

            <button
                class="cart-btn"
                id="cartBtn"
                type="button"
            >

                🛒

            </button>


            <button
                class="menu-btn"
                id="menuBtn"
                type="button"
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
     FINALIZAR COMPRA
========================================================= -->

<main class="checkout-page">


    <section class="checkout-section">


        <div class="checkout-container">


            <!-- =================================================
                 ENCABEZADO
            ================================================= -->

            <div class="section-header">

                <span class="section-number">
                    04
                </span>

                <span class="section-label">
                    BOXER HOUSE
                </span>

                <h1>
                    FINALIZAR COMPRA
                </h1>

                <p>
                    Completa tus datos para realizar tu pedido.
                </p>

            </div>


            <!-- =================================================
                 CONTENIDO
            ================================================= -->

            <div class="checkout-grid">


                <!-- =================================================
                     FORMULARIO
                ================================================= -->

                <div class="checkout-form-box">


                    <h2>
                        DATOS DE ENTREGA
                    </h2>


                    <form
                        action="procesar-pedido.php"
                        method="POST"
                    >


                        <!-- NOMBRE -->

                        <div class="form-group">

                            <label for="nombre">
                                NOMBRE COMPLETO
                            </label>

                            <input
                                type="text"
                                id="nombre"
                                name="nombre"
                                required
                            >

                        </div>


                        <!-- TELÉFONO -->

                        <div class="form-group">

                            <label for="telefono">
                                TELÉFONO
                            </label>

                            <input
                                type="tel"
                                id="telefono"
                                name="telefono"
                                required
                            >

                        </div>


                        <!-- DIRECCIÓN -->

                        <div class="form-group">

                            <label for="direccion">
                                DIRECCIÓN
                            </label>

                            <input
                                type="text"
                                id="direccion"
                                name="direccion"
                                required
                            >

                        </div>


                        <!-- CIUDAD -->

                        <div class="form-group">

                            <label for="ciudad">
                                CIUDAD
                            </label>

                            <input
                                type="text"
                                id="ciudad"
                                name="ciudad"
                                required
                            >

                        </div>


                        <!-- BOTÓN -->

                        <button
                            type="submit"
                            class="checkout-btn"
                        >

                            CONFIRMAR PEDIDO

                            <span>
                                →
                            </span>

                        </button>


                    </form>


                </div>


                <!-- =================================================
                     RESUMEN DEL PEDIDO
                ================================================= -->

                <div class="checkout-summary">


                    <span class="summary-label">
                        RESUMEN
                    </span>


                    <h2>
                        TU PEDIDO
                    </h2>


                    <?php foreach (
                        $_SESSION['carrito']
                        as $producto
                    ): ?>


                        <div class="summary-product">


                            <div>

                                <strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $producto['marca']
                                    );

                                    ?>

                                </strong>


                                <p>

                                    Talla:

                                    <?php

                                    echo htmlspecialchars(
                                        $producto['talla']
                                    );

                                    ?>

                                    <br>


                                    Color:

                                    <?php

                                    echo htmlspecialchars(
                                        $producto['color']
                                    );

                                    ?>

                                    <br>


                                    Cantidad:

                                    <?php

                                    echo intval(
                                        $producto['cantidad']
                                    );

                                    ?>

                                </p>

                            </div>


                            <span>

                                $

                                <?php

                                echo number_format(
                                    $producto['precio'] *
                                    $producto['cantidad'],
                                    0,
                                    ',',
                                    '.'
                                );

                                ?>

                            </span>


                        </div>


                    <?php endforeach; ?>


                    <!-- TOTAL -->

                    <div class="summary-total">

                        <span>
                            TOTAL
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


                </div>


            </div>

        </div>

    </section>

</main>


<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script src="assets/js/main.js"></script>


</body>

</html>