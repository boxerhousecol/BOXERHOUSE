<?php

session_start();

require_once dirname(__DIR__) . '/config/database.php';


/* =========================================================
   1. RECIBIR DATOS
========================================================= */

$id = isset($_POST['id'])
    ? intval($_POST['id'])
    : 0;

$talla = isset($_POST['talla'])
    ? trim($_POST['talla'])
    : '';

$color = isset($_POST['color'])
    ? trim($_POST['color'])
    : '';

$cantidad = isset($_POST['cantidad'])
    ? intval($_POST['cantidad'])
    : 1;


/* =========================================================
   2. VALIDAR ID
========================================================= */

if ($id <= 0) {

    die("Producto no válido.");

}


/* =========================================================
   3. VALIDAR TALLA
========================================================= */

if (empty($talla)) {

    die("Debes seleccionar una talla.");

}


/* =========================================================
   4. VALIDAR CANTIDAD
========================================================= */

if ($cantidad < 1) {

    $cantidad = 1;

}


/* =========================================================
   5. BUSCAR PRODUCTO
========================================================= */

$sql = "
    SELECT *
    FROM productos
    WHERE id = ?
    LIMIT 1
";


$stmt = $conexion->prepare($sql);


if (!$stmt) {

    die("Error al preparar la consulta.");

}


$stmt->bind_param(
    "i",
    $id
);


$stmt->execute();


$resultado =
    $stmt->get_result();


/* =========================================================
   6. VERIFICAR PRODUCTO
========================================================= */

if (
    $resultado->num_rows === 0
) {

    die("Producto no encontrado.");

}


$producto =
    $resultado->fetch_assoc();


/* =========================================================
   7. DATOS DEL PRODUCTO
========================================================= */

$sku =
    trim(
        $producto['sku']
    );


$marca =
    $producto['marca'];


$precio =
    floatval(
        $producto['precio']
    );


$imagen =
    $producto['imagen'];


$stock =
    intval(
        $producto['stock']
    );


/* =========================================================
   8. DETERMINAR TIPO DE PRODUCTO
========================================================= */


/*
   ---------------------------------------------------------
   BOXER CLÁSICO X1
   ---------------------------------------------------------
*/

if (
    strpos(
        strtoupper($sku),
        'CK-CLASICO-'
    ) === 0
) {

    $tipoProducto =
        "BOXER CLÁSICO X1";

    $unidadesPack =
        1;


/*
   ---------------------------------------------------------
   PACK X3
   ---------------------------------------------------------
*/

} elseif (
    strpos(
        strtoupper($sku),
        'PACK-X3-'
    ) === 0
) {

    $tipoProducto =
        "PACK X3";

    $unidadesPack =
        3;


/*
   ---------------------------------------------------------
   PACK X5
   ---------------------------------------------------------
*/

} elseif (
    strpos(
        strtoupper($sku),
        'PACK-X5-'
    ) === 0
) {

    $tipoProducto =
        "PACK X5";

    $unidadesPack =
        5;


/*
   ---------------------------------------------------------
   OTROS PRODUCTOS X1
   ---------------------------------------------------------
*/

} else {

    $tipoProducto =
        "BOXER X1";

    $unidadesPack =
        1;

}


/* =========================================================
   9. COMPROBAR STOCK
========================================================= */

if ($stock <= 0) {

    die("Producto agotado.");

}


/*
   No permitir agregar más
   unidades de las disponibles.
*/

if (
    $cantidad > $stock
) {

    $cantidad =
        $stock;

}


/* =========================================================
   10. CREAR CARRITO
========================================================= */

if (
    !isset(
        $_SESSION['carrito']
    )
) {

    $_SESSION['carrito'] = [];

}


/* =========================================================
   11. IDENTIFICADOR DEL PRODUCTO
========================================================= */

/*
   Utilizamos:

   ID + TALLA + COLOR

   De esta manera:

   Clásico Rojo M
   Clásico Rojo L
   Clásico Rojo XL
   Clásico Negro M

   son productos independientes.
*/

$clave =
    $id .
    "_" .
    $talla .
    "_" .
    $color;


/* =========================================================
   12. AGREGAR O ACTUALIZAR
========================================================= */

if (
    isset(
        $_SESSION['carrito'][$clave]
    )
) {


    /*
       El producto ya existe
       en el carrito.
    */

    $_SESSION['carrito'][$clave]['cantidad']
        += $cantidad;


    /*
       No superar el stock real.
    */

    if (
        $_SESSION['carrito'][$clave]['cantidad']
        > $stock
    ) {

        $_SESSION['carrito'][$clave]['cantidad']
            = $stock;

    }


} else {


    /*
       Crear producto nuevo
       dentro del carrito.
    */

    $_SESSION['carrito'][$clave] = [

        'id' =>
            $id,

        'sku' =>
            $sku,

        'marca' =>
            $marca,

        'tipo_producto' =>
            $tipoProducto,

        'unidades_pack' =>
            $unidadesPack,

        'color' =>
            $color,

        'talla' =>
            $talla,

        'precio' =>
            $precio,

        'imagen' =>
            $imagen,

        'cantidad' =>
            $cantidad

    ];

}


/* =========================================================
   13. REDIRECCIÓN
========================================================= */

/*
   AQUÍ ESTABA EL PROBLEMA.

   Si es CLÁSICO X1:
   vuelve a detalle-producto-clasico.php

   Si es otro producto:
   mantiene detalle-producto.php
*/


if (
    strpos(
        strtoupper($sku),
        'CK-CLASICO-'
    ) === 0
) {


    header(
        "Location: detalle-producto-clasico.php?id="
        . $id
    );


} else {


    header(
        "Location: detalle-producto.php?id="
        . $id
    );


}


exit;

?>