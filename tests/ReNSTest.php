<?php

	require_once('tools/libs/ReNS/ReNS.php');
	
	class ReNSTest extends Testcaser_Testase {
		public function main(){
			
			$tests = array(
				'/var/www/html/libs/test/simple.php' => array(
					'Testing simple namespace insertion',
					'<?php echo "test"; ?>',
					'<?php namespace K2F\libs\test;  echo "test"; ?>',
				),
			);
			
			$src = new ReNS_Source_Memory();
			
			foreach($tests as $name=>$data){
				$src->write($name, $data[1]);
			}
			
			$rns = new ReNS($src, true, '/var/www/html/', 'K2F/');
			$rns->run();
			
			foreach($tests as $name=>$data){
				$cmp = $data[2] === $src->read($name);
				$this->assertTrue($cmp, $data[0]);
			}
			
		}
	}

?>