<h1>Choose a new password</h1>
<?php if (!$valid): ?>
    <p>This reset link is invalid, used or expired.</p>
    <p><a href="/forgot-password">Request a new link</a></p>
<?php else: ?>
    <form class="login" method="post" action="/reset-password">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= e($token) ?>">
        <label for="password">New password (at least 10 characters)</label>
        <input id="password" name="password" type="password" required minlength="10" autocomplete="new-password">
        <label for="password_confirmation">Type it again</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required minlength="10" autocomplete="new-password">
        <button>Change password</button>
    </form>
<?php endif; ?>
