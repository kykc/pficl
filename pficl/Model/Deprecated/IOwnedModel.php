<?php

namespace pficl\Model\Deprecated
{
	interface IOwnedModel extends IGenericModel
	{
		public function setOwnerId($id);
		public function getOwnerId();
		public function setOwnerType($type);
		public function getOwnerType();
	}
}

?>
