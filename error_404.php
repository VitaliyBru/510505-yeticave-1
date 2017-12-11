<?php
require_once 'functions.php';
require_once 'authorization.php';

echo templateEngine(
    'error_404',
    [
        'title' => 'Ошибка 404',
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'user_avatar' => $user_avatar
    ]
);