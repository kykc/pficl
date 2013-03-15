<?php

namespace pficl\Core
{
	final class TimeMeter
	{
		private $start = NULL;

		/** @return \pficl\Core\TimeMeter */
		final public static function make()
		{
			return new self;
		}

		final private function __construct()
		{
			$this->start();
		}

		/** @return \TimeMeter */
		final private function start()
		{
			$this->start = microtime(TRUE);

			return $this;
		}

		final public function reset()
		{
			$value = microtime(TRUE) - $this->start;

			$this->start = microtime(TRUE);

			return round($value, 4);
		}
	}
}

?>
