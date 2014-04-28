<?php

namespace pficl\Cache
{
	interface IStorage
	{
		/** @return \pficl\Cache\IStorageSubject */
		public function load($key);
		public function save($key, $data, $periodStart, $periodLength);
		public function update($key, $data);
		public function calculateOrUseCache($key, \Closure $dataProvider, $periodLength);
	}

	interface IStorageSubject
	{
		public function getData();
		public function getPeriodStart();
		public function getPeriodLength();
		public function getFetchedOn();
	}

	abstract class Util
	{

	}
}

?>
