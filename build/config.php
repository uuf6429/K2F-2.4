<?php

	$cfg = array(
		'DIR_ROOT' => dirname(__FILE__).DIRECTORY_SEPARATOR
	);
	
	$cfg['DIR_CORE'] = $cfg['DIR_ROOT'].'core'.DIRECTORY_SEPARATOR;
	$cfg['DIR_LIBS'] = $cfg['DIR_ROOT'].'libs'.DIRECTORY_SEPARATOR;
	$cfg['DIR_APPS'] = $cfg['DIR_ROOT'].'apps'.DIRECTORY_SEPARATOR;

	return $cfg;

?>