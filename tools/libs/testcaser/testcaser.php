<?php
	
	require_once('testcaser_testcase.php');
	require_once('testcaser_reporter.php');
	require_once('testcaser_reporter_cli.php');
	require_once('testcaser_reporter_web.php');


	class Testcaser {
		
		/**
		 * @var Testcaser_Reporter Reporter instance. 
		 */
		protected $_reporter = null;
		
		/**
		 * @var array List of files to test.
		 */
		protected $_files = array();
		
		/**
		 * Construct a new instance with dependencies.
		 * @param Testcaser_Reporter $reporter Reporter instance.
		 */
		public function __construct($reporter){
			$this->_reporter = $reporter;
		}
		
		/**
		 * Begin listening for errors and exceptions.
		 */
		protected function _start_listening(){
			set_error_handler(array($this->_reporter, 'handle_error'));
			set_exception_handler(array($this->_reporter, 'handle_exception'));
			error_reporting(-1);
			ini_set('display_errors', false);
		}
		
		/**
		 * Stop listening for errors and exceptions.
		 */
		protected function _stop_listening(){
			restore_error_handler();
			restore_exception_handler();
			error_reporting();
			ini_set('display_errors', true);
		}
		
		/**
		 * Add a path for loading tests.
		 * @param string $path Either a folder or a specific PHP file.
		 * @param boolean $recursive If $path is a folder and this option is true, each subfolder is also loaded.
		 */
		public function add($path, $recursive=true){
			if(is_dir($path)){
				if(substr($path, -1, 1) != DIRECTORY_SEPARATOR){
					$path.=DIRECTORY_SEPARATOR;
				}
				foreach(glob($path.'*') as $item){
					if($recursive || is_file($item)){
						$this->add($item, $recursive);
					}
				}
			}elseif(is_file($path)){
				$this->_files[] = realpath($path);
			}else{
				throw new InvalidArgumentException('The path "'.$path.'" is not accessible');
			}
		}
		
		/**
		 * Runs tests over all loaded files.
		 * @return boolean True if all tests were successful, false otherwise.
		 */
		public function run(){
			// initialize
			$this->_reporter->init();
			$this->_start_listening();
			// load
			foreach($this->_files as $file){
				try {
					include_once($file);
				}catch(Exception $e){
					$this->_reporter->handle_exception($e);
				}
			}
			$ignore = new TestcaserTest(null);
			$ignore = get_class_methods($ignore);
			// run
			foreach(get_declared_classes() as $class){
				if(is_subclass_of($class, 'Testcaser_Testase')){
					$inst = new $class($this->_reporter);
					foreach(get_class_methods($inst) as $mtd){
						if(!in_array($mtd, $ignore))$inst->$mtd();
					}
				}
			}
			// finalize
			$this->_stop_listening();
			$this->_reporter->fini();
			return $this->_reporter->result();
		}
		
		/**
		 * Sets the reporter instance.
		 * @param Testcaser_Reporter $reporter Reporter instance.
		 */
		public function set_reporter($reporter){
			$this->_reporter = $reporter;
		}
		
		/**
		 * Gets the reporter instance.
		 * @return Testcaser_Reporter Reporter instance.
		 */
		public function get_reporter(){
			return $this->_reporter;
		}
		
	}
	
?>