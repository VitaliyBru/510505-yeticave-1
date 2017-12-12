<?php
require_once 'functions.php';
require_once 'mysql_helper.php';
require_once 'authorization.php';
require_once 'init.php';

/** @var int $id идентификатор лота */
$id = intval($_GET['id'] ?? null);
/** @var bool $bet_done «true» когда ставка сделана*/
$bet_done = false;

try {
    /** @var array $lot список информации по лоту */
    $lot = getOneLot($link, $id);
    if (empty($lot)) {
        mysqli_close($link);
        header("Location: /error_404.php", true, 404);
        exit();
    }
    /** @var bool $bet_error флаг ошибки */
    $bet_error = false;
    /** @var array $categories список категорий */
    $categories = getCategories($link);
    /** @var array $bets список всех ставок сделанных на конкретный лот */
    $bets = getBetsForLot($link, $id);
    /** @var array $bet_amounts минимально возможная ставка «not_less» и текущая стоимость лота «current» */
    $bet_amounts = getBetAmounts($lot, $bets);
    /** @var bool $bet_done пользователь уже делал ставку на этотом лоте */
    $bet_done = in_array($user_id, array_column($bets, 'user_id'));
    /** @var bool $bet_forbidden true когда пользователю запрещено делать ставку */
    $bet_forbidden = ($bet_done or !$is_auth or ($lot['author_id'] === $user_id));

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$bet_forbidden) {
        $bet_sent = null;
        if (isset($_POST['cost'])) {
            $bet_sent = $_POST['cost'];
            $bet_error = true;
        }
        if (isBetCorrect($bet_sent, $bet_amounts['not_less'])) {
            /** @var array $bet ставка пользователя */
            $bet = [
                'user_id' => $user_id,
                'price' => (int)$bet_sent,
                'lot_id' => $id
            ];

            if (setInTable($link, $bet, 'bets')) {
                header('Location: /my-lots.php');
                exit();
            }
        }
    }
} catch (Exception $e) {
    mysqli_close($link);
    showErrors($e);
    exit();
}

$nav_panel = templateEngine('nav_panel', ['categories' => $categories]);
/** @var string $main_content содержит результат работы шаблонизатора */
$main_content = templateEngine(
    'lot',
    [
        'nav_panel' => $nav_panel,
        'lot' => $lot,
        'bets' => $bets,
        'bet_amounts' => $bet_amounts,
        'bet_forbidden' => $bet_forbidden,
        'bet_error' => $bet_error
    ]
);
echo templateEngine(
    'layout',
    [
        'title' => secure($lot['name']),
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'user_avatar' => $user_avatar,
        'nav_panel' => $nav_panel,
        'main_content' => $main_content
    ]
);