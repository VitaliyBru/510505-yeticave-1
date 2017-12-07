<ul class="pagination-list">
    <?php
    if ($pagination['previous']) {
        echo "<li class=\"pagination-item pagination-item-prev\"><a href=\"{$pagination['goto']}{$pagination['previous']}\">Назад</a></li>";
    } else {
        echo "<li class=\"pagination-item pagination-item-prev\"><a>Назад</a></li>";
    }
    foreach ($pagination['pages'] as $page) {
        if ($pagination['currant'] == $page) {
            echo "<li class=\"pagination-item pagination-item-active\"><a>$page</a></li>";
        } else {
            echo "<li class=\"pagination-item\"><a href=\"{$pagination['goto']}$page\">$page</a></li>";
        }
    }
    if ($pagination['next']) {
        echo "<li class=\"pagination-item pagination-item-next\"><a href=\"{$pagination['goto']}{$pagination['next']}\">Вперед</a></li>";
    } else {
        echo "<li class=\"pagination-item pagination-item-next\"><a>Вперед</a></li>";
    }
    ?>
</ul>