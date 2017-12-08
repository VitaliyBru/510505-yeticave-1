<?php
require_once 'functions.php';
require_once 'authorization.php';
require_once 'init.php';
require_once 'determine_a_winner.php';

// устанавливаем часовой пояс в Московское время
date_default_timezone_set('Europe/Moscow');

/** @var int $limit отображаемое колличество лотов на странице */
$limit = 3;
/** @var array $pagination содержит информацию для работы пэйдженатора */
$pagination['currant'] = intval($_GET['p'] ?? 1);
/** @var int $offset смещение в выдаче результатов поиска */
$offset = ($pagination['currant'] - 1) * $limit;

try {
    // получаем из бд список категорий
    /** @var array $categories список категорий*/
    $categories = getCategories($link);

    // получаем из бд список активных лотов
    /** @var array $lots список лотов*/
    $lots = getActiveLots($link, $offset, $limit);
    $total_lots = getTotalNumberFoundRows($link);
} catch (Exception $e) {
    mysqli_close($link);
    showErrors($e);
    exit();
}
mysqli_close($link);

// если контента больше чем для вывода на одну страницу, реализуем постраничный вывод
if ($total_lots > $limit) {
    if (empty($lots)) {
        header("Location: /index.php");
    }
    $pagination['goto'] = "index.php?p=";
    $total_pages = intval(ceil($total_lots / $limit));
    $pagination['pages'] = range(1, $total_pages);
    $pagination['next'] = ($pagination['currant'] == $total_pages) ? false : ($pagination['currant'] + 1);
    $pagination['previous'] = ($pagination['currant'] == 1) ? false : ($pagination['currant'] - 1);

    /** @var string $pagination_content содержит блок верстки для постраничной навигации */
    $pagination_content = templateEngine('pagination', ['pagination' => $pagination]);
} else {
    $pagination_content = '';
}

/** @var string $main_content содержит результат работы шаблонизатора */
$main_content = templateEngine(
    'index',
    [
        'categories' => $categories,
        'lots' => $lots,
        'pagination_content' => $pagination_content
    ]
);
/** @var string $nav_panel навигационное меню */
$nav_panel = templateEngine('nav_panel', ['categories' => $categories]);
echo templateEngine(
    'layout',
    [
        'title' => 'Главная',
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'user_avatar' => $user_avatar,
        'nav_panel' => $nav_panel,
        'main_content' => $main_content
    ]
);