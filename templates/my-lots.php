<main>
    <?= $nav_panel; ?>
    <section class="rates container">
        <h2>Мои ставки</h2>
        <table class="rates__list">
            <?php foreach ($lots_with_my_bets as $lot_bet): ?>
                <?php if ($lot_bet['date_end'] - strtotime('now') > 0): ?>
                    <tr class="rates__item">
                <?php elseif ($lot_bet['winner']): ?>
                    <tr class="rates__item rates__item--win">
                <?php else: ?>
                    <tr class="rates__item rates__item--end">
                <?php endif; ?>
                <td class="rates__info">
                    <div class="rates__img">
                        <img src="<?= secure($lot_bet['img_url']); ?>" width="54" height="40" alt="Сноуборд">
                    </div>
                    <?php if ($lot_bet['winner']): ?>
                        <div>
                            <h3 class="rates__title"><a href="lot.php?id=<?= (int)$lot_bet['lot_id']; ?>"><?= secure($lot_bet['name']); ?></a>
                            </h3>
                            <p><?=secure($lot_bet['contact']); ?></p>
                        </div>
                    <?php else: ?>
                        <h3 class="rates__title"><a href="lot.php?id=<?= (int)$lot_bet['lot_id']; ?>"><?= secure($lot_bet['name']); ?></a>
                        </h3>
                    <?php endif; ?>
                </td>
                <td class="rates__category">
                    <?= secure($lot_bet['category']); ?>
                </td>
                <td class="rates__timer">
                    <?php if ($lot_bet['date_end'] - strtotime('now') > 86400): ?>
                        <div class="timer"><?= lotTimeRemaining($lot_bet['date_end']); ?></div>
                    <?php elseif ($lot_bet['date_end'] - strtotime('now') > 0): ?>
                        <div class="timer timer--finishing"><?= lotTimeRemaining($lot_bet['date_end']); ?></div>
                    <?php elseif ($lot_bet['winner']): ?>
                        <div class="timer timer--win">Ставка выиграла</div>
                    <?php else: ?>
                        <div class="timer timer--end">Торги окончены</div>
                    <?php endif; ?>
                </td>
                <td class="rates__price">
                    <?= (int)$lot_bet['price']; ?> р
                </td>
                <td class="rates__time">
                    <?= betTime($lot_bet['ts']); ?>
                </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </section>
</main>