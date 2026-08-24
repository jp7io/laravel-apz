// The articles CRUD, and the half of it a PHP test cannot see: "New Article" opens the create
// form in a modal, submits it over fetch, and swaps the refreshed list back into the page. That
// path is server-rendered markup PLUS the bundle, so a broken build renders as a 200 that does
// nothing when clicked.
//
// Mutating: it truncates before it starts.
import { chromium } from 'playwright';
import { appears, requireDisposableDatabase, reporter, seedAuthor, url } from './_e2e.mjs';

requireDisposableDatabase();

const { check, report } = reporter();
const author = seedAuthor();

const browser = await chromium.launch();
const page = await browser.newPage({ viewport: { width: 1280, height: 900 } });

const jsErrors = [];
page.on('pageerror', (error) => jsErrors.push(error.message));

try {
    await page.goto(url('articles'));

    check('the list renders', await page.locator('h1', { hasText: 'Articles' }).count() === 1);
    check('it says there is nothing yet', await page.getByText('No articles yet.').count() === 1);

    // Creating: the modal path.
    await page.getByRole('link', { name: 'New Article' }).click();

    check('the create form arrives in the modal', await appears(page.locator('#modal #articles-form')));
    check('the dialog is open', await page.locator('#modal[open]').count() === 1);
    check('the author dropdown is populated', await page.locator('#modal #author_id option').count() === 1);

    await page.locator('#modal [data-close-modal]').click();

    check('the close button dismisses it', await appears(page.locator('#modal'), { state: 'hidden' }));

    await page.getByRole('link', { name: 'New Article' }).click();

    check('and it reopens', await appears(page.locator('#modal #articles-form')));

    await page.fill('#modal #title', '12');
    await page.locator('#modal #articles-form button[type=submit]').click();

    const modalErrors = page.locator('#modal #alert-box');

    check('an invalid article comes back with its errors', await appears(modalErrors));
    check('and the modal stayed open', await page.locator('#modal[open]').count() === 1);
    check('both failures are listed', await modalErrors.locator('li').count() === 2, await modalErrors.innerText());
    check('the title rule is one of them', (await modalErrors.innerText()).includes('title'));

    await page.fill('#modal #title', 'Article Title');
    await page.fill('#modal #content', 'The new text!');
    await page.locator('#modal #articles-form button[type=submit]').click();

    const flash = page.locator('#page-content #flash-message');

    check('a valid article closes the modal and refreshes the list', await appears(flash));
    check('the modal closed', await page.locator('#modal[open]').count() === 0);
    check('the article is on the list', await page.getByRole('link', { name: 'Article Title' }).count() === 1);
    check('with its author', await page.getByText(author.name).count() >= 1);
    check('the flash says what happened', (await flash.innerText()).includes('Article was stored with success'));
    check('the page never reloaded', page.url() === url('articles'));

    // Editing: a plain page, no modal.
    await page.getByRole('link', { name: 'Edit' }).click();

    check('the edit form opens as a page', await appears(page.locator('#articles-form')));
    check('it is prefilled', await page.inputValue('#title') === 'Article Title');

    await page.fill('#content', '');
    await page.locator('#articles-form button[type=submit]').click();

    const pageErrors = page.locator('#page-content #alert-box');

    check('an invalid edit comes back with its errors', await appears(pageErrors));
    check('the content rule is one of them', (await pageErrors.innerText()).includes('content'));
    check('the title typed in survived', await page.inputValue('#title') === 'Article Title');

    await page.fill('#title', 'A better title');
    await page.fill('#content', 'The updated text!');
    await page.locator('#articles-form button[type=submit]').click();
    await page.waitForURL(url('articles'), { timeout: 5000 });

    check('a valid edit lands back on the list', await page.getByRole('link', { name: 'A better title' }).count() === 1);
    check('the flash says what happened', (await flash.innerText()).includes('Article was updated with success'));

    // Reading, then deleting.
    await page.getByRole('link', { name: 'A better title' }).click();

    check('the article reads back', await appears(page.getByText('The updated text!')));

    await page.goto(url('articles'));
    await page.getByRole('button', { name: 'Delete' }).click();
    await page.waitForURL(url('articles'), { timeout: 5000 });

    check('the article is gone', await page.getByText('No articles yet.').count() === 1);
    check('no javascript errors', jsErrors.length === 0, jsErrors.join(' | '));
} catch (error) {
    check('the script ran to the end', false, error.message.split('\n')[0]);
} finally {
    await browser.close();
}

process.exit(report());
