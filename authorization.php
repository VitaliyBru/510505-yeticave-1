<?php
$is_auth = false;
$user_name = null;
$user_avatar = null;
$user_id = null;
session_start();
if (isset($_SESSION['user'])) {
    $secure_key = md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
    if ($_SESSION['user']['secure_key'] === $secure_key) {
        $is_auth = true;
        $user_name = $_SESSION['user']['name'];
        $user_avatar = $_SESSION['user']['avatar'];
        $user_id = $_SESSION['user']['id'];
    }
}