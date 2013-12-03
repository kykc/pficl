<?php

namespace pficl\Db
{
	class DefaultCredentials
	{
		private $db;

		public function getUser()
		{
			return ini_get('mysql.default_user');
		}

		public function getPasswd()
		{
			return ini_get('mysql.default_password');
		}

		public function getDatabase()
		{
			return $this->db;
		}

		public function getHost()
		{
			return 'localhost';
		}

		private function __construct()
		{
		}

		/** @return \pficl\Db\DefaultCredentials */
		final public static function make($db = NULL)
		{
			$obj = new static;
			$obj->db = $db;

			return $obj;
		}
	}
}

?>
