<h1>Change password</h1>
<form class="login" method="post" action="/account/password">
    <?= csrf_field() ?>
    <label for="current_password">Current password</label>
    <input id="current_password" name="current_password" type="password" required autocomplete="current-password">
    <label for="password">New password (at least 10 characters)</label>
    <input id="password" name="password" type="password" required minlength="10" autocomplete="new-password">
    <label for="password_confirmation">Type it again</label>
    <input id="password_confirmation" name="password_confirmation" type="password" required minlength="10" autocomplete="new-password">
    <button>Change password</button>
</form>
<p>Changing your password logs you out on every other device.</p>
