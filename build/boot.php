<?php

	// Precondition and Environment check

	if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50300) {
		trigger_error('K2F requires at least PHP 5.3 to run.', E_USER_ERROR);
	}
	
	if (defined('K2F')) {
		trigger_error('K2F has already been loaded.', E_USER_ERROR);
	}
	
	define('K2F', 2.4);
	
	// Load Main Configuration
	
	
	// Load Framework Classes
	
	
	// Initialize Main Objects and Run

?>