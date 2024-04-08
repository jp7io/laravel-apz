<?php

class AuthorControllerCest
{
    public function _before(FunctionalTester $I)
    {
        Artisan::call('db:truncate');
        $I->haveHttpHeader('Accept', 'application/json');
    }

    public function _after(FunctionalTester $I)
    {
    }

    // tests
    public function createAnValidAuthor(FunctionalTester $I)
    {
        $attributes = ['name' => 'Author1', 'email' => 'debug@jp7.com.br'];
        $I->sendPOST('/authors', $attributes);
        $I->sendGET('/authors');
        $I->seeResponseContainsJson($attributes);
    }

    public function createAnInvalidAuthor(FunctionalTester $I)
    {
        $attributes = ['name' => 'Author2', 'email' => 'debug'];
        $I->sendPOST('/authors', $attributes);
        $I->sendGET('/authors');
        $I->dontSeeResponseContainsJson($attributes);
    }

    public function editAnAuthorWithValidAttributes(FunctionalTester $I)
    {
        $author = $I->haveAuthor();
        $attributes = ['name' => 'Updated Author1', 'email' => 'debug+updated@jp7.com.br'];
        $I->sendPUT('/authors/' . $author->id, $attributes);
        $I->sendGET('/authors');
        $I->seeResponseContainsJson($attributes);
    }

    public function editAnAuthorWithInvalidAttributes(FunctionalTester $I)
    {
        $author = $I->haveAuthor();
        $attributes = ['id' => $author->id, 'name' => 'Updated Author2', 'email' => 'debug'];
        $I->sendPUT('/authors/' . $author->id, $attributes);
        $I->sendGET('/authors');
        $I->dontSeeResponseContainsJson($attributes);
    }
}
