<?php
require_once 'functions.php';
require_once  'artificial_bd.php';

/** @var int $id идентификатор лота */
$id = $_GET['id'] ?? null;
/** @var string $main_content содержит результат работы шаблонизатора */
$main_content = null;
/** @var bool $bet_done «true» когда ставка сделана*/
$bet_done = false;

if (array_key_exists($id, $lots)) {
    $lot = $lots[$id];

    //получить данные по ставкам
    $my_bets = array();
    if (isset($_COOKIE['my_bets'])) {
        $my_bets = json_decode($_COOKIE['my_bets'], true);
    }

    //проверить есть ли моя ставка $bet_done = true
    $bet_done = array_key_exists($id, $my_bets);
    if ($bet_done) {
        $tmp[] = $my_bets[$id];
        foreach ($bets as $row) {
            $tmp[] = $row;
        }
        $bets = $tmp;
    }

    $bet_amounts = getBetAmounts($lot, $bets);

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$bet_done) {
        $bet_sent = null;
        if (isset($_POST['cost'])) {
            $bet_sent = $_POST['cost'];
        }
        if (isBetCorrect($bet_sent, $bet_amounts['not_less'])) {
            $bet = [
                'name' => $user_name, //после подключения бд заменить на user_id
                'price' => $bet_sent,
                'lot_id' => $id,
                'ts' => strtotime('now')
            ];

            // заменить на работу с бд
            $my_bets[$id] = $bet;
            $my_bets_json = json_encode($my_bets);
            setcookie('my_bets', $my_bets_json);

            header('Location: /my-lots.php');
        }
    }


    $main_content = templateEngine(
        'lot',
        [
            'categories' => $categories,
            'lot' => $lot,
            'bets' => $bets,
            'bet_amounts' => $bet_amounts,
            'bet_done' => $bet_done
        ]
    );
} else {
    $lot['name'] = 'Ошибка 404';
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