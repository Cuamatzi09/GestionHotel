<?php
$page_title = "Inicio";
require_once 'includes/config.php';
require_once 'includes/header.php';
?>

<div class="hero-section">
    <div class="hero-content text-center text-white">
        <h1 class="display-4 fw-bold">Bienvenido a <?php echo SITE_NAME; ?></h1>
        <p class="lead">Descubre el lujo y la comodidad en nuestro exclusivo hotel</p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="register.php" class="btn btn-primary btn-lg me-2">Registrarse</a>
            <a href="login.php" class="btn btn-primary btn-lg">Iniciar Sesión</a>
        <?php else: ?>
            <a href="availability.php" class="btn btn-primary btn-lg">Reservar Ahora</a>
        <?php endif; ?>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Nuestras Habitaciones</h2>
        <div class="row">
            <?php
            $stmt = $pdo->query("SELECT * FROM rooms LIMIT 3");
            while ($room = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="' . SITE_URL . '/assets/images/' . $room['image_url'] . '" class="card-img-top" alt="' . $room['room_type'] . '">
                        <div class="card-body">
                            <h5 class="card-title">' . $room['room_type'] . '</h5>
                            <p class="card-text">' . $room['description'] . '</p>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item"><i class="fas fa-bed"></i> Capacidad: ' . $room['capacity'] . ' personas</li>
                                <li class="list-group-item"><i class="fas fa-dollar-sign"></i> Precio: $' . $room['price_per_night'] . ' por noche</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-transparent">
                            <a href="availability.php" class="btn btn-primary w-100">Reservar Ahora</a>
                        </div>
                    </div>
                </div>';
            }
            ?>
        </div>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2>Servicios Exclusivos</h2>
                <ul class="list-unstyled">
                    <li class="mb-3"><i class="fas fa-wifi text-primary me-2"></i> WiFi de alta velocidad gratuito</li>
                    <li class="mb-3"><i class="fas fa-swimming-pool text-primary me-2"></i> Piscina climatizada</li>
                    <li class="mb-3"><i class="fas fa-spa text-primary me-2"></i> Spa y centro de bienestar</li>
                    <li class="mb-3"><i class="fas fa-utensils text-primary me-2"></i> Restaurante gourmet</li>
                    <li class="mb-3"><i class="fas fa-concierge-bell text-primary me-2"></i> Servicio a la habitación 24/7</li>
                </ul>
            </div>
            <div class="col-md-6">
                <img src="<?php echo SITE_URL; ?>/assets/images/hotel-services.jpg" alt="Servicios del hotel" class="img-fluid rounded">
            </div>
        </div>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>