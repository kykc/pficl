<?php

namespace pficl\Web\Route
{
	class Walk
	{
		private $route;

		/** @return \pficl\Web\Route\Walk */
		public static function make(Route $r)
		{
			return new self($r);
		}

		private function __construct(Route $r)
		{
			$this->route = $r;
		}

		public function step($r)
		{
			if (is_string($r))
			{
				$r = Route::makeByJson($r);
			}

			if ($this->route->startsWith($r))
			{
				$this->route = $this->route->subtract($r);

				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}

		public function fstep($r)
		{
			if (is_string($r))
			{
				$r = Route::makeByJson($r);
			}

			if ($this->route->isEqualRoute($r))
			{
				$this->route = $this->route->subtract($r);

				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}

		public function route()
		{
			return $this->route;
		}
	}
}

?>
