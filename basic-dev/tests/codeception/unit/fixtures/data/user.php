<?php

return [
    'admin' => [
        'id' => 1,
        'username' => 'admin',
        'password' => 'valid password',
        'authkey' => 'valid authkey'
    ],
    'user_basic' => [
        'username' => '-=[ valid username ]=-',
        'password' => 'This is another valid password!!! :) <script></script>',
        'authkey' => '00%am|%lk;@P .'
    ],
    'user_accessToken' => [
        'username' => '-=[ valid username ]=-',
        'password' => 'This is another valid password!!! :) <script></script>',
        'authkey' => uniqid(),
        'accessToken' => uniqid()
    ],
    'user_id' => [
        'id' => 4,
        'username' => '-=[ valid username ]=-',
        'password' => 'This is another valid password!!! :) <script></script>',
        'authkey' => uniqid()
    ],
];