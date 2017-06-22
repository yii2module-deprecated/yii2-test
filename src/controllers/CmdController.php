<?php

namespace yii2lab\test\controllers;

use Yii;
use woop\extension\console\yii\console\Controller;
use yii2lab\test\helpers\Test;
use woop\extension\console\helpers\input\Question;
use woop\extension\console\helpers\Output;

class CmdController extends Controller
{
	
	public function actionCreate()
	{
		Question::confirm(null, 1);
		$files = Test::createCmd();
		prr($files);
	}
	
}
