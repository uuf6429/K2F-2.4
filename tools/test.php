<?php

	date_default_timezone_set('Europe/Malta');

	require_once 'libs/testcaser/testcaser.php';
	
	$rep = (defined('STDIN') || PHP_SAPI === 'cli') ? 'Testcaser_Reporter_Cli' : 'Testcaser_Reporter_Web';
	
	$test = new Testcaser(new $rep);
	
	$test->add(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'tests'.DIRECTORY_SEPARATOR, true);
	
	$test->run();
	
?>