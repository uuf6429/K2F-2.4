<?php

	class Testcaser_Testase {
		
		/**
		 * @var Testcaser_Reporter Reporter instance.
		 */
		protected $_reporter = null;
		
		/**
		 * Generates a message out of a debug backtrace.
		 * @param array $trace The result of a call to debug_backtrace() in callee.
		 */
		protected function _message($trace){
			if(isset($trace[1])){
				return $trace[1]['class'].$trace[1]['type'].$trace[1]['function'].'()';
			}else{
				return get_class($this).'->???()';
			}
		}
		
		/**
		 * Return string representation of a value.
		 * @param mixed $value The original value.
		 * @return string The final value.
		 */
		protected function _export($value){
			return var_export($value, true);
		}
		
		/**
		 * Construct a new testcase.
		 * @param Testcaser_Reporter $reporter The reporter to use.
		 */
		public function __construct($reporter){
			$this->_reporter = $reporter;
		}
		
		/**
		 * @return Testcaser_Reporter The reporter currently in use.
		 */
		public function getReporter(){
			return $this->_reporter;
		}
		
		public function assertArrayHasKey($key, $array, $message=null){
			if(!$message)$message = $this->_message(debug_backtrace());
			$this->_reporter->report($message, $array, 'contains key '.$this->_export($key), isset($array[$key]));
		}
		public function assertArrayContains($value, $array, $message=null){
			if(!$message)$message = $this->_message(debug_backtrace());
			$this->_reporter->report($message, $array, 'contains value '.$this->_export($value), in_array($value, $array));
		}
		
		public function assertClassHasAttribute($attributeName, $className, $message=null){
			if(!$message)$message = $this->_message(debug_backtrace());
			$cmp = in_array($attributeName, get_class_vars($className)) && !isset($className::$$attributeName);
			$this->_reporter->report($message, $className.'->'.$attributeName, 'property "'.$attributeName.'"', $cmp);
		}
		
		public function assertClassHasStaticAttribute($attributeName, $className, $message=null){
			if(!$message)$message = $this->_message(debug_backtrace());
			$cmp = isset($className::$$attributeName);
			$this->_reporter->report($message, $className.'::'.$attributeName, 'static property "'.$attributeName.'"', $cmp);
		}
		
		public function assertCount($expectedCount, $haystack, $message=null){
			if(!$message)$message = $this->_message(debug_backtrace());
			$cmp = count($haystack) == $expectedCount;
			$this->_reporter->report($message, $haystack, 'contains '.(int)$expectedCount.' item(s)', $cmp);
		}
		
		public function assertEmpty($actual, $message=null){
			if(!$message)$message = $this->_message(debug_backtrace());
			$this->_reporter->report($message, $actual, 'empty', empty($actual));
		}
		
		public function assertEquals($expected, $actual, $message=null){
			if(!$message)$message = $this->_message(debug_backtrace());
			$cmp = $actual == $expected;
			$this->_reporter->report($message, $actual, 'equals '.$this->_export($expected), $cmp);
		}
		
		public function assertFalse($condition, $message=null){
			if(!$message)$message = $this->_message(debug_backtrace());
			$this->_reporter->report($message, $condition, 'false', !$condition);
		}
		
		public function assertFileExists($filename, $message=null){
			if(!$message)$message = $this->_message(debug_backtrace());
			$this->_reporter->report($message, $filename, 'existance of file', file_exists($filename));
		}
		
		public function assertGreaterThan($expected, $actual, $message=null){
			if(!$message)$message = $this->_message(debug_backtrace());
			$this->_reporter->report($message, $actual, 'larger than '.(float)$expected, $actual > $expected);
		}
		
		public function assertGreaterThanOrEqual($expected, $actual, $message=null){
			if(!$message)$message = $this->_message(debug_backtrace());
			$this->_reporter->report($message, $actual, 'larger or equal to '.(float)$expected, $actual >= $expected);
		}
		
		public function assertInstanceOf($expected, $actual, $message=null){
			if(!$message)$message = $this->_message(debug_backtrace());
			$this->_reporter->report($message, $actual, 'instance of '.$expected, is_a($actual, $expected));
		}
		
		public function assertInternalType($expected, $actual, $message=null){
			if(!$message)$message = $this->_message(debug_backtrace());
			static $cmps = array(
				'array' => 'is_array',
				'bool' => 'is_bool',
				'boolean' => 'is_bool',
				'callable' => 'is_callable',
				'double' => 'is_float',
				'float' => 'is_float',
				'int' => 'is_int',
				'integer' => 'is_int',
				'nan' => 'is_nan',
				'null' => 'is_null',
				'numeric' => 'is_numeric',
				'obj' => 'is_object',
				'object' => 'is_object',
				'resouce' => 'is_resouce',
				'scalar' => 'is_scalar',
				'str' => 'is_string',
				'string' => 'is_string',
			);
			$cmp = isset($cmps[strtolower($expected)]) ? $cmps[strtolower($expected)] : null;
			if(!$cmp)throw new InvalidArgumentException('Passed data type "'.$expected.'" is not valid/supported');
			$this->_reporter->report($message, $actual, $expected.' type', $cmp($actual));
		}
		
		public function assertLessThan($expected, $actual, $message=null){
			if(!$message)$message = $this->_message(debug_backtrace());
			$this->_reporter->report($message, $actual, 'larger than '.(float)$expected, $actual < $expected);
		}
		
		public function assertLessThanOrEqual($expected, $actual, $message=null){
			if(!$message)$message = $this->_message(debug_backtrace());
			$this->_reporter->report($message, $actual, 'larger or equal to '.(float)$expected, $actual <= $expected);
		}
		
		public function assertNull($variable, $message=null){
			if(!$message)$message = $this->_message(debug_backtrace());
			$this->_reporter->report($message, $variable, 'null', is_null($variable));
		}
		
		public function assertObjectHasAttribute($attributeName, $object, $message=null){
			if(!$message)$message = $this->_message(debug_backtrace());
			$this->_reporter->report($message, $object, 'property "'.$attributeName.'"', isset($object->$attributeName));
		}
		
		public function assertSame($expected, $actual, $message=null){
			if(!$message)$message = $this->_message(debug_backtrace());
			$cmp = $actual === $expected;
			$this->_reporter->report($message, $actual, 'identical to '.$this->_export($expected), $cmp);
		}
		
		public function assertStringEndsWith($suffix, $string, $message=null){
			if(!$message)$message = $this->_message(debug_backtrace());
			$cmp = substr($string, -strlen($suffix)) === $suffix;
			$this->_reporter->report($message, $string, 'ends with "'.$string.'"', $cmp);
		}
		
		public function assertStringStartsWith($prefix, $string, $message=null){
			if(!$message)$message = $this->_message(debug_backtrace());
			$cmp = substr($string, 0, strlen($prefix)) === $prefix;
			$this->_reporter->report($message, $string, 'starts with "'.$prefix.'"', $cmp);
		}
		
		public function assertTrue($condition, $message=null){
			if(!$message)$message = $this->_message(debug_backtrace());
			$this->_reporter->report($message, $condition, 'true', !!$condition);
		}
		
	}

?>