<?php

namespace app\controllers;

use yii\web\Controller;

class ProductController extends Controller
{
    //商品分类页	
    public function actionIndex()
    {

        // return $this->renderPartial('index');
        
        $this->layout = 'layout2';               
        return $this->render('index');
        
    }

    //商品详情页
    public function actionDetail()
    {

        // return $this->renderPartial('detail');
        
        $this->layout = 'layout2';          
        return $this->render('detail');
        
    }    

}
