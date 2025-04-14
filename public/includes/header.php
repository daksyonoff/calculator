<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="/">Технологический Калькулятор</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?php if ($currentPage == 'products.php') echo 'active'; ?>" href="../products.php">Изделия</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if ($currentPage == 'calculations.php') echo 'active'; ?>" href="../calculations.php">Расчеты</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if ($currentPage == 'guide.php') echo 'active'; ?>" href="../guide.php">Справочник</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($currentPage == 'logout.php') echo 'active'; ?>" href="../logout.php">Выход</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($currentPage == 'login.php') echo 'active'; ?>" href="../login.php">Вход</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($currentPage == 'register.php') echo 'active'; ?>" href="../register.php">Регистрация</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
