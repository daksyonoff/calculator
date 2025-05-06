<?php
session_start();
$pdo = require_once __DIR__ . '/../config/database.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Справочник технолога-машиностроителя | Техно Калькулятор</title>
    <link href=" https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css " rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <h1 class="mb-4">Справочник технолога-машиностроителя</h1>

    <div class="row">
        <div class="col-md-3">
            <div class="list-group">
                <a href="#cutting-modes" class="list-group-item list-group-item-action">Режимы резания</a>
                <a href="#products" class="list-group-item list-group-item-action">Типовые изделия</a>
                <a href="#materials" class="list-group-item list-group-item-action">Материалы</a>
                <a href="#tools" class="list-group-item list-group-item-action">Инструменты</a>
                <a href="#roughness" class="list-group-item list-group-item-action">Шероховатость</a>
            </div>
        </div>

        <div class="col-md-9">
            <section id="cutting-modes" class="mb-5">
                <h2>Режимы резания</h2>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Материал</th>
                            <th>Операция</th>
                            <th>Инструмент</th>
                            <th>Скорость (м/мин)</th>
                            <th>Подача (мм/об)</th>
                            <th>Глубина (мм)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>Сталь 45</td>
                            <td>Точение</td>
                            <td>T15K6</td>
                            <td>120</td>
                            <td>0.25</td>
                            <td>2.0</td>
                        </tr>
                        <tr>
                            <td>Сталь 40Х</td>
                            <td>Точение</td>
                            <td>T15K6</td>
                            <td>100</td>
                            <td>0.20</td>
                            <td>1.5</td>
                        </tr>
                        <tr>
                            <td>Алюминий Д16</td>
                            <td>Точение</td>
                            <td>BK8</td>
                            <td>300</td>
                            <td>0.15</td>
                            <td>2.5</td>
                        </tr>
                        <tr>
                            <td>Чугун СЧ20</td>
                            <td>Точение</td>
                            <td>BK8</td>
                            <td>90</td>
                            <td>0.25</td>
                            <td>2.0</td>
                        </tr>
                        <tr>
                            <td>Бронза БрАЖ9-4</td>
                            <td>Точение</td>
                            <td>BK8</td>
                            <td>110</td>
                            <td>0.25</td>
                            <td>2.0</td>
                        </tr>
                        <tr>
                            <td>Сталь 45</td>
                            <td>Фрезерование</td>
                            <td>P6M5</td>
                            <td>35</td>
                            <td>0.10</td>
                            <td>3.0</td>
                        </tr>
                        <tr>
                            <td>Чугун СЧ20</td>
                            <td>Фрезерование</td>
                            <td>BK8</td>
                            <td>70</td>
                            <td>0.20</td>
                            <td>3.5</td>
                        </tr>
                        <tr>
                            <td>Бронза БрАЖ9-4</td>
                            <td>Фрезерование</td>
                            <td>P6M5</td>
                            <td>90</td>
                            <td>0.20</td>
                            <td>2.5</td>
                        </tr>
                        <tr>
                            <td>Алюминий Д16</td>
                            <td>Фрезерование</td>
                            <td>BK8</td>
                            <td>300</td>
                            <td>0.15</td>
                            <td>3.0</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="products" class="mb-5">
                <h2>Типовые изделия</h2>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Изделие</th>
                                    <th>Материал</th>
                                    <th>Операция</th>
                                    <th>Инструмент</th>
                                    <th>Глубина резания (мм)</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Корпус редуктора</td>
                                    <td>Сталь 45</td>
                                    <td>Фрезерование, Точение</td>
                                    <td>P6M5 (фрезерование), T15K6 (точение)</td>
                                    <td>3.0 (фрезерование), 2.0 (точение)</td>
                                </tr>
                                <tr>
                                    <td>Вал привода</td>
                                    <td>Сталь 40Х</td>
                                    <td>Точение, Фрезерование</td>
                                    <td>T15K6 (точение), P6M5 (фрезерование)</td>
                                    <td>1.5 (точение), 2.5 (фрезерование)</td>
                                </tr>
                                <tr>
                                    <td>Крышка подшипника</td>
                                    <td>Чугун СЧ20</td>
                                    <td>Фрезерование, Сверление</td>
                                    <td>BK8</td>
                                    <td>3.5 (фрезерование), Ø8 (сверление)</td>
                                </tr>
                                <tr>
                                    <td>Фланец соединительный</td>
                                    <td>Сталь 45</td>
                                    <td>Точение</td>
                                    <td>T15K6</td>
                                    <td>2.0</td>
                                </tr>
                                <tr>
                                    <td>Шестерня цилиндрическая</td>
                                    <td>Сталь 40Х</td>
                                    <td>Фрезерование, Точение</td>
                                    <td>P6M5 (фрезерование), T15K6 (точение)</td>
                                    <td>2.5 (фрезерование), 1.5 (точение)</td>
                                </tr>
                                <tr>
                                    <td>Плита основания</td>
                                    <td>Чугун СЧ20</td>
                                    <td>Фрезерование</td>
                                    <td>BK8</td>
                                    <td>3.5</td>
                                </tr>
                                <tr>
                                    <td>Кронштейн</td>
                                    <td>Сталь 35</td>
                                    <td>Сверление, Фрезерование</td>
                                    <td>P6M5</td>
                                    <td>Ø10 (сверление), 2.5 (фрезерование)</td>
                                </tr>
                                <tr>
                                    <td>Поршень компрессора</td>
                                    <td>Алюминий Д16</td>
                                    <td>Точение</td>
                                    <td>BK8</td>
                                    <td>2.5</td>
                                </tr>
                                <tr>
                                    <td>Корпус насоса</td>
                                    <td>Бронза БрАЖ9-4</td>
                                    <td>Точение</td>
                                    <td>BK8</td>
                                    <td>2.0</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            <section id="materials" class="mb-5">
                <h2>Материалы</h2>
                <div class="card">
                    <div class="card-body">
                        <h3>Конструкционные стали</h3>
                        <p>Сталь 45 - Углеродистая конструкционная сталь</p>
                        <ul>
                            <li>Твердость: 179-229 HB</li>
                            <li>Предел прочности: 590 МПа</li>
                            <li>Применение: валы, шестерни, оси</li>
                        </ul>

                        <h3>Инструментальные стали</h3>
                        <p>Р6М5 - Быстрорежущая сталь</p>
                        <ul>
                            <li>Твердость после закалки: 62-65 HRC</li>
                            <li>Теплостойкость: до 620°C</li>
                            <li>Применение: режущий инструмент</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section id="tools" class="mb-5">
                <h2>Инструменты</h2>
                <div class="card">
                    <div class="card-body">
                        <h3>Твердосплавные пластины</h3>
                        <p>Т15К6</p>
                        <ul>
                            <li>Состав: 15% карбида титана, 6% кобальта</li>
                            <li>Применение: точение стали</li>
                            <li>Скорость резания: до 150 м/мин</li>
                        </ul>

                        <h3>Быстрорежущие инструменты</h3>
                        <p>Р6М5</p>
                        <ul>
                            <li>Состав: 6% W, 5% Mo</li>
                            <li>Применение: фрезы, сверла</li>
                            <li>Скорость резания: до 40 м/мин</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section id="roughness" class="mb-5">
                <h2>Шероховатость поверхности</h2>
                <div class="card">
                    <div class="card-body">
                        <h3>Классы шероховатости</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Класс</th>
                                    <th>Ra (мкм)</th>
                                    <th>Типовое применение</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>80</td>
                                    <td>Грубая обработка</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>40</td>
                                    <td>Получистовая обработка</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>20</td>
                                    <td>Чистовая обработка</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>10</td>
                                    <td>Тонкая обработка</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script src=" https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js "></script>
</body>
</html>