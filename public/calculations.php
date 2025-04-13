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
        'steel' => 1.5,
        'aluminum' => 1.0,
        'plastic' => 0.8,
        'Сталь 45' => 1.5,
        'Сталь 40Х' => 1.4,
        'Алюминий' => 1.0
    ];

    return $material_factors[$material] ?? 1.0;
}

$cutting_speed = null;
$feed_rate = null;
$spindle_speed = null;
$surface_roughness = null;
$cutting_depth = isset($_POST['cutting_depth']) ? floatval($_POST['cutting_depth']) : 0;
$operation_type = $_POST['operation_type'] ?? '';
$material = $_POST['material'] ?? '';
$tool_material = $_POST['tool_material'] ?? '';


if ($cutting_depth > 0) {
    if ($operation_type === 'Точение') {
        if ($material === 'Сталь 45' && $tool_material === 'Т15К6') {
            $cutting_speed = 120 * pow((20 / $cutting_depth), 0.15);
            $feed_rate = 0.25;
        } elseif ($material === 'Сталь 40Х' && $tool_material === 'Т15К6') {
            $cutting_speed = 100 * pow((20 / $cutting_depth), 0.15);
            $feed_rate = 0.2;
        } elseif ($material === 'Алюминий' && $tool_material === 'ВК8') {
            $cutting_speed = 300 * pow((20 / $cutting_depth), 0.15);
            $feed_rate = 0.15;
        }
    } elseif ($operation_type === 'Фрезерование') {
        if ($material === 'Сталь 45' && $tool_material === 'Р6М5') {
            $cutting_speed = 35 * pow((20 / $cutting_depth), 0.15);
            $feed_rate = 0.1;
        }
    }

    if ($cutting_speed !== null) {
        $diameter = 100;
        $spindle_speed = round((1000 * $cutting_speed) / (M_PI * $diameter));
    }

    if ($feed_rate !== null) {
        $surface_roughness = $feed_rate * 20;
    }
}

// Обработка формы расчета
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $product) {
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

    $success = 'Расчет успешно сохранен';
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <?php if ($product): ?>
        <h2>Расчет параметров: <?php echo htmlspecialchars($product['name']); ?></h2>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <div class="row">
        <?php if ($product): ?>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Параметры расчета</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="calculator-form" class="needs-validation" novalidate>
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
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="tool_material" class="form-label">Инструмент</label>
                                <select class="form-control" id="tool_material" name="tool_material" required>
                                    <option value="">Выберите</option>
                                    <option value="Т15К6">Т15К6</option>
                                    <option value="ВК8">ВК8</option>
                                    <option value="Р6М5">Р6М5</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="cutting_depth" class="form-label">Глубина резания (мм)</label>
                                <input type="number" step="0.01" class="form-control" id="cutting_depth" name="cutting_depth" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Рассчитать</button>
                        </form>
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
                                        <td><?= htmlspecialchars($calc['operation_type']) ?></td>
                                        <td><?= htmlspecialchars($calc['tool_material']) ?></td>
                                        <td><?= htmlspecialchars($calc['cutting_depth']) ?></td>
                                        <td><?= htmlspecialchars($calc['cutting_speed']) ?></td>
                                        <td><?= htmlspecialchars($calc['feed_rate']) ?></td>
                                        <td><?= htmlspecialchars($calc['spindle_speed']) ?></td>
                                        <td><?= htmlspecialchars($calc['surface_roughness']) ?></td>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
