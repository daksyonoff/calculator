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
function getMaterialFactor(string $material): float {
    $material_factors = [
        'Сталь 45' => 1.5,
        'Сталь 40Х' => 1.4,
        'Алюминий' => 1.0,
        'Чугун' => 1.3,
        'Бронза' => 1.2
    ];

    return $material_factors[$material] ?? 1.0;
}


$operations = [
    'Точение' => [
        'Сталь 45' => [
            'T15K6' => ['speed' => 120, 'feed' => 0.25],
        ],
        'Сталь 40Х' => [
            'T15K6' => ['speed' => 100, 'feed' => 0.2],
        ],
        'Алюминий' => [
            'BK8' => ['speed' => 300, 'feed' => 0.15],
        ],
        'Чугун' => [
            'BK8' => ['speed' => 90, 'feed' => 0.25],
        ],
        'Бронза' => [
            'BK8' => ['speed' => 110, 'feed' => 0.25],
        ],
    ],
    'Фрезерование' => [
        'Сталь 45' => [
            'P6M5' => ['speed' => 35, 'feed' => 0.1],
        ],
        'Чугун' => [
            'BK8' => ['speed' => 70, 'feed' => 0.2],
        ],
        'Бронза' => [
            'P6M5' => ['speed' => 90, 'feed' => 0.2],
        ],
        'Алюминий' => [
            'BK8' => ['speed' => 300, 'feed' => 0.15],
        ],
    ],
];



$cutting_speed = $feed_rate = $spindle_speed = $surface_roughness = null;
$cutting_depth = isset($_POST['cutting_depth']) ? floatval($_POST['cutting_depth']) : 0;
$operation_type = $_POST['operation_type'] ?? '';
$material = $_POST['material'] ?? '';
$tool_material = $_POST['tool_material'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['calculate'])) {

    if ($cutting_depth > 0 && isset($operations[$operation_type][$material][$tool_material])) {
        $base = $operations[$operation_type][$material][$tool_material];
        $cutting_speed = $base['speed'] * pow((20 / $cutting_depth), 0.15);
        $feed_rate = $base['feed'];

        $diameter = 100;
        $spindle_speed = round((1000 * $cutting_speed) / (M_PI * $diameter));

        $surface_roughness = $feed_rate * 20;
    } else {
        $_SESSION['error_message'] = 'Ошибка: параметры не рассчитаны. Проверьте правильность выбора операции, материала и инструмента.';
    }
}


