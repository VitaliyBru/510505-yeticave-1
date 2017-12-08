<?php
require_once 'functions.php';
require_once 'authorization.php';
require_once 'init.php';

// устанавливаем часовой пояс в Московское время
date_default_timezone_set('Europe/Moscow');

/** @var int $limit отображаемое колличество лотов на странице */
$limit = 3;
/** @var array $pagination содержит информацию для работы пэйдженатора */
$pagination['currant'] = intval($_GET['p'] ?? 1);
/** @var int $offset смещение в выдаче результатов поиска */
$offset = ($pagination['currant'] - 1) * $limit;
$id = intval($_GET['id'] ?? 0);

try {
    // получаем из бд список категорий
    /** @var array $categories список категорий*/
    $categories = getCategories($link);
    if (!in_array($id, array_column($categories, 'id'))) {
        http_response_code(404);
        exit();
    }

    // получаем из бд список активных лотов
    /** @var array $lots список лотов*/
    $lots = getActiveLotsFromCategory($link, $offset, $limit, $id);
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
        header("Location: /all-lots.php?id={$id}&p=1");
    }
    $pagination['goto'] = "all-lots.php?id={$id}&p=";
    $total_pages = intval(ceil($total_lots / $limit));
    $pagination['pages'] = range(1, $total_pages);
    $pagination['next'] = ($pagination['currant'] == $total_pages) ? false : ($pagination['currant'] + 1);
    $pagination['previous'] = ($pagination['currant'] == 1) ? false : ($pagination['currant'] - 1);

    /** @var string $pagination_content содержит блок верстки для постраничной навигации */
    $pagination_content = templateEngine('pagination', ['pagination' => $pagination]);
} else {
    $pagination_content = '';
}

$category = getCategoryName($id, $categories);

/** @var string $main_content содержит результат работы шаблонизатора */
$main_content = templateEngine(
    'all-lots',
    [
        'category' => $category,
        'lots' => $lots,
        'pagination_content' => $pagination_content
    ]
);
/** @var string $nav_panel навигационное меню */
$nav_panel = templateEngine('nav_panel', ['categories' => $categories]);
echo templateEngine(
    'layout',
    [
        'title' => 'Все лоты',
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'user_avatar' => $user_avatar,
        'nav_panel' => $nav_panel,
        'main_content' => $main_content
    ]
);