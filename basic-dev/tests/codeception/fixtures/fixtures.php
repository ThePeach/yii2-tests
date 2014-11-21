<?php
namespace tests\fixtures;

use app\models\User;

User::deleteAll(['username' => 'user']);

// (re)create a new user for the tests
$user = new User([
    'username' => 'user',
    'authkey' => uniqid()
]);
$userPassword = 'fixture';
$user->setPassword($userPassword);
$user->save();
