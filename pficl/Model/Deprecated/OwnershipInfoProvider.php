<?php

namespace pficl\Model\Deprecated
{
	class OwnershipInfoProvider
	{
		protected $ownerId = 0;
		protected $ownerType = 'none';

		protected function __construct()
		{
		}

		/** @return OwnershipInfoProvider */
		public static function make($mod = NULL)
		{
			$obj = new self;

			if ($mod instanceof IOwnedModel)
			{
				$obj->ownerId = $mod->getOwnerId();
				$obj->ownerType = $mod->getOwnerType();
			}

			return $obj;
		}

		public static function makeByRawData($ownerId, $ownerType)
		{
			$obj = new self;

			$obj->ownerId = $ownerId;
			$obj->ownerType = $ownerType;

			return $obj;
		}

		public function getOwnerId()
		{
			return $this->ownerId;
		}

		public function getOwnerType()
		{
			return $this->ownerType;
		}
	}
}

?>
