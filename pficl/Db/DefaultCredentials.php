<?php

namespace pficl\Db
{
	abstract class DefaultCredentials
	{
		public function getUser()
		{
			return ini_get('mysql.default_user');
		}

		public function getPasswd()
		{
			return ini_get('mysql.default_password');
		}

		abstract public function getDatabase();

		public function getHost()
		{
			return 'localhost';
		}

		private function __construct()
		{
		}

		final public static function make()
		{
			$calledClass = get_called_class();

			return new $calledClass;
		}
	}
}

?>
