<?php

namespace app\modules\controllers;

use yii\web\Controller;

use Yii;

use app\modules\models\Admin;

class ManageController extends Controller
{

    public function actionMailchangepass()
    {

        $this->layout = false;
        $time = Yii::$app->request->get("timestamp");
        $adminuser = Yii::$app->request->get("adminuser");
        $token = Yii::$app->request->get("token");

        $model = new Admin;
        //由此可见,今后可能复用的方法就该写在model里
        $myToken = $model->createToken($adminuser, $time);
        if ($token != $myToken) {
            $this->redirect(['public/login']);
            Yii::$app->end();
        }
        //超过5分钟了,失效
        if (time() - $time > 300) {
            $this->redirect(['public/login']);
            Yii::$app->end();
        }

        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if($model->changePass($post)){
                // $this->redirect('public/login');
                // 或者显示修改成功
                Yii::$app->session->setFlash('info','密码修改成功');
            }
        }

        $model->adminuser = $adminuser;
        return $this->render('mailchangepass',['model' => $model]);
    }

}
