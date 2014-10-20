<?php

namespace app\modules\v1\controllers;

use app\modules\v1\components\BaseRestController;

class UserController extends BaseRestController
{
    public function actionAuth()
    {
        return $this->render('auth');
    }

}
