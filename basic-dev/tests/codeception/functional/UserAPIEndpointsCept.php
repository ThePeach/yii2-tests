<?php

$I = new FunctionalTester($scenario);
$I->wantTo('test the user REST API endpoints');

$I->amGoingTo('ensure list users is not found without being authenticated');
$I->sendHEAD('users');
$I->seeResponseCodeIs(404);

$I->amGoingTo('ensure I cannot fetch my own information while not authenticated');
$I->sendGET('users/1');
$I->seeResponseCodeIs(401);

$I->amGoingTo('ensure I cannot view someone else');
$I->amHttpAuthenticated('admin', 'admin');
//$I->haveHttpHeader('Authorization', 'Basic '.base64_encode('admin:admin'));
$I->sendGET('users/2');
$I->seeResponseCodeIs(403);

$I->wantTo('ensure remaining actions are not available');

$I->amGoingTo('ensure list users is not found');
$I->sendHEAD('users');
$I->seeResponseCodeIs(404);
$I->sendGET('users');
$I->seeResponseCodeIs(404);

$I->amGoingTo('ensure create user is not found');
$I->sendPOST('users');
$I->seeResponseCodeIs(404);

$I->amGoingTo('ensure delete user is not found');
$I->sendDELETE('users/1');
$I->seeResponseCodeIs(404);

$I->amGoingTo('ensure options is not found');
$I->sendOPTIONS('users');
$I->seeResponseCodeIs(404);
$I->sendOPTIONS('users/1');
$I->seeResponseCodeIs(404);
