# whitesmoke/framework

Application skeleton for the Whitesmoke Framework. The engine lives in
`whitesmoke/core`, pulled in by Composer.

## Install

    composer install
    php smoke setup                                                # migrations + admin (random password)
    php smoke setup --email=you@example.com --password='YourPass123'

## Run (development)

    php smoke serve

Run `php smoke list` to see every command.

## Database

SQLite by default (`storage/database.sqlite`). Switch with environment
variables: `DB_CONNECTION` (mysql, pgsql, sqlite, sqlsrv), `DB_HOST`,
`DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

## Core during development

`composer.json` points to `../core` as a path repository. When the core
is on GitHub, replace it with a `vcs` repository or Packagist.
