<?php
	
	require_once('testcasertest.php');
	require_once('testcaserreporter.php');
	require_once('testcaserreportercli.php');
	require_once('testcaserreporterweb.php');


	class Testcaser {
		
		/**
		 * Runs an assertion test.
		 * @param string $message Description of assertion.
		 * @param mixed $result The real resulting value.
		 * @param mixed $expected The expected value.
		 * @param boolean $identical Whether both values must be identical or not (default is false).
		 */
		/*public function test($message, $result, $expected, $identical=false){
			if($identical){
				$pass = $expected === $result;
			}else{
				$pass = $expected == $result;
			}
			?><tr>
				<td class="cell case">
					<?php echo $this->_highlight_code($message); ?>
				</td><td class="cell">
					<?php echo $this->_highlight_code(var_export($expected, true)); ?>
				</td><td class="cell">
					<?php echo $this->_highlight_code(var_export($result, true)); ?>
				</td><td class="<?php echo $pass ? 'pass' : 'fail'; ?>">
					<?php echo $pass ? 'PASS' : 'FAIL'; ?>
				</td>
			</tr><?php
		}*/
		
		/**
		 * @var TestcaserReporter Reporter instance. 
		 */
		protected $_reporter = null;
		
		/**
		 * Construct a new instance with dependencies.
		 * @param TestcaserReporter $reporter Reporter instance.
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
		
		public function load($files){
			foreach($files as $file){
				try {
					include_once($file);
				}catch(Exception $e){
					$this->_reporter->handle_exception($e);
				}
			}
		}
		
		public function run(){
			$this->_reporter->init();
			$this->_start_listening();
			$ignore = new TestcaserTest(null);
			$ignore = get_class_methods($ignore);
			foreach(get_declared_classes() as $class){
				if(is_subclass_of($class, 'TestcaserTest')){
					$inst = new $class($this->_reporter);
					foreach(get_class_methods($inst) as $mtd){
						if(!in_array($mtd, $ignore))$inst->$mtd();
					}
				}
			}
			$this->_stop_listening();
			$this->_reporter->fini();
			return $this->_reporter->result();
		}
		
	}
	
?>