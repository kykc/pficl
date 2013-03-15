<?php

namespace pficl\Model\Deprecated
{
	interface IObjectLifetimeDiffSubject
	{
		public function getObjectState($forPersist = FALSE);
		public function setObjectState(array $state);
		public function getCurrentLifetimeDiff();
	}
}

?>
