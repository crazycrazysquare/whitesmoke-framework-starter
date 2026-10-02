<?php $userName = session()->get('user_name'); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Whitesmoke') ?></title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 640px; margin: 2rem auto; padding: 0 1rem; color: #1e293b; }
        nav { display: flex; gap: 1rem; align-items: center; margin-bottom: 1.5rem; }
        nav strong { margin-right: auto; }
        .flash { padding: .75rem; border-radius: .375rem; margin-bottom: 1rem; }
        .ok { background: #dcfce7; } .err { background: #fee2e2; }
        label { display: block; margin: .75rem 0 .25rem; }
        input { padding: .5rem; width: 100%; box-sizing: border-box; }
        input[type=checkbox] { width: auto; margin-right: .4rem; }
        button { padding: .5rem 1rem; }
        form.login button { margin-top: 1rem; }
        dl { display: grid; grid-template-columns: max-content 1fr; gap: .5rem 1rem; }
        dt { color: #64748b; }
    </style>
</head>
<body>
    <nav>
        <strong>Whitesmoke</strong>
        <?php if ($userName !== null): ?>
            <span><?= e($userName) ?></span>
            <a href="/account/password">Change password</a>
            <form method="post" action="/logout">
                <?= csrf_field() ?>
                <button>Logout</button>
            </form>
        <?php endif ?>
    </nav>

    <?php if ($msg = session()->getFlash('success')): ?>
        <div class="flash ok"><?= e($msg) ?></div>
    <?php endif ?>
    <?php if ($msg = session()->getFlash('error')): ?>
        <div class="flash err"><?= e($msg) ?></div>
    <?php endif ?>

    <main><?= $content ?></main>
</body>
</html>
