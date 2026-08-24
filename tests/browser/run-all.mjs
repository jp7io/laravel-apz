/**
 * Runs every browser script, one at a time.
 *
 * Serially on purpose: each of them truncates the database before it starts, so two at once empty
 * each other's fixtures halfway through. There are four of them and the whole set is seconds.
 *
 *   npm run e2e:all
 *
 * It boots `php artisan serve` itself unless E2E_BASE names a server that is already up.
 */
import { spawn, spawnSync } from 'node:child_process';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = resolve(dirname(fileURLToPath(import.meta.url)), '../..');
const SCRIPTS = ['sweep', 'articles', 'authors', 'recommendations'];
const BASE = process.env.E2E_BASE ?? 'http://localhost:8011';

/**
 * Whether THIS app answers on BASE, not merely whether something does.
 *
 * `/up` is Laravel's health route, and a neighbour holding the port answers it with a 404 -- which
 * a bare "did the fetch resolve" check reads as success and then drives the wrong application.
 */
const reachable = async () => {
    try {
        return (await fetch(`${BASE}/up`, { signal: AbortSignal.timeout(1000) })).ok;
    } catch {
        return false;
    }
};

let server;

if (await reachable()) {
    console.log(`Using the app already answering on ${BASE}\n`);
} else {
    const port = new URL(BASE).port || '8000';
    console.log(`Nothing on ${BASE}, starting php artisan serve --port=${port}\n`);
    server = spawn('php', ['artisan', 'serve', `--port=${port}`], { cwd: ROOT, stdio: 'ignore' });

    for (let attempt = 0; attempt < 50 && !(await reachable()); attempt++) {
        await new Promise((resume) => setTimeout(resume, 200));
    }

    if (!(await reachable())) {
        server.kill();
        console.error(`Could not start the app on ${BASE}.`);
        process.exit(2);
    }
}

const failed = [];

for (const script of SCRIPTS) {
    console.log(`\n=== ${script} ===`);

    const run = spawnSync('node', [`tests/browser/${script}.mjs`], {
        cwd: ROOT,
        stdio: 'inherit',
        env: { ...process.env, E2E_BASE: BASE },
    });

    if (run.status !== 0) failed.push(script);
}

server?.kill();

console.log(`\n${SCRIPTS.length - failed.length}/${SCRIPTS.length} scripts passed`);

if (failed.length) console.log(`failed: ${failed.join(', ')}`);

process.exit(failed.length ? 1 : 0);
