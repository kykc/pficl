<?php

namespace pficl\Cache
{
	class GenericSubject
	{
		protected $data;
		protected $periodStart;
		protected $periodLength;
		protected $fetchedOn;

		/** @return \pficl\Cache\IStorageSubject */
		public static function make($data, $periodStart, $periodLength, $fetchedOn)
		{
			return new self($data, $periodStart, $periodLength, $fetchedOn);
		}

		protected function __construct($data, $periodStart, $periodLength, $fetchedOn)
		{
			$this->data = $data;
			$this->periodStart = $periodStart;
			$this->periodLength = $periodLength;
			$this->fetchedOn = $fetchedOn;
		}

		public function getData()
		{
			return $this->data;
		}

		public function getPeriodStart()
		{
			return $this->periodStart;
		}

		public function getPeriodLength()
		{
			return $this->periodLength;
		}

		public function getFetchedOn()
		{
			return $this->fetchedOn;
		}
	}
}

?>
