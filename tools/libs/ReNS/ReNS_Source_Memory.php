<?php

	class ReNS_Source_Memory extends ReNS_Source {
		public $data = array();
		
		public function __construct($data=array()){
			$this->data = $data;
		}
		
		public function read($name){
			return isset($this->data[$name]) ? $this->data[$name] : false;
		}
		
		public function write($name, $code){
			$this->data[$name] = $code;
		}
		
		public function each($callback){
			foreach($this->data as $name=>$data)
				if(call_user_func($callback, $name, $data)===false)
					break;
		}
	}

?>