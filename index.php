<?php
require_once 'functions.php';
require_once 'artificial_bd.php';
require_once 'authorization.php';

// устанавливаем часовой пояс в Московское время
date_default_timezone_set('Europe/Moscow');

/** @var string $main_content содержит результат работы шаблонизатора */
$main_content = templateEngine(
    'index',
    [
        'lots' => $lots
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