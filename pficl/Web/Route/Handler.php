<?php

namespace pficl\Web\Route
{
	class HandlerInvalidRouteException extends \Exception
	{
	}

	abstract class Handler
	{
		abstract protected function handleValidRoute();

		private $route;

		/** @return \pficl\Web\Route\Route */
		final public function getRemainderRoute()
		{
			return $this->route;
		}

		public function getPageName()
		{
			return 'Unknown';
		}

		/** @return \pficl\Web\Route\Route */
		public function getMatchedRoute()
		{
			$remainder = $this->getRemainderRoute()->getComponentList();
			$full = \Autoload::inst()->webState()->route()->getComponentList();

			return Route::make(\pficl\Collection\Util::rtrim($full, $remainder));
		}

		/** @return \pficl\Web\Route\Handler */
		public static function make(Route $r)
		{
			$calledClass = get_called_class();
			return new $calledClass($r);
		}

		final protected function __construct(Route $r)
		{
			$this->route = $r;
			$this->init();
		}

		protected function init()
		{

		}

		public function isValidRoute()
		{
			return TRUE;
		}

		public function isTerminating()
		{
			return TRUE;
		}

		final public function process()
		{
			if ($this->isValidRoute())
			{
				$this->handleValidRoute();
			}
			else
			{
				$this->handleInvalidRoute();
			}

			if ($this->isTerminating())
			{
				die();
			}
		}

		protected function handleInvalidRoute()
		{
			throw new HandlerInvalidRouteException($this->getRemainderRoute()->toJson());
		}
	}
}

?>
