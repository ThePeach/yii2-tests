<?php

namespace app\modules\v1\controllers;

use app\modules\v1\components\BaseRestController;

class DefaultController extends BaseRestController
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
