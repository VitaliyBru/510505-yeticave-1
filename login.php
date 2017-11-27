<?php
require_once 'functions.php';
require_once 'artificial_bd.php';
require_once 'userdata.php';

$is_auth = false;
$user_name = null;
$user_avatar = null;

$login = ['email' => '', 'password' => ''];
$errors = [
    'form' => false,
    'email' => ['isEmpty' => false, 'isWrong' => false],
    'password' => ['isEmpty' => false, 'isWrong' => false]
];
$e_rules = ['email' => 'isEmpty', 'password' => 'isEmpty'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $login = $_POST['login'];

    //проверяем заполненность полей
    foreach ($login as $key => $value) {
        $errors[$key][$e_rules[$key]] = call_user_func($e_rules[$key], $value);
        $errors['form'] = ($errors['form'] or $errors[$key][$e_rules[$key]]);
    }

    //если все поля заполненны
    if (!$errors['form']) {
        $user = null;

        //проверяем корректность написания email адреса и его соответствие с пользователем
        if (isNotEmail($login['email'])) {
            $errors['email']['isWrong'] = true;
        } else {
            $user = getUser($login['email'], $users);
            $errors['email']['isWrong'] = empty($user);
        }

        //если пользователь найден проверяем пароль
        if (!$errors['email']['isWrong']) {
            $errors['password']['isWrong'] = !password_verify($login['password'], $user['password']);
        }

        //если ошибок нет открываем сессию
        if (!$errors['email']['isWrong'] && !$errors['password']['isWrong']) {
            $secure_key = md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
            session_start();
            $_SESSION['user'] = [
                'secure_key' => $secure_key,
                'id' => null,
                'name' => $user['name'],
                'avatar' => null
                ];
            header('Location: /index.php');
        }
    }
}

$main_content = templateEngine(
    'login',
    [
        'login' => $login,
        'errors' => $errors,
        'categories' => $categories
    ]
);
echo templateEngine(
    'layout',
    [
        'title' => 'Вход',
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'user_avatar' => $user_avatar,
        'categories' => $categories,
        'main_content' => $main_content
    ]
);