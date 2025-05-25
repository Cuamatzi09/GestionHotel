<?php
// includes/auth.php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

function getCurrentUser($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Función para verificar disponibilidad de habitación
function check_room_availability($pdo, $room_id, $check_in, $check_out) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations 
                          WHERE room_id = ? 
                          AND status = 'confirmed'
                          AND (
                              (check_in <= ? AND check_out >= ?)
                          )");
    $stmt->execute([$room_id, $check_out, $check_in]);
    return $stmt->fetchColumn() == 0;
}
?>