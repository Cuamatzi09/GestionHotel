<?php
// includes/auth.php

// Verifica si hay una sesión activa
function check_session() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Obtiene el usuario actual desde la base de datos
function get_current_user($pdo) {
    check_session();
    
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php?error=no_session");
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            session_destroy();
            header("Location: login.php?error=user_not_found");
            exit();
        }

        return $user;
    } catch(PDOException $e) {
        error_log("Database error in get_current_user: " . $e->getMessage());
        return false;
    }
}

// Verificación de autenticación
function require_auth() {
    check_session();
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header("Location: login.php");
        exit();
    }
}