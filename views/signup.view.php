<style>
    .form-signup {
        display: flex;
        flex-direction: column;
        width: 300px;
        gap: 10px;
    }
</style>
<h1>Страница регистрации</h1>
<form method="post" class="form-signup">
    <input type="text" name="username" value="<?=oldValue('username')?>" placeholder="Логин">
    <div><?= $user->getError('username') ?></div>
    <input type="email" name="email" value="<?=oldValue('email')?>" placeholder="Email">
    <div><?= $user->getError('email') ?></div>
    <input type="password" name="password" value="<?=oldValue('password')?>" placeholder="Пароль">
    <div><?= $user->getError('password') ?></div>
    <button>Регистрация</button>
</form>