# Tests

Two suites, split by what they need to run.

| Suite | Needs | How to run |
|---|---|---|
| PHPUnit (`tests/Unit`, `tests/Feature`) | PHP only | `composer test` |
| Browser (`tests/browser`) | a built bundle, a running app, Chromium | `npm run e2e:all` |

```bash
composer test              # everything PHP: 39 tests, under a second
composer test:unit         # no database
composer test:feature      # HTTP + database, on an in-memory sqlite

npm run e2e:all            # all four browser scripts, serially
npm run e2e:sweep          # every screen: status, chrome, console errors, json
npm run e2e:articles       # one flow, against an app you already have running
```

## PHPUnit

`tests/Unit` is what needs no database: the weather cache and the reCAPTCHA rule, both driven
through `Http::fake()`. `tests/Feature` is the HTTP surface, on an in-memory sqlite that
`phpunit.xml` configures.

`Tests\TestCase` calls `withoutVite()`, so the suite runs with no node build present. The real
bundle is the browser suite's business.

## Browser

Plain Playwright driven by node scripts, sharing `_e2e.mjs`. There is no test runner: a script is
a program that prints `PASS`/`FAIL` lines and exits non-zero if any failed.

`sweep.mjs` walks every screen and answers "is the site up"; the other three each drive one
journey end to end.

These cover what the PHP suite structurally cannot see: the create form arriving in a modal over
fetch, the refreshed list being swapped back into the page, and the bundle being loadable at all.
A broken build is a 200 that does nothing when clicked, and no HTTP test can tell the difference.

`_e2e.mjs` gives each script:

- `url(path)` and `BASE` (`E2E_BASE`, default `http://localhost:8011`)
- `php(code)` / `phpJson(code)`, running a snippet against a booted app for fixtures and for
  assertions the browser cannot make (what is on the queue, for instance)
- `seedAuthor({ articles })`
- `appears(locator)`, a `waitFor` that answers instead of throwing, so a timeout is one failed
  check rather than a dead script
- `reporter()` -> `check(name, ok, detail)` and `report()`

### Guardrails

- **Every script truncates before it starts**, and cannot roll that back: the writes happen in the
  app's process, not in the script's. `requireDisposableDatabase()` refuses to run unless the app
  reports `APP_ENV=local` or `testing`.
- **They run serially.** `run-all.mjs` does not parallelise, because they would empty each other's
  fixtures halfway through.
- **The port is pinned to 8011** (`SERVER_PORT` in `.env`), and `run-all.mjs` checks that `/up`
  answers `200` rather than merely that something answered. A neighbouring project holding the
  port replies 404, which a bare reachability check reads as success and then drives the wrong
  application. That is not hypothetical: 8000 belongs to another stack on the machine this was
  written on.
- **The `array` cache store is not the store the app runs on.** It holds live references, so it
  cannot see a value the framework refuses to unserialize, and `phpunit.xml` selects it for the
  whole suite. `WeatherCacheTest` switches to `database` on purpose; anything that caches a
  non-scalar needs the same treatment.
- **`type=email` fields mean the browser is the first validator.** A script proving the server
  rejects an address has to send one the browser will submit: `debug@debug` passes Chrome and
  fails `email:filter`, while plain `debug` never leaves the page.
