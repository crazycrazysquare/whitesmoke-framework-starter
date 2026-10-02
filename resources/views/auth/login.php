<?php $old = session()->getFlash('old', []); ?>
<h1>Login</h1>
<form class="login" method="post" action="/login">
    <?= csrf_field() ?>
    <label for="email">Email</label>
    <input id="email" name="email" type="email" value="<?= e($old['email'] ?? '') ?>" required autocomplete="username">
    <label for="password">Password</label>
    <input id="password" name="password" type="password" required autocomplete="current-password">
    <label><input type="checkbox" name="remember" value="1"> Remember me on this device</label>
    <button>Login</button>
</form>
<p><a href="/forgot-password">Forgot your password?</a></p>
