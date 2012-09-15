<?php

namespace pficl\Test
{	
	abstract class SimpleTest
	{
		private $log = array();
		private $currentTest = NULL;
		private $summary = array();
		
		abstract public function getTestList();
		
		public static function make()
		{
			$calledClass = get_called_class();
			
			return new $calledClass;
		}
		
		public function run()
		{
			$this->log = array();
			$this->currentTest = NULL;
			$this->summary = array('failureCount' => 0, 'successCount' => 0);
			
			foreach ($this->getTestList() as $testName => $testData)
			{
				$this->performTest($testName, $testData);
			}
			
			return $this;
		}
		
		public function getLog()
		{
			return $this->log;
		}
		
		private function performTest($testName, $testData)
		{
			$this->currentTest = $testName;
			call_user_func(array($this, $testName));
		}
		
		private function __construct()
		{	
		}
		
		protected function assertEquals($a, $b, $allowTypeConverting = FALSE)
		{
			$result = $allowTypeConverting ? $a == $b : $a === $b;
			
			$record = array('result' => $result, 'a' => $a, 'b' => $b, 'aStr' => strval($a), 'bStr' => strval($b), 'time' => date('Y-m-d H:i:s'), 'aType' => gettype($a), 'bType' => gettype($b));
			
			$this->addLog($record);
			
			if ($result)
			{
				$this->summary['successCount']++;
			}
			else
			{
				$this->summary['failureCount']++;
			}
			
			return $this;
		}
		
		protected function addLog($record)
		{
			$this->log[$this->getCurrentTest()][] = $record;
		}
		
		public function makeCliTrace()
		{
			$trace = '';
			$ident = '';
			$trace .= 'LAUNCHING TESTUNIT ['.get_class($this).']'.PHP_EOL;
			$ident = '    ';
			
			foreach ($this->getLog() as $testName => $testData)
			{
				$trace .= $ident.'PERFORMING TEST ['.$testName.']'.PHP_EOL;
				
				foreach ($testData as $assertionData)
				{
					$ident = '        ';
					$msg = $assertionData['result'] ? 'ASSERTION OK' : 'ASSERTION FAILED';
					$details = '['.$assertionData['aStr'].']:{'.$assertionData['aType'].'} ['.$assertionData['bStr'].']:{'.$assertionData['bType'].'}';
					$trace .= $ident.$msg.' '.$details.PHP_EOL;
				}
				
				$ident = '    ';
			}
			
			$ident = '';
			$trace .= 'SUMMARY -> FAILED: '.$this->summary['failureCount'].', SUCCEEDED: '.$this->summary['successCount'].PHP_EOL;
			
			return $trace;
		}
		
		public function getCurrentTest()
		{
			return $this->currentTest;
		}
	}
}

?>