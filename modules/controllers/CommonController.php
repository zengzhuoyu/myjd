<?php

namespace app\modules\controllers;

use yii\web\Controller;
use Yii;

class CommonController extends Controller
{
    public function init()//所有方法执行前，init会自动执行
    {
        if (Yii::$app->session['admin']['isLogin'] != 1) {
            return $this->redirect(['/admin/public/login']);
        }
    }
}
