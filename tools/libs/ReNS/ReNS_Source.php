<?php

	class ReNS_Source {
		protected $_names = array();
		
		/**
		 * Construct new instance.
		 * @param array $names A list of source names to read through.
		 */
		public function __construct($names = array()){
			$this->_names = $names;
		}
		
		/**
		 * Read data from source name.
		 * @param string $name Source name.
		 * @return string|false The data or false on failure.
		 */
		public function read($name){}
		
		/**
		 * Write data to source name.
		 * @param type $name Source name.
		 * @param type $code Data to write.
		 * @return boolean True on success, false on failure.
		 */
		public function write($name, $code){}
		
		/**
		 * Calls callback for each name source passing source name and data as parameters.
		 * @param callable $callback The callback to call for each source name. Note that if it returns false, looping stops.
		 */
		public function each($callback){
			foreach($this->_names as $name)
				if(call_user_func($callback, $name, $this->read($name))===false)
					break;
		}
		
		/**
		 * Create namespace out of data source name.
		 * @param string $name Data source name.
		 * @param string $old Old base path.
		 * @param string $new New base path.
		 * @return string Generated namespace.
		 */
		public function nsname($name, $old, $new){
			if($old)$name = implode($new, explode($old, dirname($name), 2));
			$name = trim(str_replace(array('/', '\\'), '\\', $name), '\\');
			return $name;
		}
	}

?>