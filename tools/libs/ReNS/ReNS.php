<?php
	
	require_once('ReNS_Result.php');
	require_once('ReNS_Source.php');
	require_once('ReNS_Source_Files.php');
	require_once('ReNS_Source_Memory.php');
	require_once('ReNS_Exception.php');
	
	class ReNS {
		/**
		 * @var ReNS_Source
		 */
		protected $_source = null;
		
		/**
		 * If true, warnings are hidden and conversion deemed successfull, if
		 * otherwise set to false, warnings are shown and conversion fails.
		 * @var boolean
		 */
		public $ignoreWarnings = false;
		
		/**
		 * The old path to replace.
		 * @var string
		 */
		protected $_base_old = '';
		
		/**
		 * The new path to use.
		 * @var string
		 */
		protected $_base_new = '';
		
		/**
		 * Construct new instance.
		 * @param ReNS_Source $source The data source.
		 * @param boolean Whether to ignore warnings or not.
		 * @param string $oldBase The old base path to replace.
		 * @param string $newBase The new path to use.
		 */
		public function __construct($source=null, $ignoreWarnings=false, $oldBase='', $newBase=''){
			$this->_source = $source ? $source : new ReNS_Source;
			$this->ignoreWarnings = $ignoreWarnings;
			$this->_base_old = $oldBase;
			$this->_base_new = $newBase;
		}
		
		/**
		 * Run the converter.
		 */
		public function run(){
			$this->_source->each(array($this, '_process'));
		}
		
		/**
		 * Converts namespaces in PHP code.
		 * @param string $name Data source name.
		 * @param string $code Code source.
		 * @ignore
		 */
		public function _process($name, $code){
			if(substr($code, 0, 5)=='<?php'){
				if(!$this->ignoreWarnings){
					if(strpos($code, 'namespace ') !== false)
						throw new ReNS_Exception('PHP code seems to rely on namespace switching');
					if(strpos($code, '__NAMESPACE__') !== false)
						throw new ReNS_Exception('PHP code seems to make use of __NAMESPACE__ constant');
					if(strpos($code, 'eval') !== false)
						throw new ReNS_Exception('PHP code seems to contain calls to eval()');
					if(strpos($code, 'call_user_func_array') !== false)
						throw new ReNS_Exception('PHP code seems to contain calls to call_user_func_array()');
					if(strpos($code, 'call_user_func') !== false)
						throw new ReNS_Exception('PHP code seems to contain calls to call_user_func()');
				}
				$code = substr_replace($code, '<?php namespace '.$this->_source->nsname($name, $this->_base_old, $this->_base_new).'; ', 0, 5);
			}else{
				throw new ReNS_Exception('PHP tag not at the beginning of file');
			}
			$this->_source->write($name, $code);
		}
	}
	
?>