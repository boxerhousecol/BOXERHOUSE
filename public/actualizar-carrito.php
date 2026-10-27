<?php

session_start();


/* =========================================================
   CREAR CARRITO SI NO EXISTE
========================================================= */

if (!isset($_SESSION['carrito'])) {

    $_SESSION['carrito'] = [];

}


/* =========================================================
   RECIBIR DATOS
========================================================= */

$accion = '';

$clave = '';


if (isset($_POST['accion'])) {

    $accion = $_POST['accion'];

}

elseif (isset($_GET['accion'])) {

    $accion = $_GET['accion'];

}


if (isset($_POST['clave'])) {

    $clave = $_POST['clave'];

}

elseif (isset($_GET['clave'])) {

    $clave = $_GET['clave'];

}


/* =========================================================
   VACIAR CARRITO
========================================================= */

if ($accion === 'vaciar') {

    $_SESSION['carrito'] = [];

    header("Location: carrito.php");

    exit;

}


/* =========================================================
   VERIFICAR PRODUCTO
========================================================= */

if (
    empty($clave) ||
    !isset($_SESSION['carrito'][$clave])
) {

    header("Location: carrito.php");

    exit;

}


/* =========================================================
   AUMENTAR CANTIDAD
========================================================= */

if ($accion === 'aumentar') {

    $_SESSION['carrito'][$clave]['cantidad']++;

}


/* =========================================================
   DISMINUIR CANTIDAD
========================================================= */

elseif ($accion === 'disminuir') {

    $_SESSION['carrito'][$clave]['cantidad']--;

    if (
        $_SESSION['carrito'][$clave]['cantidad'] <= 0
    ) {

        unset(
            $_SESSION['carrito'][$clave]
        );

    }

}


/* =========================================================
   ELIMINAR PRODUCTO
========================================================= */

elseif ($accion === 'eliminar') {

    unset(
        $_SESSION['carrito'][$clave]
    );

}


/* =========================================================
   VOLVER AL CARRITO
========================================================= */

header("Location: carrito.php");

exit;

?>