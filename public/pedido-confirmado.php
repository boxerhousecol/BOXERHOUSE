<?php

session_start();

require_once dirname(__DIR__) . '/config/database.php';


// =========================================================
// 1. OBTENER ID DEL PEDIDO
// =========================================================

$pedido_id = isset($_GET['id'])
    ? intval($_GET['id'])
    : 0;


// =========================================================
// 2. VALIDAR ID
// =========================================================

if ($pedido_id <= 0) {

    header("Location: productos.php");
    exit;

}


// =========================================================
// 3. BUSCAR PEDIDO
// =========================================================

$sql = "
    SELECT *
    FROM pedidos
    WHERE id = ?
    LIMIT 1
";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "i",
    $pedido_id
);

$stmt->execute();

$resultado = $stmt->get_result();


if ($resultado->num_rows === 0) {

    header("Location: productos.php");
    exit;

}


$pedido = $resultado->fetch_assoc();


// =========================================================
// 4. DATOS DEL PEDIDO
// =========================================================

$nombre = $pedido['nombre'];

$total = floatval(
    $pedido['total']
);


// =========================================================
// 5. BUSCAR DETALLE DEL PEDIDO
// =========================================================

$sqlDetalle = "
    SELECT *
    FROM detalle_pedido
    WHERE pedido_id = ?
";

$stmtDetalle = $conexion->prepare(
    $sqlDetalle
);

$stmtDetalle->bind_param(
    "i",
    $pedido_id
);

$stmtDetalle->execute();

$detalles = $stmtDetalle->get_result();

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
        Pedido confirmado | BOXER HOUSE
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

        <nav class="navbar">

            <a href="index.html">
                INICIO
            </a>

            <a href="productos.php">
                PRODUCTOS
            </a>

            <a href="marcas.html">
                MARCAS
            </a>

            <a href="nosotros.html">
                NOSOTROS
            </a>

            <a href="contacto.html">
                CONTACTO
            </a>

        </nav>


        <!-- ACCIONES -->

        <div class="header-actions">


            <!-- CARRITO -->

            <button
                class="cart-btn"
                id="cartBtn"
                type="button"
            >

                🛒

            </button>


            <!-- MENÚ MÓVIL -->

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
     PEDIDO CONFIRMADO
========================================================= -->

<main class="confirmation-page">


    <div class="confirmation-container">


        <!-- =================================================
             ENCABEZADO
        ================================================= -->

        <section class="confirmation-header">


            <span class="section-number">
                05
            </span>


            <span class="section-label">
                BOXER HOUSE
            </span>


            <h1>
                PEDIDO CONFIRMADO
            </h1>


            <p>
                Gracias por confiar en BOXER HOUSE.
            </p>


        </section>



        <!-- =================================================
             CONTENIDO
        ================================================= -->

        <section class="confirmation-content">


            <!-- MENSAJE -->

            <div class="confirmation-message">


                <div class="confirmation-icon">
                    ✓
                </div>


                <span class="confirmation-small">
                    COMPRA REALIZADA
                </span>


                <h2>

                    ¡Gracias por tu compra,
                    <?php
                    echo htmlspecialchars(
                        $nombre
                    );
                    ?>!

                </h2>


                <p>
                    Hemos recibido correctamente
                    tu pedido.
                </p>


                <!-- NÚMERO PEDIDO -->

                <div class="order-number">

                    NÚMERO DE PEDIDO


                    <strong>

                        #<?php
                        echo $pedido_id;
                        ?>

                    </strong>

                </div>


            </div>



            <!-- =================================================
                 RESUMEN
            ================================================= -->

            <aside class="confirmation-summary">


                <span class="summary-label">
                    RESUMEN
                </span>


                <h3>
                    TU PEDIDO
                </h3>


                <?php

                while (
                    $detalle =
                    $detalles->fetch_assoc()
                ):

                ?>


                    <div
                        class="confirmation-product"
                    >


                        <div>


                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $detalle['marca']
                                );

                                ?>

                            </strong>


                            <p>

                                Talla:

                                <?php

                                echo htmlspecialchars(
                                    $detalle['talla']
                                );

                                ?>

                            </p>


                            <p>

                                Color:

                                <?php

                                echo htmlspecialchars(
                                    $detalle['color']
                                );

                                ?>

                            </p>


                            <p>

                                Cantidad:

                                <?php

                                echo intval(
                                    $detalle['cantidad']
                                );

                                ?>

                            </p>


                        </div>


                        <strong>

                            $

                            <?php

                            echo number_format(
                                $detalle['subtotal'],
                                0,
                                ',',
                                '.'
                            );

                            ?>

                        </strong>


                    </div>


                <?php endwhile; ?>


                <!-- TOTAL -->

                <div
                    class="confirmation-total"
                >


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


            </aside>


        </section>



        <!-- =================================================
             BOTONES
        ================================================= -->

        <div
            class="confirmation-buttons"
        >


            <a
                href="productos.php"
                class="confirmation-btn-primary"
            >

                SEGUIR COMPRANDO

                <span>
                    →
                </span>

            </a>


            <a
                href="index.html"
                class="confirmation-btn-secondary"
            >

                ← VOLVER AL INICIO

            </a>


        </div>


    </div>

</main>



<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script src="assets/js/main.js"></script>


</body>

</html>