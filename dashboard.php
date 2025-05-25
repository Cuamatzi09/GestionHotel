<?php
$page_title = "Dashboard";
require_once 'includes/config.php';
require_once 'includes/auth.php';

$user = get_current_user($pdo);

require_once 'includes/header.php';
?>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user"></i> Perfil de Usuario</h5>
            </div>
            <div class="card-body text-center">
                <h4><?php echo htmlspecialchars($user['full_name']); ?></h4>
                <p class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></p>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-tachometer-alt"></i> Panel de Control</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="reservations.php" class="btn btn-primary">
                        <i class="fas fa-calendar-alt"></i> Mis Reservas
                    </a>
                    <a href="availability.php" class="btn btn-success">
                        <i class="fas fa-search"></i> Buscar Disponibilidad
                    </a>
                    <a href="logout.php" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>