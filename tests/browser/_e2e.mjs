/**
 * Shared plumbing for the browser tests that drive the real app.
 *
 * Everything here needs the app running (E2E_BASE, default localhost:8011) and a database these
 * scripts are allowed to empty, because each of them starts from `db:truncate`.
 */
import { execFileSync } from 'node:child_process';

export const BASE = process.env.E2E_BASE ?? 'http://localhost:8011';

const ROOT = new URL('../..', import.meta.url).pathname;

/** App URL: url('articles') -> http://localhost:8011/articles */
export const url = (path = '') => `${BASE}/${path.replace(/^\//, '')}`;

/**
 * Run PHP against a booted app and return ONLY what the snippet echoed.
 *
 * Fenced, because a warning or a query log printed by the framework lands on the same stdout and
 * scraping the tail of it picks up whichever line came last.
 *
 * Piped to php's stdin rather than passed as `php -r`: snippets carry $variables and quotes the
 * shell would expand.
 */
export function php(code) {
  const out = execFileSync('php', {
    cwd: ROOT,
    encoding: 'utf8',
    input: `<?php
      require 'vendor/autoload.php';
      $app = require 'bootstrap/app.php';
      $app->make(Illuminate\\Contracts\\Console\\Kernel::class)->bootstrap();
      echo "\\n<<<E2E:";
      ${code}
      echo ":E2E>>>\\n";`,
  });

  const fenced = out.match(/<<<E2E:([\s\S]*?):E2E>>>/);

  if (!fenced) throw new Error(`php(): no fenced output.\n${out.slice(-800)}`);

  return fenced[1].trim();
}

export const phpJson = (code) => JSON.parse(php(code));

export const artisan = (...args) =>
  execFileSync('php', ['artisan', ...args], { cwd: ROOT, encoding: 'utf8' });

/**
 * Refuse to run against anything but a throwaway database.
 *
 * Every script here truncates before it starts and cannot roll that back: the writes happen in
 * the app's own process, not in this one. The check is the app's environment rather than a list
 * of database names, so an unknown setup fails closed.
 */
export function requireDisposableDatabase() {
  const environment = php("echo app()->environment();");

  if (!['local', 'testing'].includes(environment)) {
    console.error(
      `Refusing to run against APP_ENV=${environment}: these scripts truncate every table.\n` +
      `Point E2E_BASE at a local app, or run them against .env.example's sqlite database.`
    );
    process.exit(2);
  }

  artisan('db:truncate');
}

/** An author with the articles a script needs, returned with the ids it will drive. */
export function seedAuthor({ articles = 0 } = {}) {
  return phpJson(`
    $author = App\\Models\\Author::factory()->create(['name' => 'Author1']);
    $articles = App\\Models\\Article::factory(${articles})->create(['author_id' => $author->id]);
    echo json_encode(['id' => $author->id, 'name' => $author->name, 'email' => $author->email,
                      'articles' => $articles->map->only('id', 'title')->all()]);
  `);
}

/**
 * Wait for a locator and answer whether it turned up, rather than throwing.
 *
 * A bare `waitFor` kills the script on timeout, so the tally never prints and the checks after it
 * never run: one slow fetch reads as a crash instead of as one failed expectation.
 */
export async function appears(locator, { state = 'visible', timeout = 5000 } = {}) {
  try {
    await locator.waitFor({ state, timeout });

    return true;
  } catch {
    return false;
  }
}

export function reporter() {
  const results = [];

  const check = (name, ok, detail = '') => {
    results.push({ name, ok: !!ok });
    console.log(`  ${ok ? 'PASS' : 'FAIL'}  ${name}${detail ? `  -- ${detail}` : ''}`);

    return !!ok;
  };

  /** Print the tally and set the exit code, so a script ending on a bare report() still fails. */
  const report = (label = '') => {
    const failed = results.filter((result) => !result.ok);

    console.log(`\n${results.length - failed.length}/${results.length} checks passed${label ? `  (${label})` : ''}`);
    process.exitCode = failed.length ? 1 : 0;

    return failed.length;
  };

  return { check, report };
}
