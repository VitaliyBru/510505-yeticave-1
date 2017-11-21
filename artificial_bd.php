<?php
$lots = [
    ['id' => 0, 'name' => '2014 Rossignol District Snowboard', 'category' => 'Доски и лыжи', 'price_origin' => 10999, 'img_url' => 'img/lot-1.jpg',
        'description' => ''],
    ['id' => 1, 'name' => 'DC Ply Mens 2016/2017 Snowboard', 'category' => 'Доски и лыжи', 'price_origin' => 159999, 'img_url' => 'img/lot-2.jpg',
        'description' => 'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив
                    снег мощным щелчкоми четкими дугами. Стекловолокно Bi-Ax, уложенное в двух направлениях, наделяет этот снаряд
                    отличной гибкостью и отзывчивостью, а симметричная геометрия в сочетании с классическим прогибом кэмбер
                    позволит уверенно держать высокие скорости. А если к концу катального дня сил совсем не останется, просто
                    посмотрите на Вашу доску и улыбнитесь, крутая графика от Шона Кливера еще никого не оставляла равнодушным.'],
    ['id' => 2, 'name' => 'Крепления Union Contact Pro 2015 года размер L/XL', 'category' => 'Крепления', 'price_origin' => 8000, 'img_url' => 'img/lot-3.jpg',
        'description' => ''],
    ['id' => 3, 'name' => 'Ботинки для сноуборда DC Mutiny Charocal', 'category' => 'Ботинки', 'price_origin' => 10999, 'img_url' => 'img/lot-4.jpg',
        'description' => ''],
    ['id' => 4, 'name' => 'Куртка для сноуборда DC Mutiny Charocal', 'category' => 'Одежда', 'price_origin' => 7500, 'img_url' => 'img/lot-5.jpg',
        'description' => ''],
    ['id' => 5, 'name' => 'Маска Oakley Canopy', 'category' => 'Разное', 'price_origin' => 5400, 'img_url' => 'img/lot-6.jpg',
        'description' => '']
];

$categories = [
    ['id' => 1, 'name' => 'Доски и лыжи'],
    ['id' => 2, 'name' => 'Крепления'],
    ['id' => 3, 'name' => 'Ботинки'],
    ['id' => 4, 'name' => 'Одежда'],
    ['id' => 5, 'name' => 'Инструменты'],
    ['id' => 6, 'name' => 'Разное']
];

$bets = [
    ['name' => 'Иван', 'price' => 11500, 'ts' => strtotime('-' . rand(1, 50) .' minute')],
    ['name' => 'Константин', 'price' => 11000, 'ts' => strtotime('-' . rand(1, 18) .' hour')],
    ['name' => 'Евгений', 'price' => 10500, 'ts' => strtotime('-' . rand(25, 50) .' hour')],
    ['name' => 'Семён', 'price' => 10000, 'ts' => strtotime('last week')]
];

$is_auth = (bool) rand(0, 1);

$user_name = 'Константин';
$user_avatar = 'img/user.jpg';