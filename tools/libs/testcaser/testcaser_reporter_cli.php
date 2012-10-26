<?php

	class Testcaser_Reporter_Cli extends Testcaser_Reporter {
		
		protected function _color($name='reset'){
			static $colors = array(
				'reset' => "\033[39m",
				'red' => "\033[31m",
				'green' => "\033[32m",
				'yellow' => "\033[33m",
			);
			return isset($colors[$name]) ? $colors[$name] : '';
		}
		
		protected function _write($text, $rightTxt=null, $rightCol=''){
			static $hchars = 76;
			if($rightTxt){
				$text = rtrim($text);
				$len = explode(PHP_EOL, $text);
				foreach($len as $i=>$txt)$len[$i] = implode(PHP_EOL, str_split($txt, $hchars));
				$text = implode(PHP_EOL, $len);
				$len = explode(PHP_EOL, $text);
				$len = strlen($len[count($len) - 1]);
				$text = str_pad($text, strlen($text) - $len + $hchars - strlen($rightTxt), ' ', STR_PAD_RIGHT);
				$text .= '['.$this->_color($rightCol).$rightTxt.$this->_color().']'.PHP_EOL;
			}
			echo $text;
		}
		
		/**
		 * Called when report starts.
		 */
		public function init(){
			$this->_write(
				PHP_EOL.'  Testcaser Report Starting ('.date('r').')'.PHP_EOL.PHP_EOL
			);
		}
		
		/**
		 * Called when report ends.
		 */
		public function fini(){
			$taken = number_format(microtime(true) - $this->_inittime, 6);
			$this->_write(
				PHP_EOL.'  Testcaser Report Finished (in '.$taken.' seconds)'.PHP_EOL.
				'  Testcaser Report Summary: '.($this->_success+$this->_failed).
				' total, '.$this->_success.' passed, '.$this->_failed.' failed.'.PHP_EOL,
				($this->result() ? 'PASS' : 'FAIL'), ($this->result() ? 'green' : 'red')
			);
		}
		
		/**
		 * Write testcase report.
		 * @param string $message A message of what is being tested.
		 * @param mixed $value The actual value that has been tested.
		 * @param string $expectation A description of what was expect.
		 * @param boolean $success Whether assertion was successful or not.
		 */
		public function report($message, $value, $expectation, $success){
			parent::report($message, $value, $expectation, $success);
			$this->_write(
				'   '.$message.': '.(is_object($value) ? '['.get_class($value).']' : var_export($value, true)).
				' is '.$expectation.'? '.PHP_EOL, ($success ? 'YES' : 'NO'), ($success ? 'green' : 'red')
			);
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
		public function handle_error($errno, $errstr, $errfile, $errline, $errcontext=array()){
			$this->_write(
				$this->_color($this->_error_color($errno)).
				'    Error '.$this->_error_name($errno, $errno).': '.$errstr.PHP_EOL.
				'    |- File: '.$this->_shorten_file($errfile).PHP_EOL.
				'    |- Line: '.$errline.PHP_EOL.
				'    \'- Context: '.implode(', ', array_keys($errcontext)).
				$this->_color().PHP_EOL
			);
			return true;
		}
		
		/**
		 * Standard PHP exception handler.
		 * @param Exception $e The exception instance.
		 * @access protected
		 */
		public function handle_exception(Exception $e){
			$this->_write(
				$this->_color('red').
				'    Exception '.get_class($e).' '.$e->getCode().' '.$e->getMessage().PHP_EOL.
				'    |- File: '.$this->_shorten_file($e->getFile()).PHP_EOL.
				'    |- Line: '.$e->getLine().PHP_EOL.
				'    \'- Stack Trace: '.$e->getTraceAsString().
				$this->_color().PHP_EOL
			);
		}
	}

?>