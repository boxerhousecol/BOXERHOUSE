<?php

session_start();

require_once dirname(__DIR__) . '/config/database.php';


/* =========================================================
   COMPROBAR CARRITO
========================================================= */

if (
    !isset($_SESSION['carrito']) ||
    empty($_SESSION['carrito'])
) {

    header("Location: carrito.php");
    exit;

}


/* =========================================================
   RECIBIR DATOS DEL FORMULARIO
========================================================= */

$nombre = isset($_POST['nombre'])
    ? trim($_POST['nombre'])
    : '';

$telefono = isset($_POST['telefono'])
    ? trim($_POST['telefono'])
    : '';

$direccion = isset($_POST['direccion'])
    ? trim($_POST['direccion'])
    : '';

$ciudad = isset($_POST['ciudad'])
    ? trim($_POST['ciudad'])
    : '';


/* =========================================================
   VALIDAR DATOS
========================================================= */

if (
    empty($nombre) ||
    empty($telefono) ||
    empty($direccion) ||
    empty($ciudad)
) {

    die("Todos los campos son obligatorios.");

}


/* =========================================================
   CALCULAR TOTAL
========================================================= */

$total = 0;

foreach ($_SESSION['carrito'] as $producto) {

    $precio = floatval($producto['precio']);
    $cantidad = intval($producto['cantidad']);

    $total += $precio * $cantidad;

}


/* =========================================================
   CREAR PEDIDO
========================================================= */

$sqlPedido = "
    INSERT INTO pedidos
    (
        nombre,
        telefono,
        direccion,
        ciudad,
        total,
        estado
    )
    VALUES (?, ?, ?, ?, ?, 'Pendiente')
";


$stmtPedido = $conexion->prepare($sqlPedido);


if (!$stmtPedido) {

    die("Error al preparar el pedido: " . $conexion->error);

}


$stmtPedido->bind_param(
    "ssssd",
    $nombre,
    $telefono,
    $direccion,
    $ciudad,
    $total
);


if (!$stmtPedido->execute()) {

    die("Error al guardar el pedido: " . $stmtPedido->error);

}


$pedidoId = $conexion->insert_id;


/* =========================================================
   PREPARAR DETALLE DEL PEDIDO
========================================================= */

$sqlDetalle = "
    INSERT INTO detalle_pedido
    (
        pedido_id,
        producto_id,
        sku,
        marca,
        talla,
        color,
        precio,
        cantidad,
        subtotal
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
";


$stmtDetalle = $conexion->prepare($sqlDetalle);


if (!$stmtDetalle) {

    die(
        "Error al preparar el detalle: "
        . $conexion->error
    );

}


/* =========================================================
   DESCONTAR STOCK
========================================================= */

$sqlStock = "
    UPDATE productos
    SET stock = stock - ?
    WHERE id = ?
      AND stock >= ?
";

$stmtStock = $conexion->prepare($sqlStock);


if (!$stmtStock) {

    die(
        "Error al preparar actualización de stock: "
        . $conexion->error
    );

}


/* =========================================================
   GUARDAR PRODUCTOS
========================================================= */

foreach ($_SESSION['carrito'] as $producto) {

    $productoId = intval($producto['id']);

    $sku = $producto['sku'];

    $marca = $producto['marca'];

    $talla = $producto['talla'];

    $color = $producto['color'];

    $precio = floatval($producto['precio']);

    $cantidad = intval($producto['cantidad']);

    $subtotal = $precio * $cantidad;


    /* -----------------------------------------------------
       DESCONTAR STOCK
    ----------------------------------------------------- */

    $stmtStock->bind_param(
        "iii",
        $cantidad,
        $productoId,
        $cantidad
    );


    if (!$stmtStock->execute()) {

        die("Error al actualizar el stock.");

    }


    /* -----------------------------------------------------
       COMPROBAR QUE REALMENTE SE DESCONTÓ
    ----------------------------------------------------- */

    if ($stmtStock->affected_rows === 0) {

        die(
            "No hay suficiente stock para el producto: "
            . htmlspecialchars($marca)
        );

    }


    /* -----------------------------------------------------
       GUARDAR DETALLE
    ----------------------------------------------------- */

    $stmtDetalle->bind_param(
        "iissssdid",
        $pedidoId,
        $productoId,
        $sku,
        $marca,
        $talla,
        $color,
        $precio,
        $cantidad,
        $subtotal
    );


    if (!$stmtDetalle->execute()) {

        die(
            "Error al guardar el detalle del pedido: "
            . $stmtDetalle->error
        );

    }

}


/* =========================================================
   CERRAR SENTENCIAS
========================================================= */

$stmtPedido->close();

$stmtDetalle->close();

$stmtStock->close();


/* =========================================================
   VACIAR CARRITO
========================================================= */

$_SESSION['carrito'] = [];


/* =========================================================
   GUARDAR DATOS DEL PEDIDO EN SESIÓN
========================================================= */

$_SESSION['pedido_confirmado'] = [

    'id' => $pedidoId,

    'nombre' => $nombre,

    'total' => $total

];


/* =========================================================
   IR A CONFIRMACIÓN
========================================================= */

header(
    "Location: pedido-confirmado.php"
);

exit;

?>