<?php

class ExampleTest extends \Codeception\TestCase\Test {

    /** @var AcceptanceTester */
    protected $tester;

    protected function _before() {
    }

    public function testAddPage() {
        $this->tester->wantTo('Can navigate to "add" page');
        $this->tester->amOnPage('/');

        $this->tester->click('Event hinzufügen');
        $this->tester->waitForElement('.Denkmal_Page_Add', 1);
        $this->tester->see('Event hinzufügen', 'h1');
        $this->tester->seeInCurrentUrl('add');
    }

    public function testNewEvent() {
        $this->tester->wantTo('Can submit a new event');
        $this->tester->amOnPage('/add');

        $this->tester->fillField('#s2id_autogen2', 'My venue');
        $this->tester->waitForElement('.select2-highlighted', 1);
        $this->tester->click('.select2-highlighted');
        $this->tester->waitForElement('[name="venueAddress"]', 1);
        $this->tester->fillField('venueAddress', 'My Address 1');
        $this->tester->fillField('venueUrl', 'http://www.example.com/');
        $this->tester->selectOption('date[year]', '2015');
        $this->tester->selectOption('date[month]', '3');
        $this->tester->selectOption('date[day]', '4');
        $this->tester->fillField('fromTime', '20:30');
        $this->tester->fillField('title', 'My Title');

        $this->tester->click('Hinzufügen');
        $this->tester->dontSee('Der Event wurde hinzugefügt');
        $this->tester->waitForJS('return !$.active;', 1);
        $this->tester->see('Der Event wurde hinzugefügt');
    }
}
