<?php
// includes/config.php
session_start();

// Configuración de la base de datos
$host = "sql203.infinityfree.com"; // Host de InfinityFree
$dbname = "if0_39072062_hotel_reservations";  // Nombre de tu BD
$user = "if0_39072062";             // Usuario
$pass = "Churritos09";          // Contraseña

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "¡Conexión exitosa!";
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}

// Configuración del sitio
define('SITE_NAME', 'Luxury Hotel');
define('SITE_URL', 'http://localhost/hotel');
?>