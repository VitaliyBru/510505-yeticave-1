<?php
require_once 'functions.php';
require_once  'artificial_bd.php';

/** @var int $id идентификатор лота */
$id = $_GET['id'] ?? null;
/** @var string $main_content содержит результат работы шаблонизатора */
$main_content = null;

if (array_key_exists($id, $lots)) {
    $lot = $lots[$id];
    $main_content = templateEngine(
        'lot',
        [
            'categories' => $categories,
            'lot' => $lot,
            'bets' => $bets
        ]
    );
} else {
    header("HTTP/1.1 404 Not Found");
    $main_content = templateEngine('404', []);
}

echo templateEngine(
    'layout',
    [
        'title' => $lot['name'],
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'user_avatar' => $user_avatar,
        'categories' => $categories,
        'main_content' => $main_content
    ]
);