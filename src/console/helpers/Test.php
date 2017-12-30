<?php

namespace yii2module\test\console\helpers;

use Yii;
use yii2lab\helpers\yii\FileHelper;
use yii2lab\helpers\Helper;
use yii\helpers\ArrayHelper;

class Test
{

	static $typeList = [
		'functional',
		'unit',
		'acceptance',
	];
	
	static function createCmd()
	{
		$apps = self::getAppDirs();
		$result = [];
		foreach ($apps as $app) {
			$tests = self::getAppTests($app);
			if (!empty($tests)) {
				$result[$app] = $tests;
			}
		}
		return $result;
	}
	
	private function getAppDirs()
	{
		$options['only'][] = 'codeception.yml';
		$options['except'][] = 'vendor/';
		$options['recursive'] = true;
		$pathList = FileHelper::findFiles(ROOT_DIR, $options);
		ArrayHelper::removeValue($pathList, ROOT_DIR . DS . 'codeception.yml');
		foreach($pathList as &$path) {
			$path = substr($path, strlen(ROOT_DIR) + 1);
			$path = substr($path, 0, strlen($path) - strlen('codeception.yml') - 1);
		}
		return $pathList;
	}
	
	private function isExistsConfig($app, $type)
	{
		$suite = $app . DS . 'tests' . DS . $type . '.suite.yml';
		return file_exists(ROOT_DIR . DS . $suite);
	}

	private function extractName($testName)
	{
		$testName = str_replace('.php', '', $testName);
		$testName = str_replace('Cest', '', $testName);
		$testName = str_replace('Test', '', $testName);
		return $testName;
	}

	private function stripFileName($file)
	{
		$file = str_replace('.php', '', $file);
		$file = FileHelper::normalizePath($file);
		$file = preg_replace('#[\w]+' . preg_quote(DS) . 'tests' . preg_quote(DS) . '#i', '', $file);
		return $file;
	}

	private function getPathInfo($fileName)
	{
		$fileNameArr = explode(DS, $fileName);
		$result['type'] = array_splice($fileNameArr, 0, 1)[0];
		$result['relativePath'] = implode(DS, $fileNameArr);
		$result['countSegment'] = count($fileNameArr);
		return $result;
	}
	
	private function createTestFile($app, $fileName)
	{
		$shotFileName = self::stripFileName($fileName);
		$info = self::getPathInfo($shotFileName);
		$isExistsConfig = self::isExistsConfig($app, $info['type']);
		$isType = in_array($info['type'], self::$typeList);
		if(!$isExistsConfig || !$isType) {
			return false;
		}
		$count = 3 + $info['countSegment'];
		$code =
			"cd " . str_repeat('..\\', $count) . $app .
			PHP_EOL .
			"codecept run " . $info['type'] . ' ' . $info['relativePath'];
		
		$cmdName = self::extractName($shotFileName);
		$fullFileName = $app . DS . 'tests' . DS . 'cmd'. DS . "$cmdName.bat";
		FileHelper::save($fullFileName, $code);
		return $cmdName;
	}
	
	private function createMainTestFile($app, $type)
	{
		$code =
			"cd " . str_repeat('..\\', 3) . $app .
			PHP_EOL .
			"codecept run " . $type . ' ' . $info['relativePath'];
		$fullFileName = $app . DS . 'tests' . DS . 'cmd'. DS . "$type.bat";
		FileHelper::save($fullFileName, $code);
		return $cmdName;
	}
	
	private function findTests($appDir)
	{
		$options['only'][] = '*Test.php';
		$options['only'][] = '*Cest.php';
		$options['recursive'] = true;
		return FileHelper::findFiles($appDir, $options);
	}

	private function removeDirectory($appDir)
	{
		$testDir = ROOT_DIR . DS . $appDir . DS . 'cmd';
		return FileHelper::removeDirectory($testDir);
	}
	
	private static function getAppTests($app)
	{
		$appDir = $app . DS . 'tests';
		$fileList = self::findTests($appDir);
		self::removeDirectory($appDir);
		
		$result = [];
		foreach ($fileList as $fileName) {
			$shotFileName = self::createTestFile($app, $fileName);
			if($shotFileName) {
				$result[] = $shotFileName;
			}
		}
		
		if(!empty($result)) {
			foreach (self::$typeList as $type) {
				$isExistsConfig = self::isExistsConfig($app, $type);
				if($isExistsConfig) {
					self::createMainTestFile($app, $type);
				}
			}
		}
		return $result;
	}

}
