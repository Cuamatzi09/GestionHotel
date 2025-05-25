<?php
$page_title = "Realizar Reserva";
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/header.php';

// Verificar parámetros requeridos
if (!isset($_GET['room_id']) || !isset($_GET['check_in']) || !isset($_GET['check_out'])) {
    header('Location: availability.php');
    exit();
}

$room_id = $_GET['room_id'];
$check_in = $_GET['check_in'];
$check_out = $_GET['check_out'];
$adults = $_GET['adults'] ?? 1;
$children = $_GET['children'] ?? 0;

// Obtener información de la habitación
$stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->execute([$room_id]);
$room = $stmt->fetch();

if (!$room) {
    header('Location: availability.php');
    exit();
}

// Calcular número de noches y precio total
$nights = (strtotime($check_out) - strtotime($check_in)) / (60 * 60 * 24);
$total_price = $nights * $room['price_per_night'];

// Procesar reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar disponibilidad nuevamente
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM reservations 
        WHERE room_id = ? 
        AND status = 'confirmed'
        AND (
            (check_in <= ? AND check_out >= ?)
        )
    ");
    $stmt->execute([$room_id, $check_out, $check_in]);
    $conflicting_reservations = $stmt->fetchColumn();
    
    if ($conflicting_reservations > 0) {
        $error = "Lo sentimos, la habitación ya no está disponible para las fechas seleccionadas.";
    } else {
        // Crear la reserva
        $stmt = $pdo->prepare("
            INSERT INTO reservations 
            (user_id, room_id, check_in, check_out, adults, children, total_price, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmed')
        ");
        
        if ($stmt->execute([
            $_SESSION['user_id'],
            $room_id,
            $check_in,
            $check_out,
            $adults,
            $children,
            $total_price
        ])) {
            $reservation_id = $pdo->lastInsertId();
            $_SESSION['success_message'] = "Reserva realizada exitosamente. Número de reserva: #$reservation_id";
            header('Location: reservations.php');
            exit();
        } else {
            $error = "Error al realizar la reserva. Por favor intente nuevamente.";
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-book"></i> Confirmar Reserva</h5>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <h5>Detalles de la Habitación</h5>
                            <div class="card">
                                <img src="<?php echo SITE_URL; ?>/assets/images/<?php echo $room['image_url']; ?>" 
                                     class="card-img-top" alt="<?php echo $room['room_type']; ?>">
                                <div class="card-body">
                                    <h6 class="card-title"><?php echo $room['room_type']; ?></h6>
                                    <p class="card-text"><?php echo $room['description']; ?></p>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <i class="fas fa-hashtag"></i> Habitación: <?php echo $room['room_number']; ?>
                                        </li>
                                        <li class="list-group-item">
                                            <i class="fas fa-user-friends"></i> Capacidad: <?php echo $room['capacity']; ?> personas
                                        </li>
                                        <li class="list-group-item">
                                            <i class="fas fa-dollar-sign"></i> Precio por noche: $<?php echo number_format($room['price_per_night'], 2); ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-4">
                            <h5>Detalles de la Reserva</h5>
                            <div class="card">
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <strong>Fecha de Entrada:</strong> <?php echo date('d/m/Y', strtotime($check_in)); ?>
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Fecha de Salida:</strong> <?php echo date('d/m/Y', strtotime($check_out)); ?>
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Noches:</strong> <?php echo $nights; ?>
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Huéspedes:</strong> <?php echo $adults; ?> Adultos, <?php echo $children; ?> Niños
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Precio Total:</strong> $<?php echo number_format($total_price, 2); ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Información del Huésped</h5>
                            <div class="card">
                                <div class="card-body">
                                    <?php
                                    $user = getCurrentUser($pdo);
                                    echo '<p><strong>Nombre:</strong> ' . htmlspecialchars($user['full_name']) . '</p>';
                                    echo '<p><strong>Email:</strong> ' . htmlspecialchars($user['email']) . '</p>';
                                    echo '<p><strong>Teléfono:</strong> ' . ($user['phone'] ? htmlspecialchars($user['phone']) : 'No proporcionado') . '</p>';
                                    ?>
                                </div>
                            </div>
                        </div>
                        
                        <form method="post">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-check-circle"></i> Confirmar Reserva
                                </button>
                                <a href="availability.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver a Disponibilidad
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>