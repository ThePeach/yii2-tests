<?php

namespace app\modules\v1\controllers;

use app\models\User;
use yii\filters\auth\HttpBasicAuth;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;

class UserController extends ActiveController
{
    public $modelClass = 'app\models\User';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => HttpBasicAuth::className(),
            'auth' => function ($username, $password) {
                    /** @var User $user */
                    $user = User::findByUsername($username);
                    if ($user && $user->validatePassword($password)) {
                        return $user;
                    }
                    return null;
                }
        ];

        return $behaviors;
    }

//    public function actionView($id)
//    {
//        return User::findOne($id);
//    }

//    public function actionAuth()
//    {
//        return $this->render('auth');
//    }

    public function checkAccess($action, $model = null, $params = [])
    {
        if (\Yii::$app->user->isGuest) {
            throw new ForbiddenHttpException;
        }
    }
}
