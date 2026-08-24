// The authors CRUD, which is the plain server-rendered half: full page loads, no modal. Its value
// here is the redirect-and-flash cycle and the validation errors coming back on the form.
//
// ⚠ The invalid e-mail is `debug@debug`, not `debug`. The field is `type=email`, so the browser
// refuses to submit anything without an `@` and the server never sees it -- and the point of the
// check is that the SERVER is the authority. `debug@debug` is what Chrome accepts and the
// `email:filter` rule rejects.
//
// Mutating: it truncates before it starts.
import { chromium } from 'playwright';
import { appears, requireDisposableDatabase, reporter, url } from './_e2e.mjs';

requireDisposableDatabase();

const { check, report } = reporter();

const browser = await chromium.launch();
const page = await browser.newPage({ viewport: { width: 1280, height: 900 } });

const jsErrors = [];
page.on('pageerror', (error) => jsErrors.push(error.message));

try {
    await page.goto(url('authors'));

    check('the list renders', await page.locator('h1', { hasText: 'Authors' }).count() === 1);
    check('it says there is nothing yet', await page.getByText('No authors yet.').count() === 1);

    await page.getByRole('link', { name: 'New Author' }).click();

    check('the create form opens as a page', await appears(page.locator('#authors-form')));
    check('not as a modal', await page.locator('#modal[open]').count() === 0);

    await page.fill('#name', 'Author2');
    await page.fill('#email', 'debug@debug');
    await page.locator('#authors-form button[type=submit]').click();

    const errors = page.locator('#alert-box');

    check('an invalid e-mail is refused', await appears(errors));
    check('the e-mail rule is the reason', (await errors.innerText()).includes('email'));
    check('the name typed in survived', await page.inputValue('#name') === 'Author2');

    await page.fill('#email', 'debug@jp7.com.br');
    await page.locator('#authors-form button[type=submit]').click();
    await page.waitForURL(url('authors'), { timeout: 5000 });

    const flash = page.locator('#flash-message');

    check('the author is on the list', await page.getByRole('link', { name: 'Author2' }).count() === 1);
    check('the flash says what happened', (await flash.innerText()).includes('Author was stored with success'));

    await page.getByRole('link', { name: 'Edit' }).click();

    check('the edit form opens', await appears(page.locator('#authors-form')));

    await page.fill('#name', 'Updated Author1');
    await page.locator('#authors-form button[type=submit]').click();
    await page.waitForURL(url('authors'), { timeout: 5000 });

    check('the rename stuck', await page.getByRole('link', { name: 'Updated Author1' }).count() === 1);

    await page.getByRole('link', { name: 'Updated Author1' }).click();

    check('the author reads back', await appears(page.getByText('debug@jp7.com.br')));

    await page.goto(url('authors'));
    await page.getByRole('button', { name: 'Delete' }).click();
    await page.waitForURL(url('authors'), { timeout: 5000 });

    check('the author is gone', await page.getByText('No authors yet.').count() === 1);
    check('no javascript errors', jsErrors.length === 0, jsErrors.join(' | '));
} catch (error) {
    check('the script ran to the end', false, error.message.split('\n')[0]);
} finally {
    await browser.close();
}

process.exit(report());
