<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('perform actions and see result');

$I->amGoingTo('authenticate without credentials');
$I->haveHttpHeader('Accept', 'application/json');
$I->sendHEAD('users');
$I->seeResponseCodeIs(401);

$I->amGoingTo('authenticate with wrong credentials');
$I->haveHttpHeader('Authorize', 'Basic '.base64_encode('admin:password'));
$I->sendHead('users');
$I->seeResponseCodeIs(401);

$I->amGoingTo('authenticate with correct credentials');
$I->haveHttpHeader('Authorization', 'Basic '.base64_encode('admin:admin'));
$I->sendHead('users');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();

$I->amGoingTo('list all the users');
$I->sendGet('users');
$I->seeResponseCodeIs(200);
$I->seeResponseContains('admin');
