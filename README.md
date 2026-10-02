# whitesmoke/framework

Application skeleton for the Whitesmoke Framework. The engine lives in
`whitesmoke/core`, pulled in by Composer.

## Install

    composer create-project whitesmoke/framework myapp
    cd myapp                    # .env is created for you
    php smoke setup                                                # migrations + admin (random password)
    php smoke setup --email=you@example.com --password='YourPass123'

## Run (development)

    php smoke serve

Run `php smoke list` to see every command.

## Database

SQLite by default (`storage/database.sqlite`). Switch in `.env` (or real
environment variables, which always win over `.env`): `DB_CONNECTION` (mysql, pgsql, sqlite, sqlsrv), `DB_HOST`,
`DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

## Production

    php smoke env:cache         # compile .env once; re-run after every .env change

## Working on the core

Composer installs `whitesmoke/core` from Packagist. To work on a local core checkout next to
this folder (`../whitesmoke-core`) instead, run:

    composer config repositories.local path ../whitesmoke-core
    composer require "whitesmoke/core:^0.2@dev"

This edits `composer.json`; do not commit it. To go back: `git checkout composer.json`
and `composer update whitesmoke/core`.

## License

MIT. See `LICENSE`.
