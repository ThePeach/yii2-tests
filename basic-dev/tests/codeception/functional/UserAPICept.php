<?php
use tests\codeception\fixtures\UserFixture;

$I = new FunctionalTester($scenario);
$I->wantTo('test the user REST API');

// FIXME there must be a way to access fixture rows...
$user = [
    'id' => 1,
    'username' => 'user',
    'password' => 'something'
];

$I->amGoingTo('ensure I can fetch my own information while being authenticated');
$I->amHttpAuthenticated($user['username'], $user['password']);
//$I->haveHttpHeader('Authorization', 'Basic '.base64_encode($user->username.':'.$userPassword));
$I->sendGET('users/'.$user['id']);
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContains($user['username']);
$I->seeResponseContains('password');

$I->amGoingTo('update my own password');
$I->amHttpAuthenticated($user['username'], $user['password']);
//$I->haveHttpHeader('Authorization', 'Basic '.base64_encode($user->username.':'.$userPassword));
$newPassword = 'something else';
$I->sendPUT(
    'users/' . $user['id'],
    ['password' => $newPassword, 'authkey' => 'updated']
);
$I->seeResponseIsJson();
$I->seeResponseContains('true');
$I->seeResponseCodeIs(200);

$I->amGoingTo('check my new password works');
$I->amHttpAuthenticated($user['username'], $newPassword);
//$I->haveHttpHeader('Authorization', 'Basic '.base64_encode($user->username.':'.$newPassword));
$I->sendHEAD('users/'.$user['id']);
$I->seeResponseIsJson();
$I->seeResponseContains($user['username']);
$I->seeResponseCodeIs(200);
