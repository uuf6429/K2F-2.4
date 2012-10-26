<?php

	class ReNS_Source_Files extends ReNS_Source {
		public function read($name){
			return file_get_contents($name);
		}
		
		public function write($name, $code){
			return file_put_contents($name, $code);
		}
	}

?>