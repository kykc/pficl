<?php

namespace pficl\Model\Deprecated
{
	final class EntityManager
	{
		private $storage = array();
		private static $inst = NULL;

		/** @return \pficl\Model\Deprecated\EntityManager */
		public static function inst()
		{
			if (self::$inst === NULL)
			{
				self::$inst = new self;
			}

			return self::$inst;
		}

		private function __construct()
		{
		}

		public function make($type, $id)
		{
			if (!array_key_exists($type, $this->storage))
			{
				$this->storage[$type] = array();
			}

			if (!array_key_exists($id, $this->storage[$type]))
			{
				$this->storage[$type][$id] = $type::make($id);
			}

			return $this->storage[$type][$id];
		}
	}
}

?>
