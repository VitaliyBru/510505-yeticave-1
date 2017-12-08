<?php
require_once 'functions.php';
require_once 'mysql_helper.php';
require_once 'init.php';

$bets = getBetsOnClosedLotsWithoutWinner($link);
$winner_list = getListWinnerBets($bets);

foreach ($winner_list as $lot_id => $winner_bet) {
    if (setWinnerId($link, $winner_bet['user_id'], $lot_id)) {

    }
}