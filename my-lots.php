<?php
require_once 'functions.php';
require_once  'artificial_bd.php';
require_once 'authorization.php';
require_once 'init.php';

if (!$is_auth) {
    header('Location: /login.php');
}

// устанавливаем часовой пояс в Московское время
date_default_timezone_set('Europe/Moscow');

try {
    // получаем из бд список категорий
    /** @var array $categories список категорий */
    $categories = getCategories($link);

    // получаем лоты на которые пользователь делал ставки и его ставки
    /** @var array $lots_with_my_bets список лотов со ставками пользователя и его ставки */
    $lots_with_my_bets = getLotsWithUsersBets($link, $user_id);
} catch (Exception $e) {
    mysqli_close($link);
    showErrors($e);
    exit();
}

$nav_panel = templateEngine('nav_panel', ['categories' => $categories]);
if (!empty($lots_with_my_bets)) {
    $main_content = templateEngine(
        'my-lots',
        [
            'nav_panel' => $nav_panel,
            'lots_with_my_bets' => $lots_with_my_bets
        ]
    );
} else {
    $main_content = '<h2>Вы еще не участвовали в торгах</h2><a href="index.php">Вернуться не главную страницу</a>';
}
echo templateEngine(
    'layout',
    [
        'title' => 'Мои ставки',
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'user_avatar' => $user_avatar,
        'nav_panel' => $nav_panel,
        'main_content' => $main_content
    ]
);