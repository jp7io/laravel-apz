# Laravel Apz

#### The guide to build a Laravel app from a to z.

A small CRUD application kept current with the framework: articles, authors, a recommendation
mailer and a weather widget, wired the way a Laravel 13 app is wired today.

#### Step-by-Step Tutorial

https://github.com/jp7io/laravel-apz/wiki

> The wiki still describes the Laravel 5.2 build: 17 of its 31 pages walk through Bower, Gulp,
> Elixir, Codeception, Heroku or Codeship, none of which this codebase uses any more. The
> narrative holds, the commands do not.

#### Screencast

https://www.youtube.com/watch?v=AKFoJ2YDPmI&list=PLFEMeqXSeh3xpnSCvL66t87_LcTNYOoFL (portuguese only)

#### Includes

* Laravel 13 on PHP 8.3+
* Restful CRUD and associations, API first (the same routes answer HTML and JSON)
* Blade components, form requests and a custom validation rule
* Ajax CRUD in a native `<dialog>`, over `fetch`, with no frontend framework
* Tailwind 4 built by Vite
* Queued mailable
* Scheduled command feeding a cached weather widget
* Feature and unit tests with PHPUnit, browser tests with Playwright (a full-site sweep plus one script per journey)
* Pint and PHPStan (level 8) in CI

## Getting started

```sh
composer setup
php artisan serve
```

`composer setup` installs both dependency sets, writes a `.env`, creates the sqlite database,
migrates it and builds the assets. The app answers on http://localhost:8011.

Seed it with something to look at:

```sh
php artisan db:seed
```

Two optional keys in `.env`. Without either one the app works and the feature it drives stays out
of the way, rather than failing:

| Key | What it turns on |
|---|---|
| `OPENWEATHER_KEY` | the temperature in the navbar |
| `RECAPTCHA_SITE_KEY` / `RECAPTCHA_SECRET_KEY` | reCAPTCHA v2 on every form |

## Working on it

```sh
composer dev               # serve + queue worker + vite, together
composer test              # PHPUnit
composer analyse           # PHPStan
composer format            # Pint

npm run e2e:all            # Playwright, boots the app itself
```

Browser tests need Chromium once: `npx playwright install chromium`. `.npmrc` sets
`ignore-scripts=true`, so npm will not fetch it for you.

Testing is documented in [tests/README.md](tests/README.md).

## Notes

The queue is `database` by default. `predis/predis` ships with the app, so switching to Redis is
`QUEUE_CONNECTION=redis` in `.env` and a worker.

`php artisan serve` is pinned to 8011 through `SERVER_PORT` and fails rather than sliding onto a
neighbouring port, which would otherwise shadow another application on the same machine.
