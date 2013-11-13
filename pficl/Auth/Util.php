<?php

namespace pficl\Auth
{
	abstract class Util
	{
		public static function getToken()
		{
			if (array_key_exists('HTTP_TOMATL_AUTH', $_SERVER) && 
				(\pficl\Web\Util::isLocalIp($_SERVER['REMOTE_ADDR']) || \pficl\Web\Util::isHomeIp($_SERVER['REMOTE_ADDR'])))
			{
				return $_SERVER['HTTP_TOMATL_AUTH'];
			}
			else
			{
				return NULL;
			}
		}
	}
}

?>
