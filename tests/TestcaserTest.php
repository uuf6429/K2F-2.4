<?php

	class Testcaser_ReporterFake extends Testcaser_Reporter {
		protected $_reporter;
		
		public function __construct(TestcaserReporter $realReporter){
			$this->_reporter = $realReporter;
		}
		
		public function report($message, $value, $expectation, $success) {
			$this->_reporter->report('Testing: '.$message, $value, $expectation, $success);
		}
	}
	
	class TestcaserTest extends Testcaser_Testase {
		public function main(){
			
			$rep = new Testcaser_ReporterFake($this->getReporter());
			$tst = new Testcaser_Test($rep);
			
			$tst->assertTrue(true, 'Ensure true is true');
			$tst->assertTrue(1==1, 'Ensure 1 is indeed 1');
			$tst->assertTrue(1==0, 'Check if 1 is also a 0');
			$tst->assertTrue(10/0, 'Divide by zero');
		}
	}

?>