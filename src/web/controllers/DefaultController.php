<?php

namespace yii2module\test\web\controllers;

use yii\web\Controller;

class DefaultController extends Controller
{
	
	public function actionIndex()
	{
		return $this->render('index');
	}

}
