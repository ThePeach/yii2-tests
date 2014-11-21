<?php
require dirname(__FILE__) . "/../fixtures/fixtures.php";

$I = new FunctionalTester($scenario);
$I->wantTo('test the user REST API');

$I->amGoingTo('ensure I can fetch my own information while being authenticated');
$I->haveHttpHeader('Authorization', 'Basic '.base64_encode($user->username.':'.$userPassword));
$I->sendGET('users/'.$user->id);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains($user->username);
$I->seeResponseContains('password');

$I->amGoingTo('update my own password');
$I->haveHttpHeader('Authorization', 'Basic '.base64_encode($user->username.':'.$userPassword));
$newPassword = 'something';
$I->sendPUT(
    'users/' . $user->id,
    ['password' => $newPassword, 'authkey' => 'updated']
);
$I->seeResponseIsJson();
$I->seeResponseContains('true');
$I->seeResponseCodeIs(200);

$I->amGoingTo('check my new password works');
$I->haveHttpHeader('Authorization', 'Basic '.base64_encode($user->username.':'.$newPassword));
$I->sendHEAD('users/'.$user->id);
$I->seeResponseIsJson();
$I->seeResponseContains($user->username);
$I->seeResponseCodeIs(200);
