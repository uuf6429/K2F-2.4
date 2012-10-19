<?php

	class TestcaserReporterWeb extends TestcaserReporter {
		/**
		 * Highlights a PHP value.
		 * @param string $value Original PHP code/value.
		 * "return string Highlighted PHP code.
		 */
		protected function _highlight_code($value){
			switch(true){
				case is_numeric($value):
					return '<span class="hl-number">'.$value.'</span>';
				case defined($value):
					return '<span class="hl-constant">'.htmlspecialchars($value, ENT_QUOTES).'</span>';
				case (substr($value, 0, 1)=='\'') && (substr($value, -1, 1)=='\''):
					return '<span class="hl-string">'.htmlspecialchars($value, ENT_QUOTES).'</span>';
				case is_resource($value):
					return '<span class="hl-resource">'.(string)$value.' ('.get_resource_type($value).')</span>';
				case is_array($value): // todo
				case is_object($value): // todo
				default:
					return '<span class="hl-none">'.htmlspecialchars($value, ENT_QUOTES).'</span>';
			}
		}
		
		/**
		 * Renders testcaser header.
		 */
		public function init(){
			?><html>
				<head>
					<style type="text/css">
						thead td { font: bold 13px Verdana; background: #222; color: #EEE; padding: 4px; }
						tbody td { font: 11px Verdana; background: #EEE; padding: 2px; border: 1px solid #FFF; }
						tbody tr:hover .cell { background: #FFD; }
						tbody tr:hover .cell:hover { background: #FFB; }
						pre { margin: 0; padding: 0; }
						.case { font-family: Consolas, 'Lucida Console', monospace; }
						.pass { color: #FFF; background: #0A0; text-align: center; }
						.fail { color: #FFF; background: #A00; text-align: center; }
						.error { border: 2px solid #F00; background: #FEE; padding: 2px; font: 11px Consolas; color: #500; }
						.hl-none { color: #444; }
						.hl-number { color: magenta; }
						.hl-constant { color: chocolate; }
						.hl-string { color: red; }
						.hl-resource { color: brown; }
					</style>
				</head>
				<body>
					<table cellpadding="0" cellspacing="0" width="100%">
						<thead>
							<tr style="font-weight: bold;"><td>Test Case</td><td>Expected</td><td>Result</td><td>Test Result</td></tr>
						</thead><tbody><?php
		}
		
		public function report($message, $value, $expectation, $success){
			parent::report($message, $value, $expectation, $success);
			?><tr>
				<td class="cell case">
					<?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
				</td><td class="cell">
					<?php echo $this->_highlight_code($expectation); ?>
				</td><td class="cell">
					<?php echo $this->_highlight_code(var_export($value, true)); ?>
				</td><td class="<?php echo $success ? 'pass' : 'fail'; ?>">
					<?php echo $success ? 'PASS' : 'FAIL'; ?>
				</td>
			</tr><?php
		}
		
		public function fini(){
						?></tbody>
					</table>
				</body>
			</html><?php
		}
		
		public function handle_error($errno, $errstr, $errfile, $errline, $errcontext=array()){
			?><tr>
				<td colspan="10" class="error">
					<b>Error <?php echo $this->_error_name($errno, $errno); ?>:</b> <?php echo $errstr; ?><br/>
					<b>File:</b> <?php echo $this->_shorten_file($errfile); ?> <b>Line:</b> <?php echo $errline; ?>
					<pre><b>Context:</b> <?php echo implode(', ', array_keys($errcontext)); ?></pre>
				</td>
			</tr><?php
			return true;
		}
		
		public function handle_exception(Exception $e){
			?><tr>
				<td colspan="10" class="error">
					<b><?php echo get_class($e).' '.$e->getCode() ?>:</b> <?php echo $e->getMessage(); ?><br/>
					<b>File:</b> <?php echo $this->_shorten_file($e->getFile()); ?> <b>Line:</b> <?php echo $e->getLine(); ?><br/>
					<pre><b>Stack Trace:</b> <?php echo $e->getTraceAsString(); ?></pre>
				</td>
			</tr><?php
		}
	}

?>