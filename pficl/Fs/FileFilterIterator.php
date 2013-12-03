<?php

namespace pficl\Fs
{
	use \FilterIterator as FilterIterator;
	use \Iterator as Iterator;
	use \Closure as Closure;
	
	class FileFilterIterator extends FilterIterator
	{
		protected $customFilter = NULL;

		public function accept()
		{
			$pathName = $this->getInnerIterator()->current()->getPathName();

			if (is_readable($pathName) && is_file($pathName) && $this->applyCustomFilter())
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}

		public function __construct(Iterator $i, Closure $customFilter)
		{
			parent::__construct($i);
			$this->customFilter = $customFilter;
		}

		protected function applyCustomFilter()
		{
			if ($this->customFilter)
			{
				$filter = $this->customFilter;
				return $filter($this->getInnerIterator()->current());
			}
			else
			{
				return TRUE;
			}
		}
	}
}
?>
