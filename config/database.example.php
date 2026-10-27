<?php
// Copiar como database.php y configurar las variables de entorno o estos valores.
$servidor = getenv('DB_HOST') ?: 'localhost';
$usuario = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$base_datos = getenv('DB_NAME') ?: 'boxerhouse';

$conexion = new mysqli($servidor, $usuario, $password, $base_datos);
if ($conexion->connect_error) {
    die('No se pudo conectar con la base de datos.');
}
$conexion->set_charset('utf8mb4');
