<?php

namespace pficl\Collection
{
	class RecursiveArrayOnlyIterator extends \RecursiveArrayIterator
	{
		public function hasChildren()
		{
			return is_array($this->current());
		}
	}
}

?>
