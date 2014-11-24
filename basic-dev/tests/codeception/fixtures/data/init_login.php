<?php

return [
    'user' => [
        'username' => 'user',
        'authkey' => uniqid(),
        // password_0
        'password' => Yii::$app->security->generatePasswordHash('something'),
    ],
];
