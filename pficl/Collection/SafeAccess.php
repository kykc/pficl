<?php

namespace pficl\Collection
{
	class SafeAccess
	{
		private $subj;
		private $default;

		/** @return \pficl\Collection\SafeAccess */
		public static function make($subj, DefaultValue $default = NULL)
		{
			return new self($subj, $default);
		}

		private function __construct($subj, $default)
		{
			$this->subj = $subj;
			$this->default = $default;
		}

		/** @return \pficl\Collection\SafeAccess */
		public function def(DefaultValue $default = NULL)
		{
			return SafeAccess::make($this->subj, $default);
		}

		/** @return \Library\DataStructure\SafeAccess */
		public function nodef()
		{
			return SafeAccess::make($this->subj);
		}

		/** @return \pficl\Collection\SafeAccess */
		public function sub($key)
		{
			if (is_array($this->subj) && array_key_exists($key, $this->subj))
			{
				return SafeAccess::make($this->subj[$key], $this->default);
			}
			elseif ($this->default === NULL)
			{
				throw new NoSuchElementException($key);
			}
			else
			{
				return SafeAccess::make($this->default->getValue(), $this->default);
			}
		}

		// TODO: support nested calls
		public function fetch($str)
		{
			return $this->sub($str)->val();
		}

		public function val()
		{
			return $this->subj;
		}

		public function has($key)
		{
			return array_key_exists($key, $this->subj);
		}
	}
}

?>
