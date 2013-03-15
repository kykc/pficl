<?php

namespace pficl\Model\Deprecated
{
	class DbObjectStorageProvider
	{
		private $subject;
		private $allowSlave;

		/** @return \pficl\Model\Deprecated\IDbObjectStorageConsumer */
		public function getSubject()
		{
			return $this->subject;
		}

		private function __construct()
		{
		}

		/** @return \pficl\Model\Deprecated\DbObjectStorageProvider */
		public static function make(IDbObjectStorageConsumer $subject, $allowSlave = FALSE)
		{
			$obj = new self;
			$obj->subject = $subject;
			$obj->allowSlave = $allowSlave;

			return $obj;
		}

		public function isSlaveAllowed()
		{
			return $this->allowSlave ? TRUE : FALSE;
		}

		public function fetchDataFromStorage()
		{
			if ($this->getSubject()->getPrimaryId())
			{
				$sql =
						'	SELECT * '.
						'		FROM '.$this->getSubject()->getStorageName().' '.
						'		WHERE id = \''.mysql_real_escape_string($this->getSubject()->getPrimaryId()).'\' '.
						'	; ';

				$statement = $this->getSubject()->db()->query($sql);

				$rowCount = $statement->rowCount();
				$row = $statement->fetch(\PDO::FETCH_ASSOC);

				if ($rowCount === 1 && $row['id'] == $this->getSubject()->getPrimaryId())
				{
					return $this->getSubject()->prepareStorageState($row);
				}
				else
				{
					throw new DbObjectStorageProviderCannotFetchDataException($this->getSubject()->getPrimaryId());
				}
			}
			else
			{
				throw new DbObjectStorageProviderCannotFetchDataException($this->getSubject()->getPrimaryId());
			}
		}

		public function storeData()
		{
			$diff = $this->getSubject()->getCurrentLifetimeDiff();
			$state = $this->getSubject()->getObjectState(TRUE);

			$insertId = NULL;

			if ($this->getSubject()->getPrimaryId())
			{
				if (array_key_exists('id', $diff))
				{
					throw new DbObjectStorageProviderCannotChangePrimaryKey(print_r($diff['id'], TRUE));
				}

				$sql = 'UPDATE '.$this->getSubject()->getStorageName().' SET ';
				$partCollection = array();

				foreach ($diff as $fieldName => $fieldValue)
				{
					$partCollection[] = $this->makeUpdatePart($fieldName, $fieldValue['now']);
				}

				$sql .= implode(', ', $partCollection).' WHERE id = \''.mysql_real_escape_string($this->getSubject()->getPrimaryId()).'\'';

				if (count($diff))
				{
					$this->getSubject()->db()->exec($sql);
				}
			}
			else
			{
				$fieldCollection = $state;
				unset($fieldCollection['id']);
				$fieldCollection = array_map(array($this, 'escape'), $fieldCollection);

				$sql =
						'	INSERT INTO '.$this->getSubject()->getStorageName().' '.
						'			('.implode(', ', array_map(function($a){return '`'.$a.'`';}, array_keys($fieldCollection))).') '.
						'		VALUES '.
						'			('.implode(', ', $fieldCollection).') '.
						'	; ';

				$this->getSubject()->db()->exec($sql);
				$insertId = $this->getSubject()->db()->lastInsertId();
			}

			return $insertId;
		}

		private function makeUpdatePart($fieldName, $fieldValue)
		{
			return '`'.$fieldName.'` = '.$this->escape($fieldValue);
		}

		private function escape($fieldValue)
		{
			if (is_int($fieldValue))
			{
				return intval($fieldValue);
			}
			elseif (is_float($fieldValue))
			{
				return floatval($fieldValue);
			}
			elseif (is_string($fieldValue))
			{
				return '\''.mysql_real_escape_string($fieldValue).'\'';
			}
			elseif (is_bool($fieldValue))
			{
				return intval($fieldValue) ? 1 : 0;
			}
			elseif (is_null($fieldValue))
			{
				return 'NULL';
			}
			elseif (is_null($fieldValue))
			{
				return '\'\'';
			}
			else
			{
				throw new DbObjectStorageProviderInvalidDataTypeException(gettype($fieldValue));
			}
		}
	}
}

?>
