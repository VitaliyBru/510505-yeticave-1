<?php
require_once 'functions.php';
require_once 'artificial_bd.php';

date_default_timezone_set('Europe/Moscow');

$errors = [
    'form' => false,
    'name' => false,
    'description' => false,
    'category' => false,
    'img_url' => true,
    'price_origin' => false,
    'price_step' => false,
    'date_end' => false
];
$lot = [
    'name' => '',
    'description' => '',
    'category' => '',
    'img_url' => '',
    'price_origin' => '',
    'price_step' => '',
    'date_end' => ''
];
$e_rules = [
    'name' => 'isEmpty',
    'description' => 'isEmpty',
    'category' => 'isNotCategory',
    'img_url' => 'isEmpty',
    'price_origin' => 'isNotPositiveNumber',
    'price_step' => 'isNotPositiveNumber',
    'date_end' => 'isNotFutureDate'
];
//удалить после подключения бд
$first_visit = true;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['lot'])) {
        $lot = $_POST['lot'];
    }
    if (isset($_FILES) && $_FILES['image']['tmp_name']) {
        $lot['img_url'] = getImageFromForm('image');
    }
    $lot['category'] = array('value' => $lot['category'], 'categories' => $categories);
    foreach ($lot as $key => $value) {
        $errors[$key] = call_user_func($e_rules[$key], $value);
        if ($errors[$key] && !$errors['form']) {
            $errors['form'] = true;
        }
    }
    $lot['category'] = $lot['category']['value'];
    if (!$errors['form']) {
        // здесь будет: запись в бд и получение айди
        // здесь будет: перенаправление на страницу лота
        // удалить после подключения бд
        $main_content = templateEngine(
            'lot',
            [
                'categories' => $categories,
                'bets' => $bets,
                'lot' => $lot
            ]
        );
    }
    // удалить после подключения бд
    $first_visit = false;
}
// Удалить иф после подключения бд
if ($errors['form'] || $first_visit) {
    $main_content = templateEngine(
        'add',
        [
            'categories' => $categories,
            'errors' => $errors,
            'lot' => $lot
        ]
    );
}
echo templateEngine(
    'layout',
    [
        'title' => 'Добавление лота',
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'user_avatar' => $user_avatar,
        'categories' => $categories,
        'main_content' => $main_content
    ]
);