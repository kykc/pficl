<?php

namespace pficl\Collection
{
	class DefaultValue
	{
		private $value;

		/** @return \pficl\Collection\DefaultValue */
		public static function make($value = NULL)
		{
			return new self($value);
		}

		private function __construct($value)
		{
			$this->value = $value;
		}

		public function getValue()
		{
			return $this->value;
		}
	}
}

?>
