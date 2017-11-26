<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $category): ?>
            <li class="nav__item">
                <a href="<?="index.php?id={$category['id']}"; ?>"><?=$category['name']; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<section class="lot-item container">
    <h2><?=secure($lot['name']); ?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="<?=$lot['img_url']; ?>" width="730" height="548" alt="Сноуборд">
            </div>
            <p class="lot-item__category">Категория: <span><?=$lot['category']; ?></span></p>
            <p class="lot-item__description"><?=secure($lot['description']); ?></p>
        </div>
        <div class="lot-item__right">
            <div class="lot-item__state" <?=$bet_done ? 'style="visibility:hidden;"' : ''; ?>>
                <div class="lot-item__timer timer">
                    <?=lotTimeRemaining(); ?>
                </div>
                <div class="lot-item__cost-state">
                    <div class="lot-item__rate">
                        <span class="lot-item__amount">Текущая цена</span>
                        <span class="lot-item__cost"><?=$bet_amounts['current']; ?></span>
                    </div>
                    <div class="lot-item__min-cost">
                        Мин. ставка <span><?=$bet_amounts['not_less']; ?> р</span>
                    </div>
                </div>
                <form class="lot-item__form" action="lot.php?id=<?=$lot['id']; ?>" method="post">
                    <p class="lot-item__form-item">
                        <label for="cost">Ваша ставка</label>
                        <input id="cost" type="number" name="cost" placeholder="<?=$bet_amounts['not_less']; ?>">
                    </p>
                    <button type="submit" class="button">Сделать ставку</button>
                </form>
            </div>
            <div class="history">
                <h3>История ставок (<span><?=count($bets); ?></span>)</h3>
                <!-- заполните эту таблицу данными из массива $bets-->
                <table class="history__list">
                    <?php foreach ($bets as $bet): ?>
                        <tr class="history__item">
                            <td class="history__name"><?=secure($bet['name']); ?><!-- имя автора--></td>
                            <td class="history__price"><?=$bet['price']; ?><!-- цена--> р</td>
                            <td class="history__time"><?=betTime($bet['ts']); ?><!-- дата в человеческом формате--></td>
                        </tr>
                    <?php endforeach;; ?>
                </table>
            </div>
        </div>
    </div>
</section>