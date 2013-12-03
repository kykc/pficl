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

		public static function ifClassHandler($str)
		{
			return strpos($str, 'class/') === 0 ? str_replace('class/', '', $str) : FALSE;
		}

		public static function ifLambdaHandler($str)
		{
			return strpos($str, 'lambda/') === 0 ? str_replace('lambda/', '', $str) : FALSE;
		}

		public static function chooseHandler(Route $route, array $routingTable)
		{
			$filter = function($subj) use ($route)
			{
				$type = array_shift($subj);

				if ($type === 'equals')
				{
					return $route->isEqualRoute(Route::makeByPath(array_shift($subj)));
				}
				elseif ($type === 'starts')
				{
					return $route->startsWith(Route::makeByPath(array_shift($subj)));
				}
				elseif ($type === 'default')
				{
					return TRUE;
				}
				else
				{
					return FALSE;
				}
			};

			$info = array_values(\pficl\Fp\Util::filter($filter, $routingTable));

			$action = array_shift($info);

			$handlerName = array_pop($action);

			return $handlerName;
		}
	}
}

?>
