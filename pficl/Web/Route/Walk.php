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
			return strpos($str, 'class/') === 0 ? str_replace('/', '\\', str_replace('class/', '', $str)) : FALSE;
		}

		public static function ifLambdaHandler($str)
		{
			return strpos($str, 'lambda/') === 0 ? str_replace('lambda/', '', $str) : FALSE;
		}

		public static function captionRoute(Route $route, array $routingTable)
		{
			$handledRoute = NULL;
			$handlerName = Walk::chooseHandler($route, $routingTable, $handledRoute);

			$remainder = $handledRoute ? $route->subtract($handledRoute) : $route;

			if (Walk::ifClassHandler($handlerName))
			{
				$className = \Autoload::inst()->getWebHandlerNamespace().Walk::ifClassHandler($handlerName);

				return $className::make($remainder)->getPageName();
			}

			return '';
		}

		public static function handleRoute(Route $route, array $routingTable, array $handlers)
		{
			$handledRoute = NULL;
			$handlerName = Walk::chooseHandler($route, $routingTable, $handledRoute);

			$remainder = $handledRoute ? $route->subtract($handledRoute) : $route;

			if (Walk::ifClassHandler($handlerName))
			{
				$className = \Autoload::inst()->getWebHandlerNamespace().Walk::ifClassHandler($handlerName);

				$className::make($remainder)->process();
			}
			elseif (Walk::ifLambdaHandler($handlerName))
			{
				$handlers[Walk::ifLambdaHandler($handlerName)]($remainder);
			}
		}

		public static function chooseHandler(Route $route, array $routingTable, &$handledRoute)
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

			if (count($action) === 4)
			{
				array_pop($action);
			}

			$handlerName = array_pop($action);

			if (count($action) === 2)
			{
				$handledRoute = Route::makeByPath(array_pop($action));
			}

			return $handlerName;
		}
	}
}

?>
