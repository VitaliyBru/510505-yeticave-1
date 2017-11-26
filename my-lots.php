<?php
require_once 'functions.php';
require_once  'artificial_bd.php';

$lots_with_my_bets = null;

if (isset($_COOKIE['my_bets'])) {
    $my_bets = json_decode($_COOKIE['my_bets'], true);
    foreach ($my_bets as $bet) {
        unset($bet['name']);
        $lots_with_my_bets[] = array_merge($lots[$bet['lot_id']], $bet);
        $lots_with_my_bets = array_reverse($lots_with_my_bets);
    }
}

if (isset($lots_with_my_bets)) {
    $main_content = templateEngine(
        'my-lots',
        [
            'categories' => $categories,
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
        'categories' => $categories,
        'main_content' => $main_content
    ]
);