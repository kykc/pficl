<?php

namespace pficl\Cache
{
	class GenericCache
	{
		protected $storage;
		protected $timeOut;
		protected $key;

		public static function make(IStorage $st, $key, $timeOut)
		{
			return new self($st, $key, $timeOut);
		}

		private function __construct(IStorage $st, $key, $timeOut)
		{
			$this->storage = $st;
			$this->timeOut = $timeOut;
			$this->key = $key;
		}

		/** @return \pficl\Cache\IStorage */
		protected function getStorage()
		{
			return $this->storage;
		}

		public function getTimeout()
		{
			return $this->timeOut;
		}

		public function getKey()
		{
			return $this->key;
		}

		public function getCachedOrRecalc(\Closure $callable)
		{
			$data = $this->getStorage()->load($this->getKey());

			$fetchedOn = strtotime($data->getFetchedOn());
			$expiresOn = strtotime($data->getPeriodStart()) + $data->getPeriodLength();

			if ($data->getFetchedOn() === NULL || $fetchedOn > $expiresOn)
			{
				$calculated = $callable();
				$storedOn = date('Y-m-d H:i:s');

				$this->getStorage()->save($this->getKey(), $calculated, $storedOn, $this->getTimeout());

				return $calculated;
			}
			else
			{
				return $data->getData();
			}
		}

		public function update(\Closure $callable)
		{
			$data = $callable();

			$this->getStorage()->update($this->getKey(), $data);

			return $data;
		}
	}
}

?>
