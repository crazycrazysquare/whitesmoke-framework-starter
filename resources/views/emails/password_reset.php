<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Reset your password</title>
</head>
<body>
<div style="padding:24px; background:#f1f5f9; font-family:system-ui, -apple-system, 'Segoe UI', sans-serif; color:#1e293b;">
    <div style="max-width:480px; margin:0 auto; background:#ffffff; border-radius:8px; padding:24px;">
        <p>Hello <?= e($name) ?>,</p>
        <p>Someone asked to reset the password for your account at <?= e($host) ?>. To choose a new password, open this link within <?= e($minutes) ?> minutes:</p>
        <p style="margin:24px 0;">
            <a href="<?= e($link) ?>" style="background:#1e293b; color:#ffffff; padding:12px 20px; border-radius:6px; text-decoration:none; display:inline-block;">Choose a new password</a>
        </p>
        <p style="font-size:13px; color:#64748b;">If the button does not work, copy this address into your browser:<br><?= e($link) ?></p>
        <p style="font-size:13px; color:#64748b;">If you did not ask for this, ignore this email. Your password stays the same.</p>
    </div>
</div>
</body>
</html>
