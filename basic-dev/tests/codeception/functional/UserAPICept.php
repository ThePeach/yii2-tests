<?php 
$I = new FunctionalTester($scenario);
$I->wantTo('perform actions and see result');

$I->haveHttpHeader('Accept', 'application/json');
$I->sendGET('/api/v1/user/login', ['parentCategory'=>'her']);
$I->seeResponseIsJson();