<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $category): ?>
            <li class="nav__item">
                <a href="<?="index.php?id={$category['id']}"; ?>"><?=$category['name']; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<section class="rates container">
    <h2>Мои ставки</h2>
    <table class="rates__list">
        <?php foreach ($lots_with_my_bets as $lot_bet): ?>
            <?php if ($lot_bet['date_end'] - strtotime('now') > 0): ?>
                <tr class="rates__item">
            <?php elseif (false/* if the user is a winner then true */): ?>
                <tr class="rates__item rates__item--win">
            <?php else: ?>
                <tr class="rates__item rates__item--end">
            <?php endif; ?>
                    <td class="rates__info">
                        <div class="rates__img">
                            <img src="<?=$lot_bet['img_url']; ?>" width="54" height="40" alt="Сноуборд">
                        </div>
                        <?php if (false/* if you are a winner then true*/): ?>
                            <div>
                                <h3 class="rates__title"><a href="lot.php?id=<?=$lot_bet['lot_id']; ?>"><?=secure($lot_bet['name']); ?></a></h3>
                                <p><?='Контактная информация продовца например: Телефон +7 900 667-84-48, Скайп: Vlas92. Звонить с 14 до 20'; ?></p>
                            </div>
                        <?php else: ?>
                            <h3 class="rates__title"><a href="lot.php?id=<?=$lot_bet['lot_id']; ?>"><?=secure($lot_bet['name']); ?></a></h3>
                        <?php endif; ?>
                    </td>
                    <td class="rates__category">
                        <?=$lot_bet['category']; ?>
                    </td>
                    <td class="rates__timer">
                        <?php if ($lot_bet['date_end'] - strtotime('now') > 86400): ?>
                            <div class="timer"><?=lotTimeRemaining(); ?></div>
                        <?php elseif($lot_bet['date_end'] - strtotime('now') > 0): ?>
                            <div class="timer timer--finishing"><?=lotTimeRemaining(); ?></div>
                        <?php elseif(false/* if the user is a winner then true */): ?>
                            <div class="timer timer--win">Ставка выиграла</div>
                        <?php else: ?>
                            <div class="timer timer--end">Торги окончены</div>
                        <?php endif; ?>
                    </td>
                    <td class="rates__price">
                        <?=$lot_bet['price']; ?> р
                    </td>
                    <td class="rates__time">
                        <?=betTime($lot_bet['ts']); ?>
                    </td>
                </tr>
        <?php endforeach; ?>
    </table>
</section>