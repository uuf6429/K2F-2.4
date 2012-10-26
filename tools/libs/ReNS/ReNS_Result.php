<?php
	
	class ReNS_Result {
		public $code = '';
		public $status = self::ST_SUCCESS;
		public $notes = array();
		public function __construct($code='', $status=self::ST_SUCCESS, $notes=array()){
			$this->code = $code;
			$this->status = $status;
			$this->notes = $notes;
		}
	}
	
?>