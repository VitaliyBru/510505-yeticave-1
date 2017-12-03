<?php
$link = mysqli_connect('Localhost', 'root', '', 'yeticave');
if (!$link) {
    $error[] = 'Код ошибки errno: ' . mysqli_connect_errno();
    $error[] = 'Текст ошибки error: ' . mysqli_connect_error();
    showErrors($error);
    exit();
}