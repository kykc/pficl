<?php

namespace pficl\Model\Deprecated
{
	interface IGenericModel extends IDbObjectStorageConsumer
	{
		public function saveToDb();
		public static function make($id);
		public function getFieldList();
	}
}

?>
