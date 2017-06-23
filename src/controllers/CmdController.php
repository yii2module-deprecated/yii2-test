<?php

namespace yii2module\test\controllers;

use Yii;
use yii2lab\console\yii\console\Controller;
use yii2module\test\helpers\Test;
use yii2lab\console\helpers\input\Question;
use yii2lab\console\helpers\Output;

class CmdController extends Controller
{
	
	public function actionCreate()
	{
		Question::confirm(null, 1);
		$files = Test::createCmd();
		prr($files);
	}
	
}
