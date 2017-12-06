<?php
require_once 'functions.php';
require_once 'mysql_helper.php';
require_once 'init.php';

/** @var bool $is_auth true если пользователь аутентифицирован */
$is_auth = false;
/** @var array $user массив с данными о пользователе */
$user = [
    'name' => '',
    'email' => '',
    'password' => '',
    'contact' => '',
    'avatar' => null
];
/** @var array $e_rules массив с именами функций для поиска ошибок в отправленной форме */
$e_rules = [
    'name' => 'isEmpty',
    'email' => 'isNotEmail',
    'password' => 'isEmpty',
    'contact' => 'isEmpty'
];
/** @var array $errors массив с флагами ошибок */
$errors = [
    'form' => false,
    'name' => false,
    'email' => false,
    'password' => false,
    'contact' => false,
    'email_claimed' => false
];

try {
    /** @var array $categories массив со списком категорий */
    $categories = getCategories($link);

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user'])) {
        $user = $_POST['user'];

        // сохранить файл изображения и получить на него ссылку
        if (isset($_FILES['avatar']) && $_FILES['avatar']['size']) {
            $user['avatar'] = getImageFromForm('avatar');
        }

        // проверка соответствия полученной формы правилам
        foreach ($e_rules as $key => $func_name) {
            $errors[$key] = call_user_func($func_name, $user[$key]);
            $errors['form'] = ($errors['form'] or $errors[$key]);
        }

        // если форма заполнена проверяем email на уникальность
        if (!$errors['form']) {
            $email_claimed = getUser($link, $user['email']);
            $errors['email_claimed'] = !empty($email_claimed);
            $errors['email'] = $errors['email_claimed'];
            $errors['form'] = $errors['email'];
        }
        // если все требования выполнены – хешируем пароль и сохраняем данные в бд
        if (!$errors['form']) {
            if ($hash = password_hash($user['password'], PASSWORD_DEFAULT)) {
                $user['password'] = $hash;
            }
            $user_id = setInTable($link, $user, 'users');
            if ($user_id) {
                header("Location: /login.php");
            }
            // если все же что-то где-то пошло нетак
            $user['password'] = '';
        }
    }
} catch (Exception $e) {
    mysqli_close($link);
    showErrors($e);
}
mysqli_close($link);

$nav_panel = templateEngine('nav_panel', ['categories' => $categories]);
$main_content = templateEngine(
    'sing-up',
    [
        'nav_panel' => $nav_panel,
        'user' => $user,
        'errors' => $errors
    ]
);
echo templateEngine(
    'layout',
    [
        'title' => 'Регистрация',
        'main_content' => $main_content,
        'is_auth' => $is_auth,
        'nav_panel' => $nav_panel
    ]
);