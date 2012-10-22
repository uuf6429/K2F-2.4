<?php

	class Testcaser_Reporter {
		/**
		 * @var integer Timestamp of when test started.
		 */
		protected $_inittime = null;
		
		/**
		 * @var integer Number of successfull tests.
		 */
		protected $_success = 0;
		
		/**
		 * @var integer Number of failed tests.
		 */
		protected $_failed = 0;
		
		/**
		 * Shortens file name to document root.
		 * @param string $orig Original path.
		 * @return string Converted path.
		 */
		protected function _shorten_file($orig){
			return str_replace(realpath($_SERVER['DOCUMENT_ROOT']), '$DOCUMENT_ROOT', $orig);
		}
		
		/**
		 * Returns error code constant given its value.
		 * @param integer $code Error code.
		 * @param mixed $default Value returned when error constant cannot be found.
		 * @return string Error constant name or $default if not found.
		 * @todo Error code might be a bitmask of codes excluded from E_ALL, we may need to handle this too.
		 */
		protected function _error_name($code, $default=null){
			foreach(get_defined_constants() as $const=>$value)
				if(substr($const, 0, 2)=='E_' && $code===$value)
					return $const;
			return $default;
		}
		
		/**
		 * Returns color name according to error severity.
		 * @param integer $code Error code.
		 * @return string Color value from 'red', 'yellow', 'blue' and 'magenta'.
		 */
		protected function _error_color($code){
			static $colors = array(
				'red' => array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR),
				'yellow' => array(E_WARNING, E_CORE_WARNING, E_COMPILE_WARNING, E_USER_WARNING),
				'blue' => array(E_NOTICE, E_USER_NOTICE, E_STRICT, E_DEPRECATED, E_USER_DEPRECATED),
			);
			foreach($colors as $color=>$codes)
				if(in_array($code, $codes))
					return $color;
			return 'magenta';
		}
		
		public function __construct(){
			$this->_inittime = microtime(true);
		}
		
		/**
		 * Called when report starts.
		 */
		public function init(){}
		
		/**
		 * Called when report ends.
		 */
		public function fini(){}
		
		/**
		 * Write testcase report.
		 * @param string $message A message of what is being tested.
		 * @param mixed $value The actual value that has been tested.
		 * @param string $expectation A description of what was expect.
		 * @param boolean $success Whether assertion was successful or not.
		 */
		public function report($message, $value, $expectation, $success){
			if($success){
				$this->_success++;
			}else{
				$this->_failed++;
			}
		}
		
		/**
		 * Standard PHP error handler.
		 * @param integer $errno PHP error code.
		 * @param string $errstr Error message.
		 * @param string $errfile File where error was caused.
		 * @param integer $errline Line causing error.
		 * @param array $errcontext Array of variables that were available when error happened.
		 * @return boolean Whether error was handled or not.
		 * @access protected
		 */
		public function handle_error($errno, $errstr, $errfile, $errline, $errcontext=array()){}
		
		/**
		 * Standard PHP exception handler.
		 * @param Exception $e The exception instance.
		 * @access protected
		 */
		public function handle_exception(Exception $e){}
		
		/**
		 * @return boolean Whether tests have been successful or not.
		 */
		public function result(){
			return !$this->_failed;
		}
	}

?>