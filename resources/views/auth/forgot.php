<?php $old = session()->getFlash('old', []); ?>
<h1>Forgot password</h1>
<p>Enter your account's email address. We'll send you a link to choose a new password.</p>
<form class="login" method="post" action="/forgot-password">
    <?= csrf_field() ?>
    <label for="email">Email</label>
    <input id="email" name="email" type="email" value="<?= e($old['email'] ?? '') ?>" required autocomplete="username">
    <button>Send reset link</button>
</form>
<p><a href="/login">Back to login</a></p>
