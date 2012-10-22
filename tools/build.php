<?php

	define('DIR_BASE', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR);
	define('DIR_SOURCE', DIR_BASE.'source'.DIRECTORY_SEPARATOR);
	define('DIR_BUILD', DIR_BASE.'build'.DIRECTORY_SEPARATOR);
	
	define('ST_NOTHING', 0);
	define('ST_FAILURE', 1);
	define('ST_WARNING', 2);
	define('ST_SUCCESS', 3);
	
	$GLOBALS['result'] = ST_SUCCESS;
	
	function get_color($status){
		static $colors = array(
			ST_NOTHING => "\033[39m",
			ST_FAILURE => "\033[31m",
			ST_WARNING => "\033[33m",
			ST_SUCCESS => "\033[32m",
		);
		return isset($colors[$status]) ? $colors[$status] : '';
	}
	
	function write_color($status){
		echo get_color($status);
	}
	
	function write_message($message, $status=ST_NOTHING){
		static $states = array(
			ST_FAILURE => 'FAIL',
			ST_WARNING => 'WARN',
			ST_SUCCESS => 'DONE',
		);
		static $hchars = 76;
		if($status){
			$message = rtrim($message);
			$len = explode(PHP_EOL, $message);
			$len = strlen($len[count($len) - 1]);
			$message = str_pad($message, strlen($message) - $len + $hchars - strlen($states[$status]), ' ', STR_PAD_RIGHT);
			$message .= '['.get_color($status).$states[$status].get_color(ST_NOTHING).']';
		}
		echo $message.PHP_EOL;
	}
	
	function convert_filens($file){
		$file = trim(str_replace(DIR_SOURCE, '', $file), DIRECTORY_SEPARATOR);
		$file = explode(DIRECTORY_SEPARATOR, $file);
		return '<?php namespace \\K2F\\'.$file[0].'\\'.$file[1].';';
	}
	
	function convert_code($code, $file){
		if(substr($code, 0, 5)=='<?php'){
			if(strpos($code, 'namespace ') !== false)
				return array($code, ST_WARNING, 'PHP code seems to rely on namespace switching');
			if(strpos($code, '__NAMESPACE__') !== false)
				return array($code, ST_WARNING, 'The code seems to make use of __NAMESPACE__ constant');
			$code = substr_replace($code, convert_filens($file), 0, 5);
		}else{
			return array(
				'code' => $code,
				'stts' => ST_FAILURE,
				'emsg' => 'PHP tag not at the beginning of file',
			);
		}
		return array(
			'code' => $code,
			'stts' => ST_SUCCESS,
			'emsg' => '',
		);
	}
	
	function convert_file($file){
		static $is_php = array('php', 'php5', 'phtml', 'phps');
		$target = str_replace(DIR_SOURCE, DIR_BUILD, $file);
		if(!file_exists($dir = dirname($target)))mkdir($dir, 0755, true);
		list($code, $stts, $emsg) = array_values(convert_code(file_get_contents($file), $file));
		if(in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $is_php)){
			file_put_contents($target, $code);
			if($stts != ST_SUCCESS)throw new Exception($emsg, $stts==ST_FAILURE ? E_USER_ERROR : E_USER_WARNING);
		}else{
			copy($file, $target);
		}
	}
	
	function convert_dir($path){
		static $warnings = array(); // array of warning error codes
		if(substr($path, -1, 1) != DIRECTORY_SEPARATOR){
			$path.=DIRECTORY_SEPARATOR;
		}
		foreach(glob($path.'*') as $item){
			if(is_file($item)){
				try {
					convert_file($item);
					write_message('   Converting '.str_replace(DIR_SOURCE, '', $item).'...', ST_SUCCESS);
				}catch(Exception $e){
					$type = isset($warnings[$e->getCode()]) ? ST_WARNING : ST_FAILURE;
					$GLOBALS['result'] = ($type==ST_WARNING && $GLOBALS['success']!=ST_WARNING) ? ST_WARNING : ST_FAILURE;
					write_message('   Converting '.str_replace(DIR_SOURCE, '', $item).'...', $type);
					write_color($type);
					write_message('     '.get_class($e).' ('.$e->getCode().'): '.$e->getMessage());
					write_message('     '.str_replace(DIR_BASE, '', $e->getFile()).':'.$e->getLine());
					write_color(ST_NOTHING);
				}
			}elseif(is_dir($item)){
				convert_dir($item);
			}else{
				trigger_error('     Unknown filesystem item: "'.str_replace(DIR_SOURCE, '', $item).'"', E_USER_WARNING);
			}
		}
	}
	
	function handle_errors($errno, $errstr='', $errfile='unknown', $errline=0){
		static $warnings = array(E_USER_DEPRECATED, E_USER_NOTICE, E_USER_WARNING, E_WARNING, E_NOTICE); // array of warning error codes
		$type = ST_FAILURE;
		$type = in_array($errno, $warnings) ? ST_WARNING : ST_FAILURE;
		write_color($type);
		write_message('     Error ('.$errno.'): '.$errstr);
		write_message('     '.str_replace(DIR_BASE, '', $errfile).':'.$errline);
		write_color(ST_NOTHING);
		return true;
	}
	ini_set('display_errors', false);
	set_error_handler('handle_errors');
	
	$taken = microtime(true);
	write_message(PHP_EOL.'  Build Starting ('.date('r').')'.PHP_EOL);
	convert_dir(DIR_SOURCE.'core');
	convert_dir(DIR_SOURCE.'libs');
	convert_dir(DIR_SOURCE.'apps');
	foreach(array('boot.php', 'config.php', '.htaccess') as $file){
		$res = copy(DIR_SOURCE.$file, DIR_BUILD.$file);
		write_message('   Copying '.str_replace(DIR_BASE, '', DIR_SOURCE.$file).'...', $res ? ST_SUCCESS : ST_FAILURE);
	}
	$taken = number_format(microtime(true) - $taken, 6);
	write_message(PHP_EOL.'  Build Finished (in '.$taken.' seconds)'.PHP_EOL, $GLOBALS['result']);

?>