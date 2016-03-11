<?php
use \Faker\Factory;

class ArticleCest
{
    public function _before(AcceptanceTester $I)
    {
        Artisan::call('db:truncate');
        $I->amOnPage('/articles');
    }

    public function _after(AcceptanceTester $I)
    {
    }

    //tests
    public function createAnValidArticle(AcceptanceTester $I)
    {
        $attributes = [
            'title' => 'Article Title',
            'content' => 'The new text!'
        ];

        $this->createAnArticle($I, $attributes);

        $I->waitForText('Article was stored with success', 10, '#content .alert-info');
        $I->see($attributes['title'], 'a');
    }

    public function createAnInvalidArticle(AcceptanceTester $I)
    {
        $attributes = [
            'title' => '12',
            'content' => ''
        ];

        $this->createAnArticle($I, $attributes);

        $I->waitForText('The title must be at least 3 characters.', 10, '#modal .alert-danger');
        $I->amOnPage('/articles');
        $I->dontSee($attributes['title'], 'a');
    }

    public function editAnArticleWithValidAttributes(AcceptanceTester $I)
    {
        $attributes = [
            'title' => Factory::create()->sentence($nbWords = 6),
            'content' => 'The updated text!'
        ];

        $this->editAnArticle($I, $attributes);

        $I->waitForText('Article was updated with success', 10, '#content .alert-info');
        $I->see($attributes['title'], 'a');
    }

    public function editAnArticleWithInvalidAttributes(AcceptanceTester $I)
    {
        $attributes = [
            'title' => 'Invalid article',
            'content' => ''
        ];

        $this->editAnArticle($I, $attributes);

        $I->waitForText('The content field is required.', 10, '#content .alert-danger');
        $I->amOnPage('/articles');
        $I->dontSee($attributes['title'], 'a');
    }

    private function createAnArticle(AcceptanceTester $I, $attributes)
    {
        $I->haveAuthor();
        $I->click('New Article');
        $this->submitTheForm($I, $attributes);
    }

    private function editAnArticle(AcceptanceTester $I, $attributes)
    {
        $I->haveAuthor();
        $I->haveArticle();
        $I->amOnPage('/articles');
        $I->click('[href*=edit]');
        $this->submitTheForm($I, $attributes);
    }

    private function submitTheForm(AcceptanceTester $I, $attributes)
    {
        $I->waitForElement('#articles-form', 2);
        $I->fillField('#articles-form #title', $attributes['title']);
        $I->fillField('#articles-form #content', $attributes['content']);
        $I->click('#articles-form .btn-primary');
    }
}
