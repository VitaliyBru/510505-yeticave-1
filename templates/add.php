<?=$nav_panel; ?>
<form class="form form--add-lot container <?= $errors['form'] ? 'form--invalid' : ''; ?>" action="add.php" method="post" enctype="multipart/form-data"> <!-- form--invalid -->
    <h2>Добавление лота</h2>
    <div class="form__container-two">
        <div class="form__item <?= $errors['name'] ? 'form__item--invalid' : ''; ?>"> <!-- form__item--invalid -->
            <label for="lot-name">Наименование</label>
            <input id="lot-name" type="text" name="lot[name]" value="<?=secure($lot['name']); ?>" placeholder="Введите наименование лота" required>
            <span class="form__error">Введите наименование лота</span>
        </div>
        <div class="form__item <?= $errors['category_id'] ? 'form__item--invalid' : ''; ?>">
            <label for="category">Категория</label>
            <select id="category" name="lot[category_id]" required>
                <option>Выберите категорию</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?=$category['id']; ?>"<?= ($lot['category_id'] == $category['id']) ? ' selected' : ''; ?>><?=$category['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <span class="form__error">Выберите категорию</span>
        </div>
    </div>
    <div class="form__item form__item--wide <?= $errors['description'] ? 'form__item--invalid' : ''; ?>">
        <label for="message">Описание</label>
        <textarea id="message" name="lot[description]" placeholder="Напишите описание лота" required><?=secure($lot['description']); ?></textarea>
        <span class="form__error">Напишите описание лота</span>
    </div>
    <div class="form__item form__item--file <?= $errors['img_url'] ? '' : 'form__item--uploaded'; ?>"> <!-- form__item--uploaded -->
        <label>Изображение</label>
        <div class="preview">
            <button class="preview__remove" type="button">x</button>
            <div class="preview__img">
                <img src="<?=$lot['img_url']; ?>" width="113" height="113" alt="Изображение лота">
                <input type="hidden" name="lot[img_url]" value="<?=$lot['img_url']; ?>">
            </div>
        </div>
        <div class="form__input-file">
            <input class="visually-hidden" type="file" id="photo2" name="image">
            <label for="photo2">
                <span>+ Добавить</span>
            </label>
        </div>
    </div>
    <div class="form__container-three">
        <div class="form__item form__item--small <?= $errors['price_origin'] ? 'form__item--invalid' : ''; ?>">
            <label for="lot-rate">Начальная цена</label>
            <input id="lot-rate" type="text" name="lot[price_origin]" value="<?=secure($lot['price_origin']); ?>" placeholder="0" required>
            <span class="form__error">Введите начальную цену</span>
        </div>
        <div class="form__item form__item--small <?= $errors['price_step'] ? 'form__item--invalid' : ''; ?>">
            <label for="lot-step">Шаг ставки</label>
            <input id="lot-step" type="text" name="lot[price_step]" value="<?=secure($lot['price_step']); ?>" placeholder="0" required>
            <span class="form__error">Введите шаг ставки</span>
        </div>
        <div class="form__item <?= $errors['date_end'] ? 'form__item--invalid' : ''; ?>">
            <label for="lot-date">Дата окончания торгов</label>
            <input class="form__input-date" id="lot-date" type="text" name="lot[date_end]" value="<?=secure($lot['date_end']); ?>" placeholder="дд.мм.гггг" required>
            <span class="form__error">Введите дату завершения торгов</span>
        </div>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Добавить лот</button>
</form>