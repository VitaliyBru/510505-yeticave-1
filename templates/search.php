<main>
    <?= $nav_panel; ?>
    <div class="container">
        <section class="lots">
            <h2>Результаты поиска по запросу «<span><?=secure($search); ?></span>»</h2>
            <ul class="lots__list">
                <?php foreach ($lots as $lot): ?>
                    <li class="lots__item lot">
                        <div class="lot__image">
                            <img src="<?=secure($lot['img_url']); ?>" width="350" height="260" alt="Сноуборд">
                        </div>
                        <div class="lot__info">
                            <span class="lot__category"><?=secure($lot['category']);?></span>
                            <h3 class="lot__title"><a class="text-link" href="lot.php?id=<?=(int)$lot['id']; ?>"><?=secure($lot['name']);?></a></h3>
                            <div class="lot__state">
                                <div class="lot__rate">
                                    <span class="lot__amount">Стартовая цена</span>
                                    <span class="lot__cost"><?=(int)$lot['price_origin'];?><b class="rub">р</b></span>
                                </div>
                                <?php $finishing = ($lot['date_end'] - strtotime('now') < 3600) ? ' timer--finishing' : '';?>
                                <div class="lot__timer timer<?=$finishing;?>">
                                    <?=lotTimeRemaining($lot['date_end']);?>
                                </div>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <?= $pagination_content; ?>
    </div>
</main>