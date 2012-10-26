<?php

	class Testcaser_ReporterFake extends Testcaser_Reporter {
		/**
		 * @var TestcaserReporter
		 */
		protected $_reporter;
		
		public function __construct($realReporter){
			$this->_reporter = $realReporter;
		}
		
		public function report($message, $value, $expectation, $success) {
			$this->_reporter->report('Testing: '.$message, $value, $expectation, $success);
		}
	}
	
	class TestcaserTest extends Testcaser_Testase {
		public function main(){
			
			$rep = new Testcaser_ReporterFake($this->getReporter());
			$tst = new Testcaser_Testase($rep);
			
			$tst->assertTrue(true, 'Run a fake assertion');
		}
	}

?>