<?=$nav_panel; ?>
<form class="form container<?=$errors['form'] ? ' form--invalid' : ''; ?>" action="sing-up.php" method="post" enctype="multipart/form-data"> <!-- form--invalid -->
    <h2>Регистрация нового аккаунта</h2>
    <div class="form__item<?=$errors['email'] ? ' form__item--invalid' : '';?>"> <!-- form__item--invalid -->
        <label for="email">E-mail*</label>
        <input id="email" type="text" name="user[email]" placeholder="Введите e-mail" value="<?=secure($user['email']); ?>" >
        <span class="form__error"><?=$errors['email_claimed'] ? 'E-mail уже занят' : 'Введите e-mail'; ?></span>
    </div>
    <div class="form__item<?=$errors['password'] ? ' form__item--invalid' : '';?>">
        <label for="password">Пароль*</label>
        <input id="password" type="text" name="user[password]" placeholder="Введите пароль" value="<?=secure($user['password']); ?>" >
        <span class="form__error">Введите пароль</span>
    </div>
    <div class="form__item<?=$errors['name'] ? ' form__item--invalid' : '';?>">
        <label for="name">Имя*</label>
        <input id="name" type="text" name="user[name]" placeholder="Введите имя" value="<?=secure($user['name']); ?>" >
        <span class="form__error">Введите имя</span>
    </div>
    <div class="form__item<?=$errors['contact'] ? ' form__item--invalid' : '';?>">
        <label for="message">Контактные данные*</label>
        <textarea id="message" name="user[contact]" placeholder="Напишите как с вами связаться" ><?=secure($user['contact']); ?></textarea>
        <span class="form__error">Напишите как с вами связаться</span>
    </div>
    <div class="form__item form__item--file form__item--last<?= $user['avatar'] ? ' form__item--uploaded' : ''; ?>">
        <label>Аватар</label>
        <div class="preview">
            <button class="preview__remove" type="button">x</button>
            <div class="preview__img">
                <img src="<?=$user['avatar']; ?>" width="113" height="113" alt="Ваш аватар">
                <input type="hidden" name="user[avatar]" value="<?=$user['avatar']; ?>">
            </div>
        </div>
        <div class="form__input-file">
            <input class="visually-hidden" type="file" id="photo2" name="avatar">
            <label for="photo2">
                <span>+ Добавить</span>
            </label>
        </div>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Зарегистрироваться</button>
    <a class="text-link" href="login.php">Уже есть аккаунт</a>
</form>