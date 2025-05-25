<?php
$page_title = "Dashboard";
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/header.php';

$user = getCurrentUser($pdo);
?>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user"></i> Perfil de Usuario</h5>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <img src="assets/images/default-avatar.png" alt="Avatar" class="rounded-circle" width="120">
                </div>
                <h4><?php echo htmlspecialchars($user['full_name']); ?></h4>
                <p class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></p>
                
                <ul class="list-group list-group-flush text-start">
                    <li class="list-group-item">
                        <i class="fas fa-envelope me-2"></i> <?php echo htmlspecialchars($user['email']); ?>
                    </li>
                    <li class="list-group-item">
                        <i class="fas fa-phone me-2"></i> <?php echo $user['phone'] ? htmlspecialchars($user['phone']) : 'No proporcionado'; ?>
                    </li>
                    <li class="list-group-item">
                        <i class="fas fa-calendar-alt me-2"></i> Miembro desde: <?php echo date('d/m/Y', strtotime($user['created_at'])); ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-calendar-check"></i> Tus Próximas Reservas</h5>
            </div>
            <div class="card-body">
                <?php
                $today = date('Y-m-d');
                $stmt = $pdo->prepare("
                    SELECT r.*, rm.room_type, rm.room_number 
                    FROM reservations r
                    JOIN rooms rm ON r.room_id = rm.id
                    WHERE r.user_id = ? AND r.check_out >= ? AND r.status = 'confirmed'
                    ORDER BY r.check_in ASC
                    LIMIT 3
                ");
                $stmt->execute([$_SESSION['user_id'], $today]);
                $reservations = $stmt->fetchAll();
                
                if (count($reservations) > 0) {
                    foreach ($reservations as $reservation) {
                        echo '
                        <div class="reservation-item mb-3 p-3 border rounded">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6>' . htmlspecialchars($reservation['room_type']) . ' (Habitación ' . htmlspecialchars($reservation['room_number']) . ')</h6>
                                    <p class="mb-1"><i class="fas fa-calendar-day"></i> ' . date('d/m/Y', strtotime($reservation['check_in'])) . ' - ' . date('d/m/Y', strtotime($reservation['check_out'])) . '</p>
                                    <p class="mb-1"><i class="fas fa-users"></i> ' . $reservation['adults'] . ' Adultos, ' . $reservation['children'] . ' Niños</p>
                                </div>
                                <div class="text-end">
                                    <p class="fw-bold">$' . number_format($reservation['total_price'], 2) . '</p>
                                    <a href="reservations.php?action=view&id=' . $reservation['id'] . '" class="btn btn-sm btn-outline-primary">Ver Detalles</a>
                                </div>
                            </div>
                        </div>';
                    }
                    echo '<div class="text-center mt-3"><a href="reservations.php" class="btn btn-primary">Ver Todas las Reservas</a></div>';
                } else {
                    echo '<div class="alert alert-info">No tienes reservas próximas. <a href="availability.php" class="alert-link">¡Haz una reserva ahora!</a></div>';
                }
                ?>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-cog"></i> Configuración de Cuenta</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-lock me-2"></i> Cambiar Contraseña
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-envelope me-2"></i> Actualizar Correo Electrónico
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-phone me-2"></i> Actualizar Teléfono
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>