<?php

namespace pficl\Web
{
	final class Util
	{
		private function __construct()
		{
		}

		public static function link($l)
		{
			return '/'.State::inst()->root().'/'.$l;
		}

		// TODO: normal implementation
		public static function isLocalIp($ip)
		{
			return preg_match('/^192\.168\./', $ip) ? TRUE : FALSE;
		}
	}
}

?>
