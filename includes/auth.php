<?php
// includes/auth.php
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Función para obtener información del usuario actual
function getCurrentUser($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>