<?php

namespace pficl\Db
{
	final class Manager
	{
		private $pdo = NULL;
		private $cred;

		/** @return \pficl\Db\Manager */
		public static function make(\pficl\Db\DefaultCredentials $cred)
		{
			return new self($cred);
		}

		private function __construct($cred)
		{
			$this->cred = $cred;
		}

		/** @return \pficl\Db\DefaultCredentials */
		public function getCredentials()
		{
			return $this->cred;
		}

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
