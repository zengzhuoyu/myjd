<?php

namespace app\controllers;

use yii\web\Controller;

class OrderController extends Controller
{
	//订单中心
	public function actionIndex()
	{

		// return $this->renderPartial('index');
		
        		$this->layout = 'layout2';  		
		return $this->render('index');

	}

	//收银台
	public function actionCheck()
	{

		// return $this->renderPartial('check');

        		$this->layout = 'layout1';  
		return $this->render('check');

	}

}
