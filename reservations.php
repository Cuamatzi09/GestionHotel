<?php
$page_title = "Mis Reservas";
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/header.php';

$user = getCurrentUser($pdo);

// Manejar acciones (cancelar reserva)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $reservation_id = $_GET['id'];
    
    // Verificar que la reserva pertenece al usuario
    $stmt = $pdo->prepare("SELECT id FROM reservations WHERE id = ? AND user_id = ?");
    $stmt->execute([$reservation_id, $_SESSION['user_id']]);
    $reservation = $stmt->fetch();
    
    if ($reservation) {
        if ($action === 'cancel') {
            $stmt = $pdo->prepare("UPDATE reservations SET status = 'cancelled' WHERE id = ?");
            if ($stmt->execute([$reservation_id])) {
                $_SESSION['success_message'] = "Reserva cancelada exitosamente.";
                header('Location: reservations.php');
                exit();
            }
        }
    }
}

// Obtener todas las reservas del usuario
$stmt = $pdo->prepare("
    SELECT r.*, rm.room_type, rm.room_number 
    FROM reservations r
    JOIN rooms rm ON r.room_id = rm.id
    WHERE r.user_id = ?
    ORDER BY r.check_in DESC
");
$stmt->execute([$_SESSION['user_id']]);
$reservations = $stmt->fetchAll();
?>

<div class="card">
    <div class="card-header bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Mis Reservas</h5>
            <a href="availability.php" class="btn btn-light btn-sm"><i class="fas fa-plus"></i> Nueva Reserva</a>
        </div>
    </div>
    <div class="card-body">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_message']; ?>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (count($reservations) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Habitación</th>
                            <th>Fechas</th>
                            <th>Huéspedes</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $reservation): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($reservation['room_type']); ?></strong><br>
                                    <small class="text-muted">Habitación <?php echo htmlspecialchars($reservation['room_number']); ?></small>
                                </td>
                                <td>
                                    <?php echo date('d/m/Y', strtotime($reservation['check_in'])); ?> -<br>
                                    <?php echo date('d/m/Y', strtotime($reservation['check_out'])); ?>
                                </td>
                                <td>
                                    <?php echo $reservation['adults']; ?> Adultos<br>
                                    <?php echo $reservation['children']; ?> Niños
                                </td>
                                <td>$<?php echo number_format($reservation['total_price'], 2); ?></td>
                                <td>
                                    <?php 
                                    $status_class = '';
                                    if ($reservation['status'] === 'confirmed') $status_class = 'success';
                                    elseif ($reservation['status'] === 'pending') $status_class = 'warning';
                                    elseif ($reservation['status'] === 'cancelled') $status_class = 'danger';
                                    ?>
                                    <span class="badge bg-<?php echo $status_class; ?>">
                                        <?php echo ucfirst($reservation['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="reservations.php?action=view&id=<?php echo $reservation['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($reservation['status'] === 'confirmed' && strtotime($reservation['check_in']) > time()): ?>
                                        <a href="reservations.php?action=cancel&id=<?php echo $reservation['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Estás seguro?')">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                No tienes reservas registradas. <a href="availability.php" class="alert-link">¡Haz tu primera reserva ahora!</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>