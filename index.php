<?php
require_once 'functions.php';
require_once 'authorization.php';
require_once 'init.php';

// устанавливаем часовой пояс в Московское время
date_default_timezone_set('Europe/Moscow');

try {
    // получаем из бд список категорий
    /** @var array $categories список категорий*/
    $categories = getCategories($link);

    // получаем из бд список активных лотов
    /** @var array $lots список лотов*/
    $lots = getActiveLots($link);
} catch (Exception $e) {
    mysqli_close($link);
    showErrors($e);
    exit();
}
mysqli_close($link);

/** @var string $main_content содержит результат работы шаблонизатора */
$main_content = templateEngine(
    'index',
    [
        'categories' => $categories,
        'lots' => $lots
    ]
);
$nav_panel = templateEngine('nav_panel', ['categories' => $categories]);
echo templateEngine(
    'layout',
    [
        'title' => 'Главная',
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'user_avatar' => $user_avatar,
        'nav_panel' => $nav_panel,
        'main_content' => $main_content
    ]
);