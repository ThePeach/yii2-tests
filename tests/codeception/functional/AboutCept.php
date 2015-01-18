<?php

use tests\codeception\_pages\AboutPage;

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that about works');
AboutPage::openBy($I);
$I->seeInTitle('About');
$I->see('About', 'h1');

$I->expectTo('see the link of to the about page as selected');
$I->seeElement('//li[@class="active"]/a[contains(.,"About")]');
