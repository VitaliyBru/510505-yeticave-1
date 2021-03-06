<?php
require_once 'functions.php';
require_once 'mysql_helper.php';
require_once 'authorization.php';
require_once 'init.php';

/** @var int $limit отображаемое колличество лотов на странице */
$limit = 3;
/** @var array $pagination содержит информацию для работы пэйдженатора */
$pagination['currant'] = intval($_GET['p'] ?? 1);
/** @var int $offset смещение в выдаче результатов поиска */
$offset = ($pagination['currant'] - 1) * $limit;
/** @var string $search поисковый запрос пользователя */
$search = trim($_GET['search'] ?? '');
/** @var string $search_request обработанный для поиска запрос пользователя */
$search_request = "%$search%";

try {
    // получаем из бд список категорий
    /** @var array $categories список категорий*/
    $categories = getCategories($link);
    if ($search) {
        // получаем из бд список активных лотов
        /** @var array $lots список лотов */
        $lots = getFoundLots($link, $offset, $limit, $search_request);
        $total_lots = getTotalNumberFoundRows($link);
    } else {
        $lots = array();
        $total_lots = 0;
    }
} catch (Exception $e) {
    mysqli_close($link);
    showErrors($e);
    exit();
}
mysqli_close($link);

// если контента больше чем для вывода на одну страницу, реализуем постраничный вывод
if ($total_lots > $limit) {
    if (empty($lots)) {
        header("Location: /search.php?search={$search}&p=1");
    }
    $pagination['goto'] = "search.php?search={$search}&p=";
    $total_pages = intval(ceil($total_lots / $limit));
    $pagination['pages'] = range(1, $total_pages);
    $pagination['next'] = ($pagination['currant'] === $total_pages) ? false : ($pagination['currant'] + 1);
    $pagination['previous'] = ($pagination['currant'] === 1) ? false : ($pagination['currant'] - 1);

    /** @var string $pagination_content содержит блок верстки для постраничной навигации */
    $pagination_content = templateEngine('pagination', ['pagination' => $pagination]);
} else {
    $pagination_content = '';
}

/** @var string $nav_panel навигационное меню */
$nav_panel = templateEngine('nav_panel', ['categories' => $categories]);
/** @var string $main_content содержит результат работы шаблонизатора */
$main_content = templateEngine(
    'search',
    [
        'search' => $search,
        'lots' => $lots,
        'nav_panel' => $nav_panel,
        'pagination_content' => $pagination_content
    ]
);
echo templateEngine(
    'layout',
    [
        'title' => 'Результаты поиска',
        'is_auth' => $is_auth,
        'user_name' => $user_name,
        'user_avatar' => $user_avatar,
        'nav_panel' => $nav_panel,
        'main_content' => $main_content
    ]
);