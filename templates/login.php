<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $category): ?>
            <li class="nav__item">
                <a href="<?="index.php?id={$category['id']}"; ?>"><?=$category['name']; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<form class="form container<?=$errors['form'] ? ' form--invalid' : ''; ?>" action="login.php" method="post"> <!-- form--invalid -->
    <h2>Вход</h2>
    <?php if ($errors['email']['isWrong'] || $errors['password']['isWrong']): ?>
    <div class="form__item form__item--invalid">
        <span class="form__error" style="font-size:16px;">Вы ввели неверный<?=$errors['email']['isWrong'] ? ' email' : ' пароль'; ?></span>
    </div>
    <?php endif; ?>
    <div class="form__item<?=($errors['email']['isEmpty'] or $errors['email']['isWrong']) ? ' form__item--invalid' : ''; ?>"> <!-- form__item--invalid -->
        <label for="email">E-mail*</label>
        <input id="email" type="text" name="login[email]" value="<?=strip_tags($login['email']); ?>" placeholder="Введите e-mail" required>
        <?=$errors['email']['isWrong'] ? '' : '<span class="form__error">Введите e-mail</span>'; ?>
    </div>
    <div class="form__item form__item--last<?=($errors['password']['isEmpty'] or $errors['password']['isWrong']) ? ' form__item--invalid' : ''; ?>">
        <label for="password">Пароль*</label>
        <input id="password" type="text" name="login[password]" placeholder="Введите пароль" required>
        <?=$errors['password']['isWrong'] ? '' : '<span class="form__error">Введите пароль</span>'; ?>
    </div>
    <button type="submit" class="button">Войти</button>
</form>