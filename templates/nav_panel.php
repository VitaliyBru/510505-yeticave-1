<nav class="nav">
    <ul class="nav__list container">
        <?php if (!empty($categories)): ?>
            <?php foreach ($categories as $category): ?>
                <li class="nav__item">
                    <a href="<?= "all-lots.php?id={$category['id']}"; ?>"><?= $category['name']; ?></a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</nav>