<?php

	date_default_timezone_set('Europe/Malta');

	require_once 'libs/testcaser/testcaser.php';
	
	$rep = (defined('STDIN') || PHP_SAPI === 'cli') ? 'TestcaserReporterCli' : 'TestcaserReporterWeb';
	
	$test = new Testcaser(new $rep);
	
	$test->load(glob(dirname(__FILE__).DIRECTORY_SEPARATOR.'*.php'));
	
	$test->run();
	
?>