<?php

session_start();

require_once dirname(__DIR__) . '/config/database.php';


/* =========================================================
   1. CONTADOR DEL CARRITO
========================================================= */

$totalProductos = 0;

if (
    isset($_SESSION['carrito']) &&
    is_array($_SESSION['carrito'])
) {

    foreach ($_SESSION['carrito'] as $item) {

        $totalProductos += isset($item['cantidad'])
            ? intval($item['cantidad'])
            : 0;

    }

}


/* =========================================================
   2. OBTENER LOS 3 PRODUCTOS ORIGINALES
========================================================= */

/*
   Estos son los productos que YA tenías:

   ID 1  = BOXER X1
   ID 41 = PACK X3
   ID 44 = PACK X5

   NO LOS ELIMINAMOS.
*/


$sql = "
    SELECT *
    FROM productos
    WHERE id IN (1, 41, 44)
    ORDER BY FIELD(id, 1, 41, 44)
";

$resultado = $conexion->query($sql);

if (!$resultado) {
    die(
        'Error al consultar productos: '
        . $conexion->error
    );
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
        Productos | BOXER HOUSE
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


        <!-- =================================================
             LOGO
        ================================================== -->

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


    <!-- BOTÓN CERRAR -->

    <button
        class="close-menu"
        id="closeMenu"
        type="button"
        aria-label="Cerrar menú"
    >

        ×

    </button>



    <!-- LOGO -->

    <div class="mobile-menu-logo">

        <img
            src="assets/images/branding/LOGO(2).jpg.jpeg"
            alt="BOXER HOUSE"
        >

    </div>



    <!-- NAVEGACIÓN MÓVIL -->

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
     PRODUCTOS
========================================================= -->

<main class="products-page">


    <section class="products-section">


        <div class="products-container">


            <!-- =================================================
                 ENCABEZADO
            ================================================== -->

            <div class="section-header">


                <h1>
                    PRODUCTOS
                </h1>


                <p>
                    Encuentra tu boxer ideal.
                </p>


            </div>



            <!-- =================================================
                 GRID DE PRODUCTOS
            ================================================== -->

            <div class="products-grid">


                <!-- =================================================
                     PRODUCTO 01
                ================================================== -->

                <?php

                if (
                    $producto =
                    $resultado->fetch_assoc()
                ):

                ?>


                <article class="product-card">


                    <!-- ETIQUETA -->

                    <div class="product-badge">

                        BOXER HOUSE

                    </div>



                    <!-- IMAGEN -->

                    <div class="product-image">

                        <img
                            src="<?php
                                echo htmlspecialchars(
                                    $producto['imagen']
                                );
                            ?>"
                            alt="Boxer Calvin Klein Negro"
                        >

                    </div>



                    <!-- INFORMACIÓN -->

                    <div class="product-info">


                        <span class="product-brand">

                            CALVIN KLEIN

                        </span>


                        <h2>

                            BOXER X1

                        </h2>


                       <p class="product-price">
    $ <?php echo number_format((float)$producto['precio'], 0, ',', '.'); ?>
</p>

                        <a
                            href="detalle-producto.php?id=<?php
                                echo intval(
                                    $producto['id']
                                );
                            ?>"
                            class="product-btn"
                        >

                            VER PRODUCTO

                        </a>


                    </div>


                </article>


                <?php endif; ?>




                <!-- =================================================
                     PRODUCTO 02
                ================================================== -->

                <?php

                if (
                    $producto =
                    $resultado->fetch_assoc()
                ):

                ?>


                <article class="product-card">


                    <!-- ETIQUETA -->

                    <div class="product-badge">

                        BOXER HOUSE

                    </div>



                    <!-- IMAGEN -->

                    <div class="product-image">

                        <img
                            src="<?php
                                echo htmlspecialchars(
                                    $producto['imagen']
                                );
                            ?>"
                            alt="Pack de 3 Calvin Klein"
                        >

                    </div>



                    <!-- INFORMACIÓN -->

                    <div class="product-info">


                        <span class="product-brand">

                            BOXER HOUSE

                        </span>


                        <h2>

                            PACK X3

                        </h2>


                        <p class="product-price">

                            $ 64.900

                        </p>


                        <a
                            href="detalle-producto.php?id=<?php
                                echo intval(
                                    $producto['id']
                                );
                            ?>"
                            class="product-btn"
                        >

                            VER PRODUCTO

                        </a>


                    </div>


                </article>


                <?php endif; ?>




                <!-- =================================================
                     PRODUCTO 03
                ================================================== -->

                <?php

                if (
                    $producto =
                    $resultado->fetch_assoc()
                ):

                ?>


                <article class="product-card">


                    <!-- ETIQUETA -->

                    <div class="product-badge">

                        BOXER HOUSE

                    </div>



                    <!-- IMAGEN -->

                    <div class="product-image">

                        <img
                            src="<?php
                                echo htmlspecialchars(
                                    $producto['imagen']
                                );
                            ?>"
                            alt="Pack de 5 Calvin Klein"
                        >

                    </div>



                    <!-- INFORMACIÓN -->

                    <div class="product-info">


                        <span class="product-brand">

                            BOXER HOUSE

                        </span>


                        <h2>

                            PACK X5

                        </h2>


                        <p class="product-price">

                            $ 99.900

                        </p>


                        <a
                            href="detalle-producto.php?id=<?php
                                echo intval(
                                    $producto['id']
                                );
                            ?>"
                            class="product-btn"
                        >

                            VER PRODUCTO

                        </a>


                    </div>


                </article>


                <?php endif; ?>




                <!-- =================================================
                     PRODUCTO 04
                     NUEVO - BLANCO
                ================================================== -->

                <article class="product-card">


                    <!-- ETIQUETA -->

                    <div class="product-badge">

                        BOXER HOUSE

                    </div>



                    <!-- IMAGEN -->

                    <div class="product-image">

                        <img
                            src="imagenes/blancoClasicoModelo2.png"
                            alt="Boxer Calvin Klein Blanco"
                        >

                    </div>



                    <!-- INFORMACIÓN -->

                    <div class="product-info">


                        <span class="product-brand">

                            CALVIN KLEIN

                        </span>


                        <h2>

                            BOXER X1 CLASICO

                        </h2>


                        <p class="product-price">

                            $ 24.900

                        </p>


<a
    href="detalle-producto-clasico.php?sku=CK-CLASICO-BLA-M"
    class="product-btn"
>
    VER PRODUCTO
</a>


                    </div>


                </article>




                <!-- =================================================
                     PRODUCTO 05
                    pack X3
                ================================================== -->

                <article class="product-card">


                    <!-- ETIQUETA -->

                    <div class="product-badge">

                        BOXER HOUSE

                    </div>



                    <!-- IMAGEN -->

                    <div class="product-image">

                        <img
                            src="imagenes/Clasico X3.png"
                            alt="Boxer Calvin Klein Azul Noche"
                        >

                    </div>



                    <!-- INFORMACIÓN -->

                    <div class="product-info">


                        <span class="product-brand">

                            CALVIN KLEIN

                        </span>


                        <h2>

                            PACK X3 CLASICO

                        </h2>


                        <p class="product-price">

                            $ 64.900

                        </p>


<a
    href="detalle-producto.php?sku=PACK-X3-CLASICO-M"
    class="product-btn"
>
    VER PRODUCTO
</a>

                    </div>


                </article>




                <!-- =================================================
                     PRODUCTO 06
                     pack X5
                ================================================== -->

                <article class="product-card">


                    <!-- ETIQUETA -->

                    <div class="product-badge">

                        BOXER HOUSE

                    </div>



                    <!-- IMAGEN -->

                    <div class="product-image">

                        <img
                            src="imagenes/Clasico X5.png"
                            alt="Boxer Calvin Klein Rojo"
                        >

                    </div>



                    <!-- INFORMACIÓN -->

                    <div class="product-info">


                        <span class="product-brand">

                            CALVIN KLEIN

                        </span>


                        <h2>

                            PACK X5 CLASICO

                        </h2>


                        <p class="product-price">

                            $ 99.900

                        </p>


 <a
    href="detalle-producto.php?sku=PACK-X5-CLASICO-M"
    class="product-btn"
>
    VER PRODUCTO
</a>


                    </div>


                </article>


            </div>


        </div>


    </section>


</main>



 <footer class="footer">

        <p>
            © 2026 BOXER'S HOUSE.
            Todos los derechos reservados.
        </p>

    </footer>

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
     JAVASCRIPT
========================================================= -->

<script src="assets/js/main.js"></script>


<!-- REDES SOCIALES BOXER HOUSE -->
<div class="social-float">

    <!-- INSTAGRAM -->
    <a
        href="https://www.instagram.com/boxerhouse.col/"
        class="social-link instagram-link"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Instagram de BOXER HOUSE"
        title="Instagram"
    >
        <img
            src="https://img.icons8.com/fluency/96/instagram-new.png"
            alt="Instagram"
        >
    </a>

    <!-- TIKTOK -->
    <a
        href="https://www.tiktok.com/@boxerhouse.col"
        class="social-link tiktok-link"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="TikTok de BOXER HOUSE"
        title="TikTok"
    >
        <img
            src="https://img.icons8.com/fluency/96/tiktok.png"
            alt="TikTok"
        >
    </a>

</div>

</body>

</html>