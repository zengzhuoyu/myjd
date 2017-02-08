<?php

namespace app\modules\controllers;

use yii\web\Controller;

use Yii;

use app\modules\models\Admin;

class PublicController extends Controller
{

    public function actionLogin()
    {
        $this->layout = false;
        $model = new Admin;
        if (Yii::$app->request->isPost) {//判断是否是post方式提交
            $post = Yii::$app->request->post();//获取提交的值
            if ($model->login($post)) {

                $this->redirect(['default/index']);//跳转至后台首页
                Yii::$app->end();
            }
        }
        return $this->render("login", ['model' => $model]);
    }

    public function actionLogout()
    {
        Yii::$app->session->removeAll();//删除所有的session
        if (!isset(Yii::$app->session['admin']['isLogin'])) {

            $this->redirect(['public/login']);
            Yii::$app->end();
        }
        $this->goback();//从哪来回到哪去
    }    

    //找回密码
    public function actionSeekpassword(){

        $this->layout = false;
        $model = new Admin;

        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
             if($model->seekPass($post)){
                Yii::$app->session->setFlash('info','电子邮件已发送成功,请注意查收');
             }
        }
        return $this->render('seekpassword',['model' => $model]); 
    }
}
