// Walks every screen the app serves, looking for the failures a status code cannot see: a page
// that renders 200 with a console error, a missing bundle, or chrome that did not come out.
//
// This is the gate that answers "is the whole site up", as opposed to the flow scripts, which
// each prove one journey works.
//
// Read-only, but it seeds fixtures, so it truncates first like the others.
import { chromium } from 'playwright';
import { appears, requireDisposableDatabase, reporter, seedAuthor, url } from './_e2e.mjs';

requireDisposableDatabase();

const { check, report } = reporter();
const author = seedAuthor({ articles: 1 });
const article = author.articles[0];

const SCREENS = [
    { path: '/', heading: 'Laravel Apz' },
    { path: 'articles', heading: 'Articles' },
    { path: 'articles/create', heading: 'New Article' },
    { path: `articles/${article.id}`, heading: article.title },
    { path: `articles/${article.id}/edit`, heading: 'Edit Article' },
    { path: `articles/${article.id}/recommendations/create`, heading: 'Recommend' },
    { path: 'authors', heading: 'Authors' },
    { path: 'authors/create', heading: 'New Author' },
    { path: `authors/${author.id}`, heading: author.name },
    { path: `authors/${author.id}/edit`, heading: 'Edit Author' },
];

const JSON_ENDPOINTS = [
    { path: 'articles', holds: article.title },
    { path: `articles/${article.id}`, holds: article.title },
    { path: 'authors', holds: author.name },
    { path: `authors/${author.id}`, holds: author.email },
];

const browser = await chromium.launch();
const page = await browser.newPage({ viewport: { width: 1280, height: 900 } });

let consoleErrors = [];
let jsErrors = [];

page.on('console', (message) => message.type() === 'error' && consoleErrors.push(message.text()));
page.on('pageerror', (error) => jsErrors.push(error.message));

try {
    for (const screen of SCREENS) {
        consoleErrors = [];
        jsErrors = [];

        const response = await page.goto(url(screen.path));

        check(`${screen.path} answers 200`, response.status() === 200, String(response.status()));
        check(`${screen.path} renders its heading`, await appears(page.getByRole('heading', { name: new RegExp(screen.heading, 'i') }).first()));
        check(`${screen.path} keeps the navbar`, await page.getByRole('link', { name: 'Laravel Apz' }).count() === 1);
        check(`${screen.path} has no console errors`, consoleErrors.length === 0, consoleErrors.join(' | '));
        check(`${screen.path} has no javascript errors`, jsErrors.length === 0, jsErrors.join(' | '));
    }

    // The bundle, which every screen above links and none of them proves arrived: a stale or
    // missing manifest is a 200 whose page simply does nothing when clicked.
    //
    // Asserted through COMPUTED STYLE rather than the asset URL. The app serves /build/assets/…
    // from the manifest and http://…:5177/… when the vite dev server is up, so a check pinned to
    // either path fails against a perfectly healthy app running the other way.
    const assets = await page.evaluate(() => {
        const background = getComputedStyle(document.body).backgroundColor;

        return {
            background,
            styled: !['rgba(0, 0, 0, 0)', 'rgb(255, 255, 255)', 'transparent'].includes(background),
            modal: typeof document.getElementById('modal')?.showModal === 'function',
            centred: getComputedStyle(document.getElementById('modal')).marginTop === 'auto',
        };
    });

    check('the stylesheet loaded and applied', assets.styled, assets.background);
    check('the dialog is a real dialog', assets.modal);
    check('and Tailwind did not zero the margin that centres it', assets.centred);

    for (const endpoint of JSON_ENDPOINTS) {
        const response = await page.request.get(url(endpoint.path), { headers: { Accept: 'application/json' } });
        const body = await response.text();

        check(`${endpoint.path} serves json`, response.headers()['content-type']?.includes('json'), response.headers()['content-type']);
        check(`${endpoint.path} json holds the record`, body.includes(endpoint.holds));
    }

    const missing = await page.request.get(url('nope'));

    check('an unknown path is a 404', missing.status() === 404, String(missing.status()));
} catch (error) {
    check('the script ran to the end', false, error.message.split('\n')[0]);
} finally {
    await browser.close();
}

process.exit(report());
