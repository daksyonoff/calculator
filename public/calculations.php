<?php
session_start();
$pdo = require_once '../config/database.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

// Получение информации о продукте
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$product = null;
if ($product_id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
}

// Обработка формы расчета
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $length = floatval($_POST['length']);
    $width = floatval($_POST['width']);
    $height = floatval($_POST['height']);
    $material_thickness = floatval($_POST['material_thickness']);
    $weight = floatval($_POST['weight']);
    
    // Расчет времени обработки
    $processing_time = calculateProcessingTime($length, $width, $height, $product['material']);
    
    // Сохранение расчета
    $stmt = $pdo->prepare('INSERT INTO calculations (product_id, length, width, height, weight, material_thickness, processing_time) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$product_id, $length, $width, $height, $weight, $material_thickness, $processing_time]);

    $success = 'Расчет успешно сохранен';
}

// Получение истории расчетов
$calculations = [];
if ($product_id > 0) {
    $stmt = $pdo->prepare('SELECT * FROM calculations WHERE product_id = ? ORDER BY created_at DESC');
    $stmt->execute([$product_id]);
    $calculations = $stmt->fetchAll();
}

function calculateProcessingTime($length, $width, $height, $material) {
    $volume = $length * $width * $height;
    $base_time = $volume * 0.1; // Базовое время на единицу объема
    
    // Коэффициенты времени обработки для разных материалов
    $material_factors = [
        'steel' => 1.5,
        'aluminum' => 1.0,
        'plastic' => 0.8
    ];
    
    $factor = isset($material_factors[$material]) ? $material_factors[$material] : 1.0;
    return round($base_time * $factor);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Расчет параметров | Технологический Калькулятор</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-4">
        <?php if ($product): ?>
            <h2>Расчет параметров: <?php echo htmlspecialchars($product['name']); ?></h2>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Параметры расчета</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="calculator-form" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="length" class="form-label">Длина (мм)</label>
                                    <input type="number" step="0.01" class="form-control" id="length" name="length" required>
                                </div>
                                <div class="mb-3">
                                    <label for="width" class="form-label">Ширина (мм)</label>
                                    <input type="number" step="0.01" class="form-control" id="width" name="width" required>
                                </div>
                                <div class="mb-3">
                                    <label for="height" class="form-label">Высота (мм)</label>
                                    <input type="number" step="0.01" class="form-control" id="height" name="height" required>
                                </div>
                                <div class="mb-3">
                                    <label for="material_thickness" class="form-label">Толщина материала (мм)</label>
                                    <input type="number" step="0.01" class="form-control" id="material_thickness" name="material_thickness" required>
                                </div>
                                <div class="mb-3">
                                    <label for="weight" class="form-label">Вес (кг)</label>
                                    <input type="number" step="0.01" class="form-control" id="weight" name="weight" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Рассчитать</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">История расчетов</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($calculations)): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Дата</th>
                                                <th>Размеры (мм)</th>
                                                <th>Время обработки (мин)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($calculations as $calc): ?>
                                                <tr>
                                                    <td><?php echo date('d.m.Y H:i', strtotime($calc['created_at'])); ?></td>
                                                    <td><?php echo $calc['length'] . 'x' . $calc['width'] . 'x' . $calc['height']; ?></td>
                                                    <td><?php echo $calc['processing_time']; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">История расчетов пуста</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                Изделие не найдено. <a href="products.php">Вернуться к списку изделий</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/main.js"></script>
</body>
</html>
