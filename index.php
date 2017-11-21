<?php
require_once 'functions.php';
$is_auth = (bool) rand(0, 1);

$user_name = 'Константин';
$user_avatar = 'img/user.jpg';

// устанавливаем часовой пояс в Московское время
date_default_timezone_set('Europe/Moscow');

// записать в эту переменную оставшееся время в этом формате (ЧЧ:ММ)
$lot_time_remaining = "00:00";

// временная метка для полночи следующего дня
$tomorrow = strtotime('tomorrow midnight');

// временная метка для настоящего времени
$now = strtotime('now');

// далее нужно вычислить оставшееся время до начала следующих суток и записать его в переменную $lot_time_remaining
$delta_time_in_minutes = floor(($tomorrow - $now) / 60);
$hours = sprintf("%02d", floor($delta_time_in_minutes / 60));
$minutes = sprintf("%02d", ($delta_time_in_minutes % 60));
$lot_time_remaining = $hours . ":" . $minutes;

$categories = ['Доски и лыжи', 'Крепления', 'Ботинки', 'Одежда', 'Инструменты', 'Разное'];

$lots = [
    ['name' => '2014 Rossignol District Snowboard', 'category' => 'Доски и лыжи', 'price_origin' => 10999, 'img_url' => 'img/lot-1.jpg'],
    ['name' => 'DC Ply Mens 2016/2017 Snowboard', 'category' => 'Доски и лыжи', 'price_origin' => 159999, 'img_url' => 'img/lot-2.jpg'],
    ['name' => 'Крепления Union Contact Pro 2015 года размер L/XL', 'category' => 'Крепления', 'price_origin' => 8000, 'img_url' => 'img/lot-3.jpg'],
    ['name' => 'Ботинки для сноуборда DC Mutiny Charocal', 'category' => 'Ботинки', 'price_origin' => 10999, 'img_url' => 'img/lot-4.jpg'],
    ['name' => 'Куртка для сноуборда DC Mutiny Charocal', 'category' => 'Одежда', 'price_origin' => 7500, 'img_url' => 'img/lot-5.jpg'],
    ['name' => 'Маска Oakley Canopy', 'category' => 'Разное', 'price_origin' => 5400, 'img_url' => 'img/lot-6.jpg']
];

/** @var string $main_content содержит результат работы шаблонизатора */
$main_content = templateEngine(
    'index',
    [
        'lots' => $lots,
        'lot_time_remaining' => $lot_time_remaining
    ]
);
echo templateEngine(
    'layout',
    [
        'title' => 'Главная',
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'user_avatar' => $user_avatar,
        'categories' => $categories,
        'main_content' => $main_content
    ]
);