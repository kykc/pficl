<?php

namespace pficl\Web\Route
{
	use \pficl\Fp\Util as Fp;

	class InvalidRouteComponentListException extends \Exception
	{
	}

	class RouteSubtractFailedException extends \Exception
	{
	}

	class Route
	{
		private $path;

		private function __construct(array $path)
		{
			if ($this->isStringArray($path))
			{
				$this->path = $path;
			}
			else
			{
				throw new InvalidRouteComponentListException(json_encode($path));
			}
		}

		private function isStringArray(array $arr)
		{
			return count($arr) === 0 || Fp::all('is_string', $arr);
		}

		/** @return \pficl\Web\Route\Route */
		public static function makeCurrent($routerLocation = 'master.php')
		{
			$scriptLocation = $_SERVER['SCRIPT_FILENAME'];
			$documentRoot = $_SERVER['DOCUMENT_ROOT'];

			$relativeRouterLocation = str_replace($routerLocation, '', str_replace($documentRoot, '', $scriptLocation));

			$fullPath = array_key_exists('REDIRECT_URL', $_SERVER) ? $_SERVER['REDIRECT_URL'] : $_SERVER['SCRIPT_NAME'];
			$relativePath = str_replace($relativeRouterLocation, '', $fullPath);
			$relativePath = trim($relativePath, '/');

			if (is_string($relativePath) && strlen($relativePath) > 0)
			{
				return new self(explode('/', $relativePath));
			}
			else
			{
				return new self(array());
			}
		}

		/** @return \pficl\Web\Route\Route */
		public static function make(array $path)
		{
			return new self($path);
		}

		/** @return \pficl\Web\Route\Route */
		public static function makeByJson($serialized)
		{
			return new self(json_decode($serialized, TRUE));
		}

		/** @return \pficl\Web\Route\Route */
		public static function makeByPath($str)
		{
			return new self(explode('/', trim($str, '/')));
		}

		public function getComponentList()
		{
			return $this->path;
		}

		public function toJson()
		{
			return json_encode($this->path);
		}

		public function getComponentCount()
		{
			return count($this->path);
		}

		/** @return \pficl\Web\Route\Route */
		public function subtract(Route $r, $test = FALSE)
		{
			$result = NULL;

			if ($this->getComponentCount() >= $r->getComponentCount())
			{
				$part = Route::make(array_slice($this->getComponentList(), 0, $r->getComponentCount()));

				if ($r->isEqualRoute($part))
				{
					$result = Route::make(array_slice($this->getComponentList(), $r->getComponentCount()));
				}
			}

			if ($result === NULL && !$test)
			{
				throw new RouteSubtractFailedException();
			}
			else
			{
				return $result;
			}
		}

		public function startsWith(Route $r)
		{
			try
			{
				$this->subtract($r);

				return TRUE;
			}
			catch (RouteSubtractFailedException $ex)
			{
				return FALSE;
			}
		}

		public function isEqualRoute(Route $r)
		{
			return $this->isEqualComponentList($r->getComponentList());
		}

		public function isEqualComponentList(array $list)
		{
			return $this->getComponentList() === $list;
		}

		public function isEmpty()
		{
			return count($this->path) === 0;
		}
	}
}

?>
