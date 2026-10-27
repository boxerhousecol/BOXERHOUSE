<?php

session_start();

require_once dirname(__DIR__) . '/config/database.php';


/* =========================================================
   1. RECIBIR PRODUCTO
========================================================= */

$producto = null;


/* =========================================================
   BUSCAR POR SKU
========================================================= */

if (isset($_GET['sku']) && !empty($_GET['sku'])) {

    $skuBuscado = trim($_GET['sku']);

    $sql = "
        SELECT *
        FROM productos
        WHERE sku = ?
        LIMIT 1
    ";

    $stmt = $conexion->prepare($sql);

    $stmt->bind_param(
        "s",
        $skuBuscado
    );

    $stmt->execute();

    $resultado = $stmt->get_result();


/* =========================================================
   BUSCAR POR ID
========================================================= */

} elseif (isset($_GET['id']) && !empty($_GET['id'])) {

    $idBuscado = intval($_GET['id']);

    $sql = "
        SELECT *
        FROM productos
        WHERE id = ?
        LIMIT 1
    ";

    $stmt = $conexion->prepare($sql);

    $stmt->bind_param(
        "i",
        $idBuscado
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

} else {

    die("Producto no seleccionado.");

}


/* =========================================================
   2. VERIFICAR PRODUCTO
========================================================= */

if ($resultado->num_rows === 0) {

    die("Producto no encontrado.");

}


$producto = $resultado->fetch_assoc();


/* =========================================================
   3. DATOS DEL PRODUCTO
========================================================= */

$id = intval(
    $producto['id']
);

$marca = $producto['marca'];

$precio = floatval(
    $producto['precio']
);

$colorActual = trim(
    $producto['color']
);

$tallaActual = trim(
    $producto['talla']
);

$imagen = trim(
    $producto['imagen']
);

$imagen2 = isset($producto['imagen2'])
    ? trim($producto['imagen2'])
    : '';

$stockActual = intval(
    $producto['stock']
);

$skuActual = trim(
    $producto['sku']
);


/* =========================================================
   4. DETERMINAR TIPO DE PRODUCTO
========================================================= */

/*
   IMPORTANTE:

   Aquí separamos completamente:

   CK-CLASICO-
   PACK-X3-CLASICO-
   PACK-X5-CLASICO-

   Esto evita que las tallas se mezclen.
*/


if (
    strpos(
        $skuActual,
        'PACK-X3-CLASICO-'
    ) === 0
) {

    /* -----------------------------------------
       PACK X3 CLÁSICO
    ----------------------------------------- */

    $tipoProducto = "PACK X3";

    $unidadesPack = 3;

    $tituloProducto = "PACK X3";

    $descripcionProducto =
        "Pack clásico de 3 unidades, diseñado para ofrecer comodidad, estilo y confianza durante todo el día.";

    $familiaSku =
        "PACK-X3-CLASICO-";

    $esPack = true;

    $tipoPack = "x3";


} elseif (
    strpos(
        $skuActual,
        'PACK-X5-CLASICO-'
    ) === 0
) {

    /* -----------------------------------------
       PACK X5 CLÁSICO
    ----------------------------------------- */

    $tipoProducto = "PACK X5";

    $unidadesPack = 5;

    $tituloProducto = "PACK X5";

    $descripcionProducto =
        "Pack clásico de 5 unidades, diseñado para ofrecer comodidad, estilo y confianza durante todo el día.";

    $familiaSku =
        "PACK-X5-CLASICO-";

    $esPack = true;

    $tipoPack = "x5";


} elseif (
    strpos(
        $skuActual,
        'CK-CLASICO-'
    ) === 0
) {

    /* -----------------------------------------
       BOXER CLÁSICO X1
       
       ESTA ES LA PARTE QUE YA FUNCIONABA.
       SE MANTIENE SU LÓGICA.
    ----------------------------------------- */

    $tipoProducto = "BOXER CLÁSICO X1";

    $unidadesPack = 1;

    $tituloProducto = "BOXER CLÁSICO X1";

    $descripcionProducto =
        "Boxer Calvin Klein clásico, diseñado para ofrecer comodidad, estilo y confianza durante todo el día.";

    $familiaSku =
        "CK-CLASICO-";

    $esPack = false;

    $tipoPack = "x1";


} else {

    die("Este producto no pertenece a la línea clásica.");

}


/* =========================================================
   5. COLORES
========================================================= */

/*
   SOLO EL X1 CLÁSICO utiliza selector de colores.

   Los PACK X3 y X5 tienen una referencia específica
   y NO mezclamos sus colores con los del X1.
*/


$coloresPermitidos = [
    'Rojo',
    'Blanco',
    'Negro'
];

$resultadoColores = null;


if (!$esPack) {

    $sqlColores = "
        SELECT
            color,
            MIN(id) AS id
        FROM productos
        WHERE sku LIKE 'CK-CLASICO-%'
        AND color IN ('Rojo','Blanco','Negro')
        GROUP BY color
        ORDER BY FIELD(
            color,
            'Rojo',
            'Blanco',
            'Negro'
        )
    ";

    $stmtColores =
        $conexion->prepare(
            $sqlColores
        );

    $stmtColores->execute();

    $resultadoColores =
        $stmtColores->get_result();

}


/* =========================================================
   6. TALLAS
========================================================= */

/*
   AQUÍ ESTÁ UNO DE LOS CAMBIOS IMPORTANTES.

   Ya NO buscamos todos los productos clásicos.

   Buscamos únicamente la familia del producto actual.

   X1:
   CK-CLASICO-

   X3:
   PACK-X3-CLASICO-

   X5:
   PACK-X5-CLASICO-
*/


$sqlTallas = "
    SELECT
        id,
        talla,
        stock,
        sku
    FROM productos
    WHERE sku LIKE CONCAT(?, '%')
    AND talla IN ('M','L','XL','XXL')
    ORDER BY FIELD(
        talla,
        'M',
        'L',
        'XL',
        'XXL'
    ),
    id ASC
";


$stmtTallas =
    $conexion->prepare(
        $sqlTallas
    );

$stmtTallas->bind_param(
    "s",
    $familiaSku
);

$stmtTallas->execute();

$resultadoTallas =
    $stmtTallas->get_result();


/* =========================================================
   7. ELIMINAR TALLAS DUPLICADAS
========================================================= */

/*
   Aunque la base de datos tenga algún registro repetido,
   aquí mostramos solamente una vez:

   M
   L
   XL
   XXL
*/


$tallasTemporales = [];

$tallasMostradas = [];


while (
    $filaTalla =
    $resultadoTallas->fetch_assoc()
) {

    $tallaFila =
        trim(
            $filaTalla['talla']
        );


    if (
        isset(
            $tallasMostradas[
                $tallaFila
            ]
        )
    ) {

        continue;

    }


    $tallasMostradas[
        $tallaFila
    ] = true;


    $tallasTemporales[] =
        $filaTalla;

}


/* =========================================================
   8. GALERÍA
========================================================= */

$imagenesColor = [];


/*
   ---------------------------------------------------------
   X3 Y X5
   ---------------------------------------------------------

   Para los packs utilizamos directamente las imágenes
   guardadas en la base de datos.

   Esto evita que el sistema encuentre por accidente
   las imágenes del PACK X3 normal.
*/


if ($esPack) {

    if (
        !empty($imagen)
    ) {

        $imagenesColor[] =
            $imagen;

    }


    if (
        !empty($imagen2)
        &&
        $imagen2 !== 'NULL'
    ) {

        $imagenesColor[] =
            $imagen2;

    }


} else {

    /*
       -----------------------------------------------------
       X1 CLÁSICO
       -----------------------------------------------------

       SE CONSERVA LA GALERÍA QUE YA TENÍAS.
    */


    /* =====================================================
       NORMALIZAR COLOR
    ===================================================== */

    $colorBusqueda =
        strtolower(
            trim(
                $colorActual
            )
        );


    $colorBusqueda =
        str_replace(
            [
                'á',
                'é',
                'í',
                'ó',
                'ú',
                'ñ',
                ' '
            ],
            [
                'a',
                'e',
                'i',
                'o',
                'u',
                'n',
                ''
            ],
            $colorBusqueda
        );


    /* =====================================================
       BUSCAR IMÁGENES
    ===================================================== */

    $archivosImagenes =
        glob(
            __DIR__ . "/imagenes/*"
        );


    foreach (
        $archivosImagenes
        as $archivo
    ) {

        if (
            !is_file($archivo)
        ) {

            continue;

        }


        $nombreArchivo =
            basename(
                $archivo
            );


        $nombreNormalizado =
            strtolower(
                $nombreArchivo
            );


        $nombreNormalizado =
            str_replace(
                [
                    'á',
                    'é',
                    'í',
                    'ó',
                    'ú',
                    'ñ',
                    ' '
                ],
                [
                    'a',
                    'e',
                    'i',
                    'o',
                    'u',
                    'n',
                    ''
                ],
                $nombreNormalizado
            );


        if (
            strpos(
                $nombreNormalizado,
                'clasico'
            ) !== false
            &&
            strpos(
                $nombreNormalizado,
                $colorBusqueda
            ) !== false
        ) {

            $imagenesColor[] =
                "imagenes/" .
                $nombreArchivo;

        }

    }


    /* =====================================================
       ORDENAR IMÁGENES
    ===================================================== */

    usort(
        $imagenesColor,
        function ($a, $b) {

            $orden = [

                'adelante' => 1,
                'frontal' => 1,

                'atras' => 2,
                'atrás' => 2,
                'trasera' => 2,

                'modelo1' => 3,
                'modelo01' => 3,

                'modelo2' => 4,
                'modelo02' => 4

            ];


            $nombreA =
                strtolower(
                    basename($a)
                );


            $nombreB =
                strtolower(
                    basename($b)
                );


            $posA = 99;

            $posB = 99;


            foreach (
                $orden
                as $palabra =>
                $posicion
            ) {

                if (
                    strpos(
                        $nombreA,
                        $palabra
                    ) !== false
                ) {

                    $posA =
                        $posicion;

                    break;

                }

            }


            foreach (
                $orden
                as $palabra =>
                $posicion
            ) {

                if (
                    strpos(
                        $nombreB,
                        $palabra
                    ) !== false
                ) {

                    $posB =
                        $posicion;

                    break;

                }

            }


            return $posA <=> $posB;

        }
    );


    /* =====================================================
       RESPALDO
    ===================================================== */

    if (
        empty($imagenesColor)
    ) {

        if (
            !empty($imagen)
        ) {

            $imagenesColor[] =
                $imagen;

        }


        if (
            !empty($imagen2)
        ) {

            $imagenesColor[] =
                $imagen2;

        }

    }

}


/* =========================================================
   9. ELIMINAR IMÁGENES DUPLICADAS
========================================================= */

$imagenesColor =
    array_values(
        array_unique(
            $imagenesColor
        )
    );


/* =========================================================
   10. IMAGEN PRINCIPAL
========================================================= */

$imagenPrincipal =
    !empty($imagenesColor)
    ? $imagenesColor[0]
    : $imagen;


/* =========================================================
   11. CARRITO
========================================================= */

$totalProductos = 0;


if (
    isset($_SESSION['carrito'])
    &&
    is_array($_SESSION['carrito'])
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

        CALVIN KLEIN |
        <?php
        echo htmlspecialchars(
            $tituloProducto
        );
        ?> |
        BOXER HOUSE

    </title>


    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >


    <!-- =====================================================
         GALERÍA
    ====================================================== -->

    <link rel="stylesheet" href="assets/css/pages/detalle-producto-clasico.css">

</head>


<body>


<!-- =========================================================
     HEADER
========================================================= -->

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
     PRODUCTO
========================================================= -->

<main>

<section class="product-detail">

    <div class="product-detail-container">


        <!-- =================================================
             GALERÍA
        ================================================== -->

        <div>


            <div class="product-detail-image">

                <?php if (
                    !empty($imagenPrincipal)
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
                                $tituloProducto
                            );
                        ?>"
                    >

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
                        class="thumbnail <?php
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
                            alt="Imagen <?php
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


            <span class="detail-brand">

                <?php
                echo htmlspecialchars(
                    $marca
                );
                ?>

            </span>


            <h1>

                <?php
                echo htmlspecialchars(
                    $tituloProducto
                );
                ?>

            </h1>


            <strong class="detail-price">

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


            <p class="detail-description">

                <?php
                echo htmlspecialchars(
                    $descripcionProducto
                );
                ?>

            </p>


            <!-- =================================================
                 COLORES — SOLO X1
            ================================================== -->

            <?php if (!$esPack): ?>

            <div class="detail-option">

                <h3>
                    Color
                </h3>


                <div class="color-options">


                    <?php

                    while (
                        $colorProducto =
                        $resultadoColores->fetch_assoc()
                    ) {


                        $color =
                            $colorProducto['color'];


                        /*
                           Buscamos el registro
                           de talla M de ese color.
                        */

                        $sqlColorId = "
                            SELECT id
                            FROM productos
                            WHERE color = ?
                            AND sku LIKE 'CK-CLASICO-%'
                            AND talla = 'M'
                            LIMIT 1
                        ";


                        $stmtColorId =
                            $conexion->prepare(
                                $sqlColorId
                            );


                        $stmtColorId->bind_param(
                            "s",
                            $color
                        );


                        $stmtColorId->execute();


                        $resultadoColorId =
                            $stmtColorId->get_result();


                        $filaColorId =
                            $resultadoColorId->fetch_assoc();


                        $idColor =
                            $filaColorId
                            ? intval(
                                $filaColorId['id']
                            )
                            : intval(
                                $colorProducto['id']
                            );

                    ?>


                        <a
                            href="detalle-producto-clasico.php?id=<?php
                                echo $idColor;
                            ?>"
                            class="color-option <?php
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

            <?php endif; ?>


            <!-- =================================================
                 IDENTIFICADOR DEL PACK
            ================================================== -->

            <?php if ($esPack): ?>

            <div class="detail-option">

                <div class="color-options">

                    <span
                        class="color-option selected"
                    >

                        <?php
                        echo htmlspecialchars(
                            $tipoProducto
                        );
                        ?>

                    </span>

                </div>

            </div>

            <?php endif; ?>


            <!-- =================================================
                 TALLAS
            ================================================== -->

            <div class="detail-option">

                <h3>
                    Talla
                </h3>


                <div class="size-options">


                    <?php

                    foreach (
                        $tallasTemporales
                        as $tallaProducto
                    ) {


                        $idTalla =
                            intval(
                                $tallaProducto['id']
                            );


                        $talla =
                            $tallaProducto['talla'];


                        $stockTalla =
                            intval(
                                $tallaProducto['stock']
                            );


                        $esActual =
                            $idTalla === $id;

                    ?>


                        <button
                            type="button"
                            class="size-option <?php
                                echo (
                                    $esActual
                                )
                                ? 'active'
                                : '';
                            ?>"
                            data-product-id="<?php
                                echo $idTalla;
                            ?>"
                            data-size="<?php
                                echo htmlspecialchars(
                                    $talla
                                );
                            ?>"
                            data-stock="<?php
                                echo $stockTalla;
                            ?>"
                            <?php
                            if (
                                $stockTalla <= 0
                            ) {

                                echo 'disabled';

                            }
                            ?>
                        >

                            <?php

                            echo htmlspecialchars(
                                $talla
                            );

                            ?>

                        </button>


                    <?php

                    }

                    ?>


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
                    $stockActual > 0
                ): ?>

                    <span id="stockStatus">

                        ✓ Disponible

                    </span>


                    <small id="stockText">

                        20 unidades disponibles

                    </small>


                <?php else: ?>

                    <span
                        id="stockStatus"
                        class="out-stock"
                    >

                        ✕ No disponible

                    </span>


                    <small id="stockText">

                        Producto agotado

                    </small>

                <?php endif; ?>


            </div>


            <!-- =================================================
                 FORMULARIO
            ================================================== -->

            <form
                action="agregar-carrito.php"
                method="POST"
                id="cartForm"
            >


                <input
                    type="hidden"
                    name="id"
                    id="selectedProductId"
                    value="<?php
                        echo $id;
                    ?>"
                >


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


                <input
                    type="hidden"
                    name="cantidad"
                    value="1"
                >


                <input
                    type="hidden"
                    name="tipo_producto"
                    value="<?php
                        echo htmlspecialchars(
                            $tipoProducto
                        );
                    ?>"
                >


                <input
                    type="hidden"
                    name="unidades_pack"
                    value="<?php
                        echo $unidadesPack;
                    ?>"
                >


                <button
                    type="submit"
                    class="detail-cart-btn"
                    id="addCartBtn"
                    <?php
                    echo (
                        $stockActual <= 0
                    )
                    ? 'disabled'
                    : '';
                    ?>
                >

                    <?php if (
                        $stockActual > 0
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


        </div>


    </div>

</section>


<!-- =========================================================
     VOLVER
========================================================= -->

<section class="back-products">

    <a href="productos.php">

        ← VOLVER A PRODUCTOS

    </a>

</section>


</main>


<!-- =========================================================
     WHATSAPP
========================================================= -->

<a
    href="https://wa.me/"
    class="whatsapp-float"
    target="_blank"
    rel="noopener noreferrer"
>

    <img
        src="https://img.icons8.com/color/96/whatsapp--v1.png"
        alt="WhatsApp"
    >

</a>


<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script src="assets/js/pages/detalle-producto-clasico.js"></script>


<script src="assets/js/main.js"></script>


</body>

</html>