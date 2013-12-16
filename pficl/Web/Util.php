<?php

namespace pficl\Web
{
	use \pficl\Web\Route\Route;

	final class Util
	{
		private function __construct()
		{
		}

		// TODO: normal implementation
		public static function isLocalIp($ip)
		{
			return preg_match('/^192\.168\./', $ip) ? TRUE : FALSE;
		}

		public static function isHomeIp($ip)
		{
			return gethostbyname('ya.tomatl.org') == $ip;
		}

		public static function isRouteInPathList(Route $r, array $list)
		{
			$mappingLambda = function($path) use ($r)
			{
				return $r->isEqualRoute(Route::makeByPath($path));
			};

			return \pficl\Fp\Util::any($mappingLambda, $list);
		}

		public static function isSubrouteInPathList(Route $r, array $list)
		{
			$map = function($path) use ($r)
			{
				return $r->startsWith(Route::makeByPath($path));
			};

			return \pficl\Fp\Util::any($map, $list);
		}
	}
}

?>
