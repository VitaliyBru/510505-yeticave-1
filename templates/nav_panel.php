<nav class="nav">
    <ul class="nav__list container">
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $category): ?>
                <li class="nav__item">
                    <a href="<?= secure("all-lots.php?id={$category['id']}"); ?>"><?= secure($category['name']); ?></a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</nav>