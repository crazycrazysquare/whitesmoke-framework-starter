# whitesmoke/framework

Application skeleton for the Whitesmoke Framework. The engine lives in
`whitesmoke/core`, pulled in by Composer.

## Install

    composer install
    php database/setup.php                     # tables + admin (random password)
    php database/setup.php you@example.com 'YourPass123'

## Run (development)

    SESSION_SECURE=false php -S 127.0.0.1:8000 -t public public/index.php

`SESSION_SECURE=false` is only for plain-HTTP local development.

## Database

SQLite by default (`storage/database.sqlite`). Switch with environment
variables: `DB_CONNECTION` (mysql, pgsql, sqlite, sqlsrv), `DB_HOST`,
`DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

## Core during development

`composer.json` points to `../core` as a path repository. When the core
is on GitHub, replace it with a `vcs` repository or Packagist.
