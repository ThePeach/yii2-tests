<?php

return [
    'admin' => [
        'id' => 1,
        'username' => 'admin',
        'authkey' => uniqid(),
        'password' => Yii::$app->security->generatePasswordHash('admin'),
    ],
    'demo' => [
        'id' => 2,
        'username' => 'demo',
        'authkey' => uniqid(),
        'password' => Yii::$app->security->generatePasswordHash('demo'),
    ],
    'basic' => [
        'username' => 'user',
        'authkey' => uniqid(),
        'password' => Yii::$app->security->generatePasswordHash('something'),
    ],
];
