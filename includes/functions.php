<?php
// FUNCIONES DE BASE DE DATOS
function db_connect() {
    static $pdo;
    
    if (!$pdo) {
        try {
            $pdo = new PDO(
                "mysql:host=".DB_HOST.";dbname=".DB_NAME, 
                DB_USER, 
                DB_PASS
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

// FUNCIONES DE USUARIO
function user_register($username, $email, $password, $full_name, $phone) {
    $pdo = db_connect();
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, phone) VALUES (?, ?, ?, ?, ?)");
    return $stmt->execute([$username, $email, $hashed_password, $full_name, $phone]);
}

function user_login($username, $password) {
    $pdo = db_connect();
    
    $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        return true;
    }
    
    return false;
}

// FUNCIONES DE HABITACIONES
function get_rooms($limit = 3) {
    $pdo = db_connect();
    $stmt = $pdo->prepare("SELECT * FROM rooms LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_room_by_id($room_id) {
    $pdo = db_connect();
    $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->execute([$room_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// FUNCIONES DE RESERVAS
function create_reservation($user_id, $room_id, $check_in, $check_out, $adults, $children) {
    $pdo = db_connect();
    
    // Calcular precio total
    $room = get_room_by_id($room_id);
    $nights = (strtotime($check_out) - strtotime($check_in)) / (60 * 60 * 24);
    $total_price = $nights * $room['price_per_night'];
    
    $stmt = $pdo->prepare("INSERT INTO reservations (...) VALUES (...)");
    return $stmt->execute([...]);
}

// FUNCIONES DE VISTAS
function display_hero_section() {
    ?>
    <div class="hero-section">
        <div class="hero-content text-center text-white">
            <h1 class="display-4 fw-bold">Bienvenido a <?= SITE_NAME ?></h1>
            <p class="lead">Descubre el lujo y la comodidad en nuestro exclusivo hotel</p>
            <?php if (!user_logged_in()): ?>
                <a href="register.php" class="btn btn-primary btn-lg me-2">Registrarse</a>
                <a href="login.php" class="btn btn-outline-light btn-lg">Iniciar Sesión</a>
            <?php else: ?>
                <a href="availability.php" class="btn btn-primary btn-lg">Reservar Ahora</a>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

function display_rooms($rooms) {
    foreach ($rooms as $room) {
        ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="<?= SITE_URL ?>/assets/images/<?= $room['image_url'] ?>" class="card-img-top">
                <div class="card-body">
                    <h5 class="card-title"><?= $room['room_type'] ?></h5>
                    <p class="card-text"><?= $room['description'] ?></p>
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item">
                            <i class="fas fa-bed"></i> Capacidad: <?= $room['capacity'] ?> personas
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-dollar-sign"></i> Precio: $<?= $room['price_per_night'] ?> por noche
                        </li>
                    </ul>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="book.php?room_id=<?= $room['id'] ?>" class="btn btn-primary w-100">Reservar Ahora</a>
                </div>
            </div>
        </div>
        <?php
    }
}
?>