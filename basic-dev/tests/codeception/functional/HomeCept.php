<?php

$I = new FunctionalTester($scenario);
$I->wantTo('ensure that home page works');
$I->amOnPage(Yii::$app->homeUrl);

$I->expect('the title to be set correctly');
$I->seeInTitle('My Yii Application');

$I->expectTo('see all the links of the menu');
$url = $I->grabFromCurrentUrl();
$I->seeLink('Home', $url);
$I->seeLink('About', '/about');
$I->seeLink('Login', '/login');
$I->seeLink('Contact', '/contact');
$I->dontSeeLink('About', 'site/about');
$I->dontSeeLink('Login', 'site/login');
$I->dontSeeLink('About', 'site/contact');

$I->expectTo('see a self-referencing link to my company homepage');
$I->seeLink('My Company', $url);

$I->expectTo('see the link of the homepage as selected');
$I->seeElement('//li[@class="active"]/a[contains(.,"Home")]');
