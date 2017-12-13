<?php
require_once 'functions.php';
require_once 'mysql_helper.php';
require_once 'authorization.php';
require_once 'init.php';

/** @var array $login Массив с данными из формы входа */
$login = ['email' => '', 'password' => ''];
/** @var array $errors Массив с флагами ошибок */
$errors = [
    'form' => false,
    'email' => ['isEmpty' => false, 'isWrong' => false],
    'password' => ['isEmpty' => false, 'isWrong' => false]
];
/** @var array $e_rules массив с правилами проверок заполниния формы */
$e_rules = ['email' => 'isEmpty', 'password' => 'isEmpty'];

try {
    /** @var array $categories массив с наименованиями категорий товаров */
    $categories = getCategories($link);
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        $login = $_POST['login'];

        //проверяем заполненность полей
        foreach ($login as $key => $value) {
            $errors[$key][$e_rules[$key]] = call_user_func($e_rules[$key], $value);
            $errors['form'] = ($errors['form'] or $errors[$key][$e_rules[$key]]);
        }

        //если все поля заполненны получаем пользователя по его email если пароль совпадает
        if (!$errors['form']) {
            $user = getAuthenticUser($link, $login, $errors);

            //если пользователь аутентифицирован устанавливаем сессию
            if (!empty($user)) {
                setSessionAndStart($user);
                header('Location: /index.php');
            }
        }
    }
} catch (Exception $e) {
    mysqli_close($link);
    showErrors($e);
    exit();
}
/** @var string $nav_panel верстка панели навигации по категориям */
$nav_panel = templateEngine('nav_panel', ['categories' => $categories]);
$main_content = templateEngine(
    'login',
    [
        'login' => $login,
        'errors' => $errors,
        'nav_panel' => $nav_panel
    ]
);
echo templateEngine(
    'layout',
    [
        'title' => 'Вход',
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'user_avatar' => $user_avatar,
        'nav_panel' => $nav_panel,
        'main_content' => $main_content
    ]
);