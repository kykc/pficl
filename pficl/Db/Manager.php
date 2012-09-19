<?php

namespace pficl\Db
{
	abstract class Manager
	{
		private $pdo = NULL;
		private static $instance = NULL;
		private $cred;

		/** @return \pficl\Db\Manager */
		public static function inst()
		{
			if (self::$instance === NULL)
			{
				$calledClass = get_called_class();
				self::$instance = new $calledClass;
			}

			return self::$instance;
		}

		private function __construct()
		{
			$this->cred = $this->makeCredentials();
		}

		/** @return \pficl\Db\DefaultCredentials */
		public function getCredentials()
		{
			return $this->cred;
		}

		abstract protected function makeCredentials();

		/** @return \pficl\Db\PDO */
		public function pdo()
		{
			if ($this->pdo === NULL)
			{
				$this->pdo = new PDO('mysql:host='.$this->getCredentials()->getHost().';dbname='.$this->getCredentials()->getDatabase(), $this->getCredentials()->getUser(), $this->getCredentials()->getPasswd());
				$this->pdo->exec('SET NAMES utf8');
				$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			}

			return $this->pdo;
		}
	}
}

?>
