<?php
	
	class TestEnvironment extends TestcaserTest {
		public function test_php(){
			
			$v = explode('.', phpversion().'..');
			$v = ($v[0] * 10000) + ($v[1] * 100) + ($v[2] * 1);
			$this->assertGreaterThanOrEqual(50300, $v, 'Check PHP version at least 5.3');
			
			$this->assertTrue(function_exists('gd_info'), 'Check GD functionality');
			
		}
	}
	
?>