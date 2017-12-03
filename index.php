<?php
require_once 'functions.php';
require_once 'artificial_bd.php';
require_once 'authorization.php';
require_once 'init.php';

// устанавливаем часовой пояс в Московское время
date_default_timezone_set('Europe/Moscow');

// получаем из бд список категорий
$categories = getCategories($link);
if (empty($categories)) {
    $error = getMysqliError($link);
    showErrors($error);
    exit();
}

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