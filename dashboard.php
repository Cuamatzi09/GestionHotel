<?php
// dashboard.php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Habilitar errores para depuración (quitar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar autenticación
require_auth();

// Obtener usuario actual
$user = get_current_user($pdo);
if (!$user) {
    die("Error al cargar datos de usuario");
}

$page_title = "Dashboard - " . $user['username'];
require_once 'includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Perfil</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                    </div>
                    <h4><?= htmlspecialchars($user['full_name']) ?></h4>
                    <p class="text-muted">@<?= htmlspecialchars($user['username']) ?></p>
                    <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-tachometer-alt"></i> Panel</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="reservations.php" class="btn btn-primary w-100 py-3">
                                <i class="fas fa-calendar-alt fa-2x mb-2"></i><br>
                                Mis Reservas
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="availability.php" class="btn btn-success w-100 py-3">
                                <i class="fas fa-search fa-2x mb-2"></i><br>
                                Buscar Habitaciones
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="profile.php" class="btn btn-info w-100 py-3">
                                <i class="fas fa-user-edit fa-2x mb-2"></i><br>
                                Editar Perfil
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="logout.php" class="btn btn-danger w-100 py-3">
                                <i class="fas fa-sign-out-alt fa-2x mb-2"></i><br>
                                Cerrar Sesión
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>