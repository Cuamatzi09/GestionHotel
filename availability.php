<?php
$page_title = "Consultar Disponibilidad";
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/header.php';

// Obtener tipos de habitación para el filtro
$room_types = $pdo->query("SELECT DISTINCT room_type FROM rooms")->fetchAll();

// Procesar búsqueda de disponibilidad
$available_rooms = [];
$search_params = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];
    $room_type = $_POST['room_type'];
    $adults = $_POST['adults'];
    $children = $_POST['children'];
    
    $search_params = compact('check_in', 'check_out', 'room_type', 'adults', 'children');
    
    // Validar fechas
    $today = date('Y-m-d');
    if ($check_in < $today) {
        $error = "La fecha de entrada no puede ser en el pasado.";
    } elseif ($check_out <= $check_in) {
        $error = "La fecha de salida debe ser posterior a la fecha de entrada.";
    } else {
        // Consultar habitaciones disponibles
        $sql = "
            SELECT r.* 
            FROM rooms r
            WHERE r.room_type LIKE :room_type
            AND r.capacity >= :capacity
            AND r.id NOT IN (
                SELECT res.room_id 
                FROM reservations res
                WHERE res.status = 'confirmed'
                AND (
                    (res.check_in <= :check_out AND res.check_out >= :check_in)
                )
            )
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':room_type' => $room_type === 'all' ? '%' : $room_type,
            ':capacity' => $adults + $children,
            ':check_in' => $check_in,
            ':check_out' => $check_out
        ]);
        
        $available_rooms = $stmt->fetchAll();
        
        if (empty($available_rooms)) {
            $info = "No hay habitaciones disponibles para los criterios seleccionados. Intenta con otras fechas o tipo de habitación.";
        }
    }
}
?>

<div class="row">
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-search"></i> Buscar Disponibilidad</h5>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="post">
                    <div class="mb-3">
                        <label for="check_in" class="form-label">Fecha de Entrada</label>
                        <input type="date" class="form-control" id="check_in" name="check_in" 
                               value="<?php echo $search_params['check_in'] ?? ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="check_out" class="form-label">Fecha de Salida</label>
                        <input type="date" class="form-control" id="check_out" name="check_out" 
                               value="<?php echo $search_params['check_out'] ?? ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="room_type" class="form-label">Tipo de Habitación</label>
                        <select class="form-select" id="room_type" name="room_type">
                            <option value="all">Todos los tipos</option>
                            <?php foreach ($room_types as $type): ?>
                                <option value="<?php echo $type['room_type']; ?>"
                                    <?php if (($search_params['room_type'] ?? '') === $type['room_type']) echo 'selected'; ?>>
                                    <?php echo $type['room_type']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="adults" class="form-label">Adultos</label>
                        <select class="form-select" id="adults" name="adults">
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <option value="<?php echo $i; ?>"
                                    <?php if (($search_params['adults'] ?? 2) == $i) echo 'selected'; ?>>
                                    <?php echo $i; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="children" class="form-label">Niños</label>
                        <select class="form-select" id="children" name="children">
                            <?php for ($i = 0; $i <= 4; $i++): ?>
                                <option value="<?php echo $i; ?>"
                                    <?php if (($search_params['children'] ?? 0) == $i) echo 'selected'; ?>>
                                    <?php echo $i; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Buscar Habitaciones
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <?php if (isset($info)): ?>
            <div class="alert alert-info">
                <?php echo $info; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($available_rooms)): ?>
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-check-circle"></i> Habitaciones Disponibles</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Mostrando resultados para: 
                        <?php echo date('d/m/Y', strtotime($search_params['check_in'])); ?> - 
                        <?php echo date('d/m/Y', strtotime($search_params['check_out'])); ?>, 
                        <?php echo $search_params['adults']; ?> Adultos, 
                        <?php echo $search_params['children']; ?> Niños
                    </p>
                    
                    <div class="row">
                        <?php foreach ($available_rooms as $room): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <img src="<?php echo SITE_URL; ?>/assets/images/<?php echo $room['image_url']; ?>" 
                                         class="card-img-top" alt="<?php echo $room['room_type']; ?>">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $room['room_type']; ?></h5>
                                        <p class="card-text"><?php echo $room['description']; ?></p>
                                        <ul class="list-group list-group-flush mb-3">
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
                                    <div class="card-footer bg-transparent">
                                        <a href="book.php?room_id=<?php echo $room['id']; ?>&check_in=<?php echo $search_params['check_in']; ?>&check_out=<?php echo $search_params['check_out']; ?>&adults=<?php echo $search_params['adults']; ?>&children=<?php echo $search_params['children']; ?>" 
                                           class="btn btn-primary w-100">
                                            <i class="fas fa-book"></i> Reservar Ahora
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>