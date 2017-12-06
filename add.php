<?php
require_once 'functions.php';
require_once 'mysql_helper.php';
require_once 'authorization.php';
require_once 'init.php';

// запрет на посещение страницы неавторизованным пользователям
if (!$is_auth) {
    http_response_code(403);
    exit();
}

/** @var array $errors массив наличия ошибок заполнения формы пользователем */
$errors = [
    'form' => false,
    'name' => false,
    'description' => false,
    'category_id' => false,
    'img_url' => true,
    'price_origin' => false,
    'price_step' => false,
    'date_end' => false
];
/** @var array $lot массив заполняемый данными из формы */
$lot = [
    'name' => '',
    'description' => '',
    'category_id' => '',
    'img_url' => '',
    'price_origin' => '',
    'price_step' => '',
    'date_end' => ''
];
/** @var array $e_rules массив с именами функций для проверки на наличее ошибок в заполнении формы */
$e_rules = [
    'name' => 'isEmpty',
    'description' => 'isEmpty',
    'category_id' => 'isNotCategory',
    'img_url' => 'isEmpty',
    'price_origin' => 'isNotPositiveNumber',
    'price_step' => 'isNotPositiveNumber',
    'date_end' => 'isNotFutureDate'
];

try {
    /** @var array $categories массив со списком категорий */
    $categories = getCategories($link);
} catch (Exception $e) {
    mysqli_close($link);
    showErrors($e);
    exit();
}

// если данные отправленны методом POST авторизованным пользователем
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $is_auth) {
    if (isset($_POST['lot'])) {
        $lot = $_POST['lot'];
    }
    // если отправленно изображение сохранить в указанную директорию и вернуть ссылку в случае успеха
    if (isset($_FILES) && $_FILES['image']['tmp_name']) {
        $lot['img_url'] = getImageFromForm('image');
    }
    // подготовка данных к проверке
    $lot['category_id'] = array('id' => $lot['category_id'], 'categories' => $categories);
    // проверка на наличие ошибок в заполнении формы
    foreach ($lot as $key => $value) {
        $errors[$key] = call_user_func($e_rules[$key], $value);
        $errors['form'] = ($errors[$key] or $errors['form']);
    }
    // подготовка данных к сохранению или выводу на экран
    $lot['category_id'] = $lot['category_id']['id'];
    // если ошибок в заполнении формы не обнаружено
    if (!$errors['form']) {
        // добавляем id пользователя к данным по лоту
        $lot['author_id'] = $user_id;
        // трансформируем формат даты к формату бд
        $lot['date_end'] = date('Y-m-d', strtotime($lot['date_end']));
        try {
            // запись в бд и получение айди
            $id = setInTable($link, $lot, 'lots');
        } catch (Exception $e) {
            mysqli_close($link);
            showErrors($e);
            exit();
        }
        // перенаправление на страницу лота в случае успешного сохранения данных в базе
        if ($id) {
            header("Location: /lot.php?id=$id");
        }
    }
}
mysqli_close($link);

$nav_panel = templateEngine('nav_panel', ['categories' => $categories]);
$main_content = templateEngine(
    'add',
    [
        'categories' => $categories,
        'nav_panel' => $nav_panel,
        'errors' => $errors,
        'lot' => $lot
    ]
);
echo templateEngine(
    'layout',
    [
        'title' => 'Добавление лота',
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'user_avatar' => $user_avatar,
        'nav_panel' => $nav_panel,
        'main_content' => $main_content
    ]
);