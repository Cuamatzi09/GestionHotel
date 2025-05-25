<?php
// includes/config.php
session_start();

// Configuración para InfinityFree
define('DB_HOST', 'sql203.infinityfree.com');
define('DB_NAME', 'if0_39072062_hotel_reservations');
define('DB_USER', 'if0_39072062');
define('DB_PASS', 'Churritos09');

// Configuración del sitio (¡cambia por tu URL real!)
define('SITE_NAME', 'Luxury Hotel');
define('SITE_URL', 'https://luxuryhotel.infinityfreeapp.com/'); 

// Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>