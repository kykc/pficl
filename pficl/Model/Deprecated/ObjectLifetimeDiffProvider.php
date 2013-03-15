<?php

namespace pficl\Model\Deprecated
{
	use \pficl\Fp\Util as Fp;

	class ObjectLifetimeDiffProvider
	{
		protected $subject;
		protected $initialSnapshot;

		protected function __construct()
		{
		}

		public function __destruct()
		{
			//unset($this->subject);
			//unset($this->initialSnapshot);
		}

		public static function make(IObjectLifetimeDiffSubject $object)
		{
			$obj = new self;
			$obj->subject = $object;
			$obj->initialSnapshot = $obj->getSubject()->getObjectState(TRUE);
			$obj->forceValidSnapshot($obj->getInitialSnapshot());

			return $obj;
		}

		/** @return IObjectLifetimeDiffSubject */
		protected function getSubject()
		{
			return $this->subject;
		}

		public function getDiff($force = array())
		{
			$currentSnapshot = $this->getSubject()->getObjectState(TRUE);
			$initialSnapshot = $this->getInitialSnapshot();

			$this->forceValidSnapshot($currentSnapshot);
			$diff = array();

			if (count(array_diff(array_keys($initialSnapshot), array_keys($currentSnapshot))))
			{
				throw new ObjectLifetimeDiffProviderSnapshotFormatMismatchException();
			}
			else
			{
				$fieldCollection = array_keys($initialSnapshot);

				foreach ($fieldCollection as $fieldName)
				{
					if ($initialSnapshot[$fieldName] !== $currentSnapshot[$fieldName] || in_array($fieldName, $force))
					{
						$diff[$fieldName] = array('was' => $initialSnapshot[$fieldName], 'now' => $currentSnapshot[$fieldName]);
					}
				}

				return $diff;
			}
		}

		protected function getInitialSnapshot()
		{
			return $this->initialSnapshot;
		}

		protected function validateSnapshot(array $snapshot)
		{
			$value = Fp::all('is_string', array_keys($snapshot));

			return $value;
		}

		protected function forceValidSnapshot(array $snapshot)
		{
			if (!$this->validateSnapshot($snapshot))
			{
				throw new ObjectLifetimeDiffProviderCorruptedSnapshotException();
			}
		}
	}
}

?>
