<?php
require_once 'functions.php';
require_once 'mysql_helper.php';
require_once 'init.php';
require_once 'vendor/autoload.php';

// получаем ставки по закрывшемся лотам без победителей
$bets = getBetsOnClosedLotsWithoutWinner($link);
// определяем ставки победителей
$winner_list = getListWinnerBets($bets);

// подготавливаем данные для отправки уведомлений на почту о выиграшной ставке
if (!empty($winner_list)) {
    $transport = (new Swift_SmtpTransport('smtp.mail.ru', 465, 'ssl'))
        ->setUsername('doingsdone@mail.ru')
        ->setPassword('rds7BgcL');
    $mailer = new Swift_Mailer($transport);
}

// заносим данные о победителе в лот и отсылаем победителю письмо с уведомлением
foreach ($winner_list as $lot_id => $winner_bet) {
    if (setWinnerId($link, $winner_bet['user_id'], $lot_id)) {
        $lot = getOneLot($link, $lot_id);
        $user = getUserFromId($link, $winner_bet['user_id']);
        if (!empty($lot) && !empty($user)) {
            $message_body = templateEngine('email', ['user' => $user, 'lot' => $lot]);
            $message = (new Swift_Message('Ваша ставка победила'))
                ->setFrom(['doingsdone@mail.ru' => 'Интернет Аукцион "YetiCave"'])
                ->setTo($user['email'], $user['name'])
                ->setBody($message_body, 'text/html');
            $mailer->send($message);
        }
    }
}