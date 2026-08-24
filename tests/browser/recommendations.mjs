// Recommending an article by e-mail. The assertion that matters is on the QUEUE rather than on the
// screen: the mailable is queued, so a green flash says nothing about whether anything was
// actually handed to the mailer.
//
// Mutating: it truncates before it starts.
import { chromium } from 'playwright';
import { appears, php, phpJson, requireDisposableDatabase, reporter, seedAuthor, url } from './_e2e.mjs';

requireDisposableDatabase();

const { check, report } = reporter();
const article = seedAuthor({ articles: 1 }).articles[0];

const browser = await chromium.launch();
const page = await browser.newPage({ viewport: { width: 1280, height: 900 } });

try {
    await page.goto(url('articles'));
    await page.getByRole('link', { name: 'Recommend' }).click();

    check('the form opens', await appears(page.locator('#recommendations-form')));
    check('it names the article', (await page.innerText('h2')).includes(article.title));

    await page.fill('#email', 'debug@debug');
    await page.locator('#recommendations-form button[type=submit]').click();

    const errors = page.locator('#alert-box');

    check('an invalid e-mail is refused', await appears(errors));
    check('the e-mail rule is the reason', (await errors.innerText()).includes('email'));
    check('nothing was queued', phpJson('echo DB::table("jobs")->count();') === 0);

    await page.fill('#email', 'friend@jp7.com.br');
    await page.locator('#recommendations-form button[type=submit]').click();
    await page.waitForURL(url('articles'), { timeout: 5000 });

    check(
        'the flash says what happened',
        (await page.locator('#flash-message').innerText()).includes('Your recommendation was sent')
    );
    check('one job was queued', phpJson('echo DB::table("jobs")->count();') === 1);
    check('and it is the recommendation', php('echo DB::table("jobs")->value("payload");').includes('ArticleRecommendation'));

    // The worker has to be able to run it: a mailable whose model cannot be unserialised fails
    // here and nowhere else, since nothing in the request path ever opens the payload.
    check('the worker sends it', php(`
        \\Illuminate\\Support\\Facades\\Mail::fake();
        \\Illuminate\\Support\\Facades\\Artisan::call('queue:work', ['--once' => true, '--quiet' => true]);
        echo DB::table('jobs')->count();
    `) === '0');
} catch (error) {
    check('the script ran to the end', false, error.message.split('\n')[0]);
} finally {
    await browser.close();
}

process.exit(report());
