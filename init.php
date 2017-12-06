<?php
try {
    $link = mysqli_connect('Localhost', 'root', '', 'yeticave');
    if (!$link) {
        throw new Exception(mysqli_connect_error(), mysqli_connect_errno());
    }
} catch (Exception $e) {
    showErrors($e);
    exit();
}
