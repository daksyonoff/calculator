<?php
session_start();
$pdo = require_once '../config/database.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

// Добавление нового изделия
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['description'], $_POST['material'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $material = $_POST['material'];

    $stmt = $pdo->prepare('INSERT INTO products (name, description, material) VALUES (?, ?, ?)');
    $stmt->execute([$name, $description, $material]);
    header('Location: /products.php');
    exit;
}

// Удаление изделия
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $product_id_to_delete = (int)$_POST['product_id'];

    if ($product_id_to_delete > 0) {
        try {
            $pdo->beginTransaction();

            $stmt_delete_calc = $pdo->prepare('DELETE FROM calculations WHERE product_id = ?');
            $stmt_delete_calc->execute([$product_id_to_delete]);

            $stmt_delete_prod = $pdo->prepare('DELETE FROM products WHERE id = ?');
            $stmt_delete_prod->execute([$product_id_to_delete]);

            $pdo->commit();
            $_SESSION['success_message'] = 'Изделие и его расчеты успешно удалены.';
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['error_message'] = 'Ошибка при удалении изделия: ' . $e->getMessage();
        }
        header('Location: /products.php');
        exit;
    }
}

// Получение списка изделий
$stmt = $pdo->query('SELECT * FROM products ORDER BY created_at DESC');
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Изделия | Технологический Калькулятор</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-4">
        <?php
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . $_SESSION['success_message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            unset($_SESSION['success_message']);
        }
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . $_SESSION['error_message'] . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
            unset($_SESSION['error_message']);
        }
        ?>
        <div class="row">
            <div class="col-md-12 mb-4">
                <h2>Управление изделиями</h2>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    Добавить изделие
                </button>

            </div>
        </div>

        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p class="card-text"><small class="text-muted">Материал: <?php echo htmlspecialchars($product['material']); ?></small></p>
                            <a href="calculations.php?product_id=<?php echo $product['id']; ?>" class="btn btn-primary">Расчет параметров</a>
                            <form method="POST" action="products.php" style="display: inline;" onsubmit="return confirm('Вы уверены, что хотите удалить это изделие?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm" title="Удалить">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Добавить новое изделие</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="name" class="form-label">Название</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Описание</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="material" class="form-label">Материал</label>
                            <select class="form-select" id="material" name="material" required>
                                <option value="Сталь 45">Сталь 45</option>
                                <option value="Алюминий">Алюминий</option>
                                <option value="Сталь 40Х">Сталь 40Х</option>
                                <option value="Чугун">Чугун</option>
                                <option value="Бронза">Бронза</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
