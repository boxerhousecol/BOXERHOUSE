<?php

session_start();

require_once dirname(__DIR__) . '/config/database.php';


/* =========================================================
   1. RECIBIR EL PRODUCTO
========================================================= */

if (
    isset($_GET['id']) &&
    !empty($_GET['id'])
) {

    $id = intval($_GET['id']);

    $sql = "
        SELECT *
        FROM productos
        WHERE id = ?
        LIMIT 1
    ";

    $stmt = $conexion->prepare($sql);

    $stmt->bind_param(
        "i",
        $id
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

} elseif (
    isset($_GET['sku']) &&
    !empty($_GET['sku'])
) {

    $sku = trim($_GET['sku']);

    $sql = "
        SELECT *
        FROM productos
        WHERE sku = ?
        LIMIT 1
    ";

    $stmt = $conexion->prepare($sql);

    $stmt->bind_param(
        "s",
        $sku
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

} else {

    die("Producto no seleccionado.");

}


/* =========================================================
   2. VERIFICAR PRODUCTO
========================================================= */

if (
    $resultado->num_rows === 0
) {

    die("Producto no encontrado.");

}


/* =========================================================
   3. DATOS DEL PRODUCTO
========================================================= */

$producto =
    $resultado->fetch_assoc();


$id =
    intval(
        $producto['id']
    );


$marca =
    $producto['marca'];


$precio =
    $producto['precio'];


$colorActual =
    trim(
        $producto['color']
    );


$imagen =
    $producto['imagen'];


$imagen2 =
    $producto['imagen2'];


$stockActual =
    intval(
        $producto['stock']
    );


$skuActual =
    trim(
        $producto['sku']
    );


/* =========================================================
   4. DETERMINAR TIPO DE PRODUCTO
========================================================= */

$skuMayusculas =
    strtoupper(
        $skuActual
    );


$nombreProducto =
    "BOXER X1";


$cantidadProducto =
    1;


$unidadesPack =
    1;


$esPack =
    false;


$esClasico =
    false;


$tipoPack =
    "";


/* =========================================================
   PACK X3 CLÁSICO
========================================================= */

if (
    strpos(
        $skuMayusculas,
        'PACK-X3-CLASICO-'
    ) === 0
) {

    $nombreProducto =
        "PACK X3";

    $cantidadProducto =
        1;

    $unidadesPack =
        3;

    $esPack =
        true;

    $esClasico =
        true;

    $tipoPack =
        "PACK-X3-CLASICO";


/* =========================================================
   PACK X5 CLÁSICO
========================================================= */

} elseif (
    strpos(
        $skuMayusculas,
        'PACK-X5-CLASICO-'
    ) === 0
) {

    $nombreProducto =
        "PACK X5";

    $cantidadProducto =
        1;

    $unidadesPack =
        5;

    $esPack =
        true;

    $esClasico =
        true;

    $tipoPack =
        "PACK-X5-CLASICO";


/* =========================================================
   PACK X3 NORMAL
========================================================= */

} elseif (
    strpos(
        $skuMayusculas,
        'PACK-X3-'
    ) === 0
) {

    $nombreProducto =
        "PACK X3";

    $cantidadProducto =
        1;

    $unidadesPack =
        3;

    $esPack =
        true;

    $esClasico =
        false;

    $tipoPack =
        "PACK-X3";


/* =========================================================
   PACK X5 NORMAL
========================================================= */

} elseif (
    strpos(
        $skuMayusculas,
        'PACK-X5-'
    ) === 0
) {

    $nombreProducto =
        "PACK X5";

    $cantidadProducto =
        1;

    $unidadesPack =
        5;

    $esPack =
        true;

    $esClasico =
        false;

    $tipoPack =
        "PACK-X5";


/* =========================================================
   BOXER X1 NORMAL
========================================================= */

} else {

    $nombreProducto =
        "BOXER X1";

    $cantidadProducto =
        1;

    $unidadesPack =
        1;

    $esPack =
        false;

    $esClasico =
        false;

    $tipoPack =
        "";

}


/* =========================================================
   5. OBTENER COLORES
========================================================= */

$resultadoColores =
    null;


/*
   SOLO PARA BOXER X1 NORMAL.

   Excluimos:

   - CK-CLASICO
   - PACK X3
   - PACK X5

   De esta manera los colores del X1
   no se mezclan con los demás productos.
*/

if (
    !$esPack
) {

    $sqlColores = "
        SELECT
            MIN(id) AS id,
            color
        FROM productos
        WHERE marca = ?
        AND sku NOT LIKE 'CK-CLASICO-%'
        AND sku NOT LIKE 'PACK-X3-%'
        AND sku NOT LIKE 'PACK-X5-%'
        AND talla IN (
            'M',
            'L',
            'XL',
            'XXL'
        )
        AND color IN (
            'Negro',
            'Rojo',
            'Azul Noche',
            'Blanco',
            'Gris'
        )
        GROUP BY color
        ORDER BY FIELD(
            color,
            'Negro',
            'Rojo',
            'Azul Noche',
            'Blanco',
            'Gris'
        )
    ";

    $stmtColores =
        $conexion->prepare(
            $sqlColores
        );

    $stmtColores->bind_param(
        "s",
        $marca
    );

    $stmtColores->execute();

    $resultadoColores =
        $stmtColores->get_result();

}


/* =========================================================
   6. OBTENER TALLAS
========================================================= */


/*
   =========================================================
   PACKS
   =========================================================
*/

if (
    $esPack
) {

    $patronPack =
        $tipoPack . "-%";


    $sqlTallas = "
        SELECT
            MAX(id) AS id,
            talla,
            MAX(stock) AS stock,
            MAX(sku) AS sku
        FROM productos
        WHERE sku LIKE ?
        AND talla IN (
            'M',
            'L',
            'XL',
            'XXL'
        )
        GROUP BY talla
        ORDER BY FIELD(
            talla,
            'M',
            'L',
            'XL',
            'XXL'
        )
    ";


    $stmtTallas =
        $conexion->prepare(
            $sqlTallas
        );


    $stmtTallas->bind_param(
        "s",
        $patronPack
    );


/*
   =========================================================
   BOXER X1 NORMAL
   =========================================================
*/

} else {

    /*
       IMPORTANTE:

       Aquí estaba el problema.

       Ahora buscamos únicamente:

       - misma marca
       - mismo color
       - NO clásico
       - NO pack

       Y además agrupamos por talla.

       Resultado:

       M
       L
       XL
       XXL

       Nunca:

       M M
       L L
       XL XL
    */

    $sqlTallas = "
        SELECT
            MAX(id) AS id,
            talla,
            MAX(stock) AS stock,
            MAX(sku) AS sku
        FROM productos
        WHERE marca = ?
        AND color = ?
        AND sku NOT LIKE 'CK-CLASICO-%'
        AND sku NOT LIKE 'PACK-X3-%'
        AND sku NOT LIKE 'PACK-X5-%'
        AND talla IN (
            'M',
            'L',
            'XL',
            'XXL'
        )
        GROUP BY talla
        ORDER BY FIELD(
            talla,
            'M',
            'L',
            'XL',
            'XXL'
        )
    ";


    $stmtTallas =
        $conexion->prepare(
            $sqlTallas
        );


    $stmtTallas->bind_param(
        "ss",
        $marca,
        $colorActual
    );

}


$stmtTallas->execute();


$resultadoTallas =
    $stmtTallas->get_result();


/* =========================================================
   7. GUARDAR TALLAS
========================================================= */

$tallasTemporales =
    [];


while (
    $filaTalla =
    $resultadoTallas->fetch_assoc()
) {

    $tallasTemporales[] =
        $filaTalla;

}


/* =========================================================
   8. DETERMINAR TALLA ACTUAL
========================================================= */

$tallaActual =
    "";


/*
   Primero intentamos encontrar
   el ID actual.
*/

foreach (
    $tallasTemporales
    as $filaTalla
) {

    if (
        intval(
            $filaTalla['id']
        )
        ===
        intval(
            $id
        )
    ) {

        $tallaActual =
            strtoupper(
                trim(
                    $filaTalla['talla']
                )
            );


        $stockActual =
            intval(
                $filaTalla['stock']
            );


        break;

    }

}


/*
   Si el ID actual no coincide con el
   MAX(id), obtenemos la talla desde
   el SKU.
*/

if (
    empty(
        $tallaActual
    )
) {

    if (
        preg_match(
            '/-(M|L|XL|XXL)$/i',
            $skuActual,
            $coincidencia
        )
    ) {

        $tallaActual =
            strtoupper(
                $coincidencia[1]
            );

    }

}


/* =========================================================
   9. GALERÍA
========================================================= */

$imagenesColor =
    [];


/* =========================================================
   PACK X3 CLÁSICO
========================================================= */

if (
    $tipoPack ===
    "PACK-X3-CLASICO"
) {

    $imagenesColor = [

        "imagenes/Clasico X3.png"

    ];


/* =========================================================
   PACK X5 CLÁSICO
========================================================= */

} elseif (
    $tipoPack ===
    "PACK-X5-CLASICO"
) {

    $imagenesColor = [

        "imagenes/Clasico X5.png"

    ];


/* =========================================================
   PACK X3 NORMAL
========================================================= */

} elseif (
    $tipoPack ===
    "PACK-X3"
) {

    $imagenesColor = [

        "imagenes/packX3.png"

    ];


/* =========================================================
   PACK X5 NORMAL
========================================================= */

} elseif (
    $tipoPack ===
    "PACK-X5"
) {

    $imagenesColor = [

        "imagenes/packX5.png"

    ];


/* =========================================================
   BOXER X1 NORMAL
========================================================= */

} else {

    /*
       =====================================================
       GALERÍA DE 4 IMÁGENES SEGÚN COLOR
       =====================================================

       Esta es la parte importante del X1.

       Cada color tiene sus propias 4 imágenes.
    */


    $imagenesPorColor = [

        "Negro" => [

            "negroAdelante.png",
            "negroAtrás.png",
            "negromodelo1.png",
            "negromodelo2.png"

        ],

        "Rojo" => [

            "rojoAdelante.png",
            "rojoAtrás.png",
            "rojomodelo 1.png",
            "rojomodelo 2.png"

        ],

        "Azul Noche" => [

            "azulAdelante.png",
            "azulAtrás.png",
            "azulmodelo1.png",
            "azulmodelo2.png"

        ],

        "Blanco" => [

            "blancoAdelante.png",
            "blancoAtrás.png",
            "blancomodelo1.png",
            "blancomodelo2.png"

        ],

        "Gris" => [

            "grisAdelante.png",
            "grisAtrás.png",
            "grismodelo1.png",
            "grismodelo2.png"

        ]

    ];


    /*
       Buscar las imágenes correspondientes
       al color actual.
    */

    if (
        isset(
            $imagenesPorColor[
                $colorActual
            ]
        )
    ) {

        foreach (
            $imagenesPorColor[
                $colorActual
            ]
            as $nombreImagen
        ) {

            $rutaFisica =
                __DIR__
                . "/imagenes/"
                . $nombreImagen;


            /*
               Si existe exactamente
               con ese nombre.
            */

            if (
                file_exists(
                    $rutaFisica
                )
            ) {

                $imagenesColor[] =
                    "imagenes/"
                    . $nombreImagen;

            } else {

                /*
                   Intentamos Atras
                   en lugar de Atrás.
                */

                $nombreSinTilde =
                    str_replace(
                        "Atrás",
                        "Atras",
                        $nombreImagen
                    );


                $rutaSinTilde =
                    __DIR__
                    . "/imagenes/"
                    . $nombreSinTilde;


                if (
                    file_exists(
                        $rutaSinTilde
                    )
                ) {

                    $imagenesColor[] =
                        "imagenes/"
                        . $nombreSinTilde;

                }

            }

        }

    }

}


/* =========================================================
   10. RESPALDO DE IMÁGENES
========================================================= */

if (
    empty(
        $imagenesColor
    )
) {

    if (
        !empty(
            $imagen
        )
    ) {

        $imagenesColor[] =
            $imagen;

    }


    if (
        !empty(
            $imagen2
        )
    ) {

        $imagenesColor[] =
            $imagen2;

    }

}


/* =========================================================
   11. IMAGEN PRINCIPAL
========================================================= */

if (
    !empty(
        $imagenesColor
    )
) {

    $imagenPrincipal =
        $imagenesColor[0];

} else {

    $imagenPrincipal =
        "";

}


/* =========================================================
   12. PRODUCTO DISPONIBLE
========================================================= */

$productoDisponible =
    $stockActual > 0;


/* =========================================================
   13. CONTADOR DEL CARRITO
========================================================= */

$totalProductos =
    0;


if (
    isset(
        $_SESSION['carrito']
    )
    &&
    is_array(
        $_SESSION['carrito']
    )
) {

    foreach (
        $_SESSION['carrito']
        as $item
    ) {

        $totalProductos +=
            intval(
                $item['cantidad'] ?? 0
            );

    }

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

        <?php

        echo htmlspecialchars(
            $marca
        );

        ?>

        |

        <?php

        echo htmlspecialchars(
            $nombreProducto
        );

        ?>

        | BOXER HOUSE

    </title>


    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >


    <link rel="stylesheet" href="assets/css/pages/detalle-producto.css">

</head>


<body>


<!-- =====================================================
     HEADER
====================================================== -->

<header class="header">

    <div class="container">


        <a
            href="index.html"
            class="logo"
        >

            <img
                src="assets/images/branding/LOGO(2).jpg.jpeg"
                alt="BOXER HOUSE"
            >

        </a>


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


        <div class="header-actions">


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


<!-- =====================================================
     MENÚ MÓVIL
====================================================== -->

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


<!-- =====================================================
     DETALLE DEL PRODUCTO
====================================================== -->

<main>

<section class="product-detail">


    <div class="product-detail-container">


        <!-- =================================================
             VOLVER
        ================================================== -->

        <div class="back-products">

            <a href="productos.php">

                ← VOLVER A PRODUCTOS

            </a>

        </div>


        <!-- =================================================
             GALERÍA
        ================================================== -->

        <div class="product-detail-images">


            <div
                class="product-detail-main-image"
            >

                <?php if (
                    !empty(
                        $imagenPrincipal
                    )
                ): ?>

                    <img
                        id="productImage"
                        src="<?php

                        echo htmlspecialchars(
                            $imagenPrincipal
                        );

                        ?>"
                        alt="<?php

                        echo htmlspecialchars(
                            $nombreProducto
                        );

                        ?>"
                    >

                <?php else: ?>

                    <p>
                        Imagen no disponible
                    </p>

                <?php endif; ?>

            </div>


            <!-- =================================================
                 MINIATURAS
            ================================================== -->

            <div
                class="product-thumbnails"
                id="productThumbnails"
            >

                <?php

                foreach (
                    $imagenesColor
                    as $indice =>
                    $imagenGaleria
                ):

                ?>

                    <button
                        type="button"
                        class="thumbnail

                        <?php

                        echo (
                            $indice === 0
                        )
                        ? 'active'
                        : '';

                        ?>"

                        data-image="<?php

                        echo htmlspecialchars(
                            $imagenGaleria
                        );

                        ?>"
                    >

                        <img
                            src="<?php

                            echo htmlspecialchars(
                                $imagenGaleria
                            );

                            ?>"
                            alt="Vista <?php

                            echo (
                                $indice + 1
                            );

                            ?>"
                        >

                    </button>

                <?php endforeach; ?>

            </div>


        </div>


        <!-- =================================================
             INFORMACIÓN
        ================================================== -->

        <div class="product-detail-info">


            <!-- MARCA -->

            <span
                class="product-detail-brand"
            >

                <?php

                echo htmlspecialchars(
                    $marca
                );

                ?>

            </span>


            <!-- NOMBRE -->

            <h1>

                <?php

                echo htmlspecialchars(
                    $nombreProducto
                );

                ?>

            </h1>


            <!-- PRECIO -->

            <strong
                class="product-detail-price"
            >

                $

                <?php

                echo number_format(
                    $precio,
                    0,
                    ',',
                    '.'
                );

                ?>

            </strong>


            <!-- =================================================
                 DESCRIPCIÓN
            ================================================== -->

            <div
                class="product-detail-description"
            >

                <p>

                    Boxer

                    <?php

                    echo htmlspecialchars(
                        $marca
                    );

                    ?>,

                    diseñado para ofrecer
                    comodidad, estilo y
                    confianza durante todo
                    el día.


                    <?php if (
                        $unidadesPack > 1
                    ): ?>

                        Este producto
                        corresponde a un pack
                        de

                        <?php

                        echo intval(
                            $unidadesPack
                        );

                        ?>

                        unidades.

                    <?php endif; ?>

                </p>

            </div>


            <!-- =================================================
                 COLOR / PACK
            ================================================== -->

            <?php if (
                !$esPack
            ): ?>


                <div
                    class="detail-option"
                >

                    <h3>
                        Color
                    </h3>


                    <div
                        class="detail-colors"
                    >

                        <?php

                        $coloresMostrados =
                            [];


                        while (
                            $colorProducto =
                            $resultadoColores
                            ->fetch_assoc()
                        ) {


                            $color =
                                trim(
                                    $colorProducto[
                                        'color'
                                    ]
                                );


                            if (
                                in_array(
                                    $color,
                                    $coloresMostrados
                                )
                            ) {

                                continue;

                            }


                            $coloresMostrados[] =
                                $color;

                        ?>

                            <a
                                href="detalle-producto.php?id=<?php

                                echo intval(
                                    $colorProducto[
                                        'id'
                                    ]
                                );

                                ?>"

                                class="color-option

                                <?php

                                echo (
                                    $color ===
                                    $colorActual
                                )
                                ? 'selected'
                                : '';

                                ?>"
                            >

                                <?php

                                echo htmlspecialchars(
                                    $color
                                );

                                ?>

                            </a>

                        <?php

                        }

                        ?>

                    </div>

                </div>


            <?php else: ?>


                <div
                    class="detail-option"
                >

                    <h3>
                        Producto
                    </h3>


                    <span
                        class="pack-label"
                    >

                        <?php

                        echo (
                            $unidadesPack === 3
                        )
                        ? 'Pack x3'
                        : 'Pack x5';

                        ?>

                    </span>

                </div>


            <?php endif; ?>


            <!-- =================================================
                 TALLAS
            ================================================== -->

            <div
                class="detail-option"
            >

                <h3>
                    Talla
                </h3>


                <div
                    class="detail-sizes"
                >

                    <?php

                    foreach (
                        $tallasTemporales
                        as $tallaProducto
                    ):


                        $talla =
                            strtoupper(
                                trim(
                                    $tallaProducto[
                                        'talla'
                                    ]
                                )
                            );


                        $stockTalla =
                            intval(
                                $tallaProducto[
                                    'stock'
                                ]
                            );


                        $idTalla =
                            intval(
                                $tallaProducto[
                                    'id'
                                ]
                            );


                        /*
                           La talla actual queda
                           seleccionada.
                        */

                        $tallaSeleccionada =
                            (
                                $talla ===
                                $tallaActual
                            );


                    ?>

                        <button

                            type="button"

                            class="size-option

                            <?php

                            echo (
                                $tallaSeleccionada
                            )
                            ? 'active'
                            : '';

                            ?>"

                            data-id="<?php

                            echo $idTalla;

                            ?>"

                            data-talla="<?php

                            echo htmlspecialchars(
                                $talla
                            );

                            ?>"

                            data-stock="<?php

                            echo $stockTalla;

                            ?>"

                            <?php

                            echo (
                                $stockTalla <= 0
                            )
                            ? 'disabled'
                            : '';

                            ?>
                        >

                            <?php

                            echo htmlspecialchars(
                                $talla
                            );

                            ?>

                        </button>


                    <?php endforeach; ?>

                </div>

            </div>


            <!-- =================================================
                 STOCK
            ================================================== -->

            <div
                class="detail-stock"
                id="stockBox"
            >

                <?php if (
                    $productoDisponible
                ): ?>


                    <span
                        id="stockStatus"
                        class="stock-available"
                    >

                        ✓ Disponible

                    </span>


                    <small
                        id="stockText"
                    >

                        20 unidades disponibles

                    </small>


                <?php else: ?>


                    <span
                        id="stockStatus"
                        class="stock-unavailable"
                    >

                        ✕ No disponible

                    </span>


                    <small
                        id="stockText"
                    >

                        Producto agotado

                    </small>


                <?php endif; ?>

            </div>


            <!-- =================================================
                 FORMULARIO CARRITO
            ================================================== -->

            <?php if (
                !empty(
                    $tallasTemporales
                )
            ): ?>


                <form
                    action="agregar-carrito.php"
                    method="POST"
                    id="cartForm"
                >


                    <!-- ID DEL PRODUCTO -->

                    <input
                        type="hidden"
                        name="id"
                        id="selectedProductId"
                        value="<?php

                        echo intval(
                            $id
                        );

                        ?>"
                    >


                    <!-- TALLA -->

                    <input
                        type="hidden"
                        name="talla"
                        id="selectedSize"
                        value="<?php

                        echo htmlspecialchars(
                            $tallaActual
                        );

                        ?>"
                    >


                    <!-- COLOR -->

                    <input
                        type="hidden"
                        name="color"
                        id="selectedColor"
                        value="<?php

                        echo htmlspecialchars(
                            $colorActual
                        );

                        ?>"
                    >


                    <!-- CANTIDAD -->

                    <input
                        type="hidden"
                        name="cantidad"
                        id="selectedQuantity"
                        value="1"
                    >


                    <!-- TIPO -->

                    <input
                        type="hidden"
                        name="tipo_producto"
                        value="<?php

                        echo htmlspecialchars(
                            $nombreProducto
                        );

                        ?>"
                    >


                    <!-- UNIDADES -->

                    <input
                        type="hidden"
                        name="unidades_pack"
                        value="<?php

                        echo intval(
                            $unidadesPack
                        );

                        ?>"
                    >


                    <button
                        type="submit"
                        class="detail-cart-btn

                        <?php

                        echo (
                            !$productoDisponible
                        )
                        ? 'disabled'
                        : '';

                        ?>"

                        id="addCartBtn"

                        <?php

                        echo (
                            !$productoDisponible
                        )
                        ? 'disabled'
                        : '';

                        ?>
                    >

                        <?php if (
                            $productoDisponible
                        ): ?>

                            AGREGAR AL CARRITO

                            <span>
                                +
                            </span>

                        <?php else: ?>

                            PRODUCTO AGOTADO

                        <?php endif; ?>

                    </button>


                </form>


            <?php else: ?>


                <button
                    type="button"
                    class="detail-cart-btn disabled"
                    disabled
                >

                    PRODUCTO AGOTADO

                </button>


            <?php endif; ?>


        </div>


    </div>


</section>


</main>


<!-- =====================================================
     WHATSAPP
====================================================== -->

<a
    href="https://wa.me/TU_NUMERO"
    class="whatsapp-float"
    target="_blank"
    rel="noopener noreferrer"
    aria-label="WhatsApp"
>

    <img
        src="https://img.icons8.com/color/96/whatsapp--v1.png"
        alt="WhatsApp"
    >

</a>


<!-- =====================================================
     JAVASCRIPT
====================================================== -->

<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {


        /* =================================================
           GALERÍA
        ================================================= */

        const productImage =
            document.getElementById(
                "productImage"
            );


        const thumbnails =
            document.querySelectorAll(
                ".product-thumbnails .thumbnail"
            );


        thumbnails.forEach(
            function (
                thumbnail
            ) {


                thumbnail.addEventListener(
                    "click",
                    function () {


                        const nuevaImagen =
                            this.dataset.image;


                        if (
                            productImage &&
                            nuevaImagen
                        ) {

                            productImage.src =
                                nuevaImagen;

                        }


                        thumbnails.forEach(
                            function (
                                item
                            ) {

                                item.classList.remove(
                                    "active"
                                );

                            }
                        );


                        this.classList.add(
                            "active"
                        );

                    }
                );

            }
        );


        /* =================================================
           TALLAS
        ================================================= */

        const sizeButtons =
            document.querySelectorAll(
                ".size-option"
            );


        const selectedProductId =
            document.getElementById(
                "selectedProductId"
            );


        const selectedSize =
            document.getElementById(
                "selectedSize"
            );


        const selectedColor =
            document.getElementById(
                "selectedColor"
            );


        const addCartBtn =
            document.getElementById(
                "addCartBtn"
            );


        const stockStatus =
            document.getElementById(
                "stockStatus"
            );


        const stockText =
            document.getElementById(
                "stockText"
            );


        /* =================================================
           SELECCIONAR TALLA
        ================================================= */

        sizeButtons.forEach(
            function (
                button
            ) {


                button.addEventListener(
                    "click",
                    function (
                        event
                    ) {


                        event.preventDefault();


                        if (
                            this.disabled
                        ) {

                            return;

                        }


                        /* ---------------------------------
                           QUITAR SELECCIÓN
                        --------------------------------- */

                        sizeButtons.forEach(
                            function (
                                item
                            ) {

                                item.classList.remove(
                                    "active"
                                );

                            }
                        );


                        /* ---------------------------------
                           ACTIVAR TALLA
                        --------------------------------- */

                        this.classList.add(
                            "active"
                        );


                        /* ---------------------------------
                           OBTENER DATOS
                        --------------------------------- */

                        const productId =
                            this.dataset.id;


                        const talla =
                            this.dataset.talla;


                        const stock =
                            parseInt(
                                this.dataset.stock ||
                                "0",
                                10
                            );


                        /* ---------------------------------
                           ACTUALIZAR ID
                        --------------------------------- */

                        if (
                            selectedProductId
                        ) {

                            selectedProductId.value =
                                productId;

                        }


                        /* ---------------------------------
                           ACTUALIZAR TALLA
                        --------------------------------- */

                        if (
                            selectedSize
                        ) {

                            selectedSize.value =
                                talla;

                        }


                        /* ---------------------------------
                           ACTUALIZAR COLOR
                        --------------------------------- */

                        if (
                            selectedColor
                        ) {

                            selectedColor.value =
                                <?php

                                echo json_encode(
                                    $colorActual
                                );

                                ?>;

                        }


                        /* ---------------------------------
                           DISPONIBILIDAD
                        --------------------------------- */

                        if (
                            stock > 0
                        ) {


                            if (
                                stockStatus
                            ) {

                                stockStatus.textContent =
                                    "✓ Disponible";

                                stockStatus.className =
                                    "stock-available";

                            }


                            /*
                               Conservamos las
                               20 unidades visuales.
                            */

                            if (
                                stockText
                            ) {

                                stockText.textContent =
                                    "20 unidades disponibles";

                            }


                            if (
                                addCartBtn
                            ) {

                                addCartBtn.disabled =
                                    false;

                                addCartBtn.classList.remove(
                                    "disabled"
                                );

                                addCartBtn.innerHTML =
                                    "AGREGAR AL CARRITO <span>+</span>";

                            }


                        } else {


                            if (
                                stockStatus
                            ) {

                                stockStatus.textContent =
                                    "✕ No disponible";

                                stockStatus.className =
                                    "stock-unavailable";

                            }


                            if (
                                stockText
                            ) {

                                stockText.textContent =
                                    "Producto agotado";

                            }


                            if (
                                addCartBtn
                            ) {

                                addCartBtn.disabled =
                                    true;

                                addCartBtn.classList.add(
                                    "disabled"
                                );

                                addCartBtn.textContent =
                                    "PRODUCTO AGOTADO";

                            }

                        }

                    }
                );

            }
        );


        /* =================================================
           VALIDAR CARRITO
        ================================================= */

        const cartForm =
            document.getElementById(
                "cartForm"
            );


        if (
            cartForm
        ) {

            cartForm.addEventListener(
                "submit",
                function (
                    event
                ) {


                    if (
                        !selectedSize ||
                        !selectedSize.value
                    ) {

                        event.preventDefault();

                        alert(
                            "Debes seleccionar una talla."
                        );

                        return;

                    }


                    if (
                        !selectedProductId ||
                        !selectedProductId.value
                    ) {

                        event.preventDefault();

                        alert(
                            "Producto no válido."
                        );

                        return;

                    }

                }
            );

        }

    }

);

</script>
<script src="assets/js/main.js"></script>

</body>

</html>