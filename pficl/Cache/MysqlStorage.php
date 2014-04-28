<?php

namespace pficl\Cache
{
	class MysqlStorage implements IStorage
	{
		private $manager;
		private $tableName;

		/** @return \pficl\Db\Manager */
		public function getDbManager()
		{
			return $this->manager;
		}

		public function getTableName()
		{
			return $this->tableName;
		}

		public static function make(\pficl\Db\Manager $db, $tableName)
		{
			$obj = new static;
			$obj->manager = $db;
			$obj->tableName = $tableName;

			return $obj;
		}

		protected function __construct()
		{

		}

		/** @return \pficl\Cache\Encoding\IProcessor */
		protected function makeKeyProc()
		{
			return Encoding\GenericProcessor::make();
		}

		/** @return \pficl\Cache\Encoding\IProcessor */
		protected function makeDataProc()
		{
			return Encoding\GenericProcessor::make();
		}

		/** @return \pficl\Cache\IStorageSubject */
		final public function calculateOrUseCache($key, \Closure $dataProvider, $periodLength)
		{
			$result = $this->load($key, NULL);
			$periodStart = date('Y-m-d H:i:s');

			if ($result->getData() === NULL)
			{
				$result = $this->save($key, $dataProvider(), $periodStart, $periodLength);
			}

			return $result;
		}

		/** @return \pficl\Cache\IStorageSubject */
		final public function load($key, $defData = NULL)
		{
			$keyProc = $this->makeKeyProc()->loadDecoded($key)->encode();
			$key = $keyProc->getData();
			$now = date('Y-m-d H:i:s');

			$statement = $this->getDbManager()->pdo()->prepare('SELECT * FROM `'.$this->getTableName().'` WHERE `key` = :key AND DATE_ADD(periodStart, INTERVAL periodLength SECOND) >= :now');

			$statement->execute(compact('key', 'now'));

			$rowList = $statement->fetchAll();

			$statement->closeCursor();

			if (count($rowList) > 0)
			{
				$row = array_shift($rowList);

				$dataProc = $this->makeDataProc()->loadEncoded($row['data'], $row['decodeData'])->decode();
				$data = $dataProc->getData();

				return GenericSubject::make($data, $row['periodStart'], $row['periodLength'], $now);
			}
			else
			{
				return GenericSubject::make($defData, NULL, NULL, NULL);
			}
		}

		final public function save($key, $data, $periodStart, $periodLength)
		{
			$keyProc = $this->makeKeyProc()->loadDecoded($key)->encode();
			$dataProc = $this->makeDataProc()->loadDecoded($data)->encode();
			$rawData = $data;

			$key = $keyProc->getData();
			$data = $dataProc->getData();
			$decodeKey = $keyProc->getMethod();
			$decodeData = $dataProc->getMethod();
			$now = date('Y-m-d H:i:s');

			$insertData = compact('key', 'data', 'periodStart', 'periodLength', 'decodeKey', 'decodeData');
			$updateData = $insertData;
			unset($updateData['key']);

			$pdo = $this->getDbManager()->pdo();

			$statement = $pdo->prepare('INSERT INTO `'.$this->getTableName().'` '.
					'('.$pdo->insNm($insertData).') '.
					'VALUES('.$pdo->insVal($insertData).') '.
					'ON DUPLICATE KEY UPDATE '.$pdo->updLst($updateData));

			$statement->execute();

			$statement->closeCursor();

			return GenericSubject::make($rawData, $periodStart, $periodLength, $now);
		}

		final public function update($key, $data)
		{
			$keyProc = $this->makeKeyProc()->loadDecoded($key)->encode();
			$dataProc = $this->makeDataProc()->loadDecoded($data)->encode();

			$key = $keyProc->getData();
			$data = $dataProc->getData();

			$pdo = $this->getDbManager()->pdo();

			$statement = $pdo->prepare('UPDATE `'.$this->getTableName().'` SET `data` = :data WHERE `key` = :key');

			$statement->execute(compact('key', 'data'));

			$statement->closeCursor();
		}
	}
}

?>
