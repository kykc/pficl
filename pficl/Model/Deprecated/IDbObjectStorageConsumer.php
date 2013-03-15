<?php

namespace pficl\Model\Deprecated
{
	interface IDbObjectStorageConsumer extends IObjectLifetimeDiffSubject
	{
		public function getStorageName();
		public function getPrimaryId();
		public function prepareStorageState(array $state);
		/** @return \pficl\Db\PDO */
		public function db();
	}
}

?>