// Обработка формы расчета
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $product) {
    if ($cutting_speed !== null && $feed_rate !== null && $spindle_speed !== null && $surface_roughness !== null)
    try {
        $stmt = $pdo->prepare('INSERT INTO calculations (
            product_id, material, operation_type, tool_material, cutting_depth,
            cutting_speed, feed_rate, spindle_speed, surface_roughness
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');

        $stmt->execute([
            $product_id ?: null,
            $material,
            $operation_type,
            $tool_material,
            $cutting_depth,
            $cutting_speed,
            $feed_rate,
            $spindle_speed,
            $surface_roughness
        ]);

        $_SESSION['success_message'] = 'Расчет успешно сохранен';
    } catch (PDOException $e) {
        $_SESSION['error_message'] = 'Ошибка при сохранении расчета: ' . $e->getMessage();
    }
}



if (isset($_POST['delete_calculation'])) {
    $calculation_id = (int)$_POST['calculation_id'];

    if ($calculation_id > 0) {
        try {
            $stmt_delete_calculation = $pdo->prepare('DELETE FROM calculations WHERE id = ?');
            $stmt_delete_calculation->execute([$calculation_id]);

            $_SESSION['success_message'] = 'Расчет успешно удален.';
        } catch (PDOException $e) {
            $_SESSION['error_message'] = 'Ошибка при удалении расчета: ' . $e->getMessage();
        }
        header('Location: /calculations.php?product_id=' . $product_id);
        exit;
    }
}
// Получение истории расчетов
$calculations = [];
if ($product_id > 0) {
    $stmt = $pdo->prepare('SELECT c.*, p.name as product_name FROM calculations c
                          LEFT JOIN products p ON c.product_id = p.id
                          WHERE c.product_id = ?
                          ORDER BY c.created_at DESC');
    $stmt->execute([$product_id]);
} else {
    $stmt = $pdo->query('SELECT c.*, p.name as product_name FROM calculations c
                        LEFT JOIN products p ON c.product_id = p.id
                        ORDER BY c.created_at DESC');
}
$calculations = $stmt->fetchAll();

$factor = getMaterialFactor($material);

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>История расчетов | Технологический Калькулятор</title>
    <link href=" https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css " rel="stylesheet">
    <link href=" https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css " rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <?php if ($product): ?>
        <h2>Расчет параметров: <?php echo htmlspecialchars($product['name']); ?></h2>
    <?php endif; ?>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
    <?php endif; ?>
    <div class="container mt-4">
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="bi bi-exclamation-triangle-fill" style="font-size: 1.5em; color: #f39c12; margin-right: 10px;"></i>
            <p>Эти расчеты основаны на теоретических значениях и предназначены только для учебных целей. Фактические результаты будут отличаться.</p>
        </div>
    <div class="row">
        <?php if ($product): ?>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Параметры расчета</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" id="calculator-form" class="needs-validation" novalidate>
                            <input type="hidden" name="calculate" value="1">
                            <div class="mb-3">
                                <label for="operation_type" class="form-label">Тип операции</label>
                                <select class="form-control" id="operation_type" name="operation_type" required>
                                    <option value="">Выберите</option>
                                    <option value="Точение">Точение</option>
                                    <option value="Фрезерование">Фрезерование</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="material" class="form-label">Материал</label>
                                <select class="form-control" id="material" name="material" required>
                                    <option value="">Выберите</option>
                                    <option value="Сталь 45">Сталь 45</option>
                                    <option value="Сталь 40Х">Сталь 40Х</option>
                                    <option value="Алюминий">Алюминий</option>
                                    <option value="Чугун">Чугун</option>
                                    <option value="Бронза">Бронза</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="tool_material" class="form-label">Инструмент</label>
                                <select class="form-control" id="tool_material" name="tool_material" required>
                                    <option value="">Выберите</option>
                                    <option value="T15K6">T15K6</option>
                                    <option value="BK8">BK8</option>
                                    <option value="P6M5">P6M5</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="cutting_depth" class="form-label">Глубина резания (мм)</label>
                                <input type="number" step="0.01" class="form-control" id="cutting_depth" name="cutting_depth" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Рассчитать</button>
                        </form>
                        <?php if ($cutting_speed !== null && $feed_rate !== null && $spindle_speed !== null && $surface_roughness !== null): ?>
                            <div class="mt-4 alert alert-info">
                                <h5>Результаты расчета:</h5>
                                <ul>
                                    <li><strong>Скорость резания:</strong> <?= round($cutting_speed, 2) ?> м/мин</li>
                                    <li><strong>Подача:</strong> <?= round($feed_rate, 3) ?> мм/об</li>
                                    <li><strong>Обороты шпинделя:</strong> <?= round($spindle_speed) ?> об/мин</li>
                                    <li><strong>Шероховатость поверхности Ra:</strong> <?= round($surface_roughness, 2) ?> мкм</li>
                                </ul>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        <?php endif; ?>
            <div class="<?php echo $product ? 'col-md-6' : 'col-md-12'; ?>">
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
                                    <th>Изделие</th>
                                    <th>Операция</th>
                                    <th>Инструмент</th>
                                    <th>Глубина<br>(мм)</th>
                                    <th>Скорость<br>(м/мин)</th>
                                    <th>Подача<br>(мм/об)</th>
                                    <th>Шпиндель<br>(об/мин)</th>
                                    <th>Ra<br>(мкм)</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($calculations as $calc): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($calc['product_name'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($calc['operation_type'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($calc['tool_material'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($calc['cutting_depth'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($calc['cutting_speed'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($calc['feed_rate'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($calc['spindle_speed'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($calc['surface_roughness'] ?? '') ?></td>
                                        <td>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="calculation_id" value="<?= htmlspecialchars($calc['id']) ?>">
                                            <button type="submit" name="delete_calculation" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Вы уверены, что хотите удалить этот расчет?');"
                                                    title="Удалить расчет">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        </td>
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
</div>

<script src=" https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js "></script>
<script src="js/main.js"></script>
</body>
</html>
