<?php
$page_title = "Mis Reservas";
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/header.php';

// Verificar sesión y obtener usuario
require_auth();
$user = get_current_user($pdo);

// Procesar cancelación
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'cancel' && isset($_GET['id'])) {
    $reservation_id = (int)$_GET['id'];
    
    try {
        // Verificar que la reserva pertenece al usuario y es cancelable
        $stmt = $pdo->prepare("SELECT id FROM reservations WHERE id = ? AND user_id = ? AND status = 'confirmed' AND check_in > NOW()");
        $stmt->execute([$reservation_id, $_SESSION['user_id']]);
        
        if ($stmt->fetch()) {
            $update = $pdo->prepare("UPDATE reservations SET status = 'cancelled' WHERE id = ?");
            if ($update->execute([$reservation_id])) {
                $_SESSION['success'] = "Reserva #$reservation_id cancelada exitosamente.";
            } else {
                $_SESSION['error'] = "Error al actualizar la reserva.";
            }
        } else {
            $_SESSION['error'] = "No puedes cancelar esta reserva.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
    }
    
    header('Location: reservations.php');
    exit();
}

// Obtener reservas del usuario
try {
    $stmt = $pdo->prepare("
        SELECT r.*, rm.room_type, rm.room_number 
        FROM reservations r
        JOIN rooms rm ON r.room_id = rm.id
        WHERE r.user_id = ?
        ORDER BY r.check_in DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $reservations = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error al obtener reservas: " . $e->getMessage());
}
?>

<div class="container py-4">
    <!-- Mostrar mensajes -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-calendar-alt me-2"></i>Mis Reservas
            </h5>
            <a href="availability.php" class="btn btn-light btn-sm">
                <i class="fas fa-plus me-1"></i>Nueva Reserva
            </a>
        </div>

        <div class="card-body">
            <?php if (count($reservations) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
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
                                    <strong><?= htmlspecialchars($reservation['room_type']) ?></strong><br>
                                    <small class="text-muted">Hab. <?= htmlspecialchars($reservation['room_number']) ?></small>
                                </td>
                                <td>
                                    <?= date('d/m/Y', strtotime($reservation['check_in'])) ?><br>
                                    a <?= date('d/m/Y', strtotime($reservation['check_out'])) ?>
                                </td>
                                <td>
                                    <?= $reservation['adults'] ?> Adulto(s)<br>
                                    <?= $reservation['children'] ?> Niño(s)
                                </td>
                                <td>$<?= number_format($reservation['total_price'], 2) ?></td>
                                <td>
                                    <?php
                                    $badge_class = [
                                        'confirmed' => 'success',
                                        'pending' => 'warning',
                                        'cancelled' => 'secondary'
                                    ][$reservation['status']] ?? 'info';
                                    ?>
                                    <span class="badge bg-<?= $badge_class ?>">
                                        <?= ucfirst($reservation['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="reservation-details.php?id=<?= $reservation['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if ($reservation['status'] === 'confirmed' && strtotime($reservation['check_in']) > time()): ?>
                                        <a href="reservations.php?action=cancel&id=<?= $reservation['id'] ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           title="Cancelar"
                                           onclick="return confirm('¿Confirmar cancelación de reserva #<?= $reservation['id'] ?>?')">
                                            <i class="fas fa-times"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
                    <h4>No tienes reservas registradas</h4>
                    <p class="text-muted mb-4">Comienza explorando nuestras habitaciones disponibles</p>
                    <a href="availability.php" class="btn btn-primary px-4">
                        <i class="fas fa-search me-2"></i>Buscar Disponibilidad
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>