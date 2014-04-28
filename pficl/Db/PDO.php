<?php

namespace pficl\Db
{
	use \pficl\Fp\Util as Fp;

	final class PDO extends \PDO
	{
		public function queryFetchAllAssoc($sql)
		{
			return $this->query($sql)->fetchAll(self::FETCH_ASSOC);
		}

		public function fetchRow($storageName, array $fieldList)
		{
			$this->checkStorageName($storageName)->checkFieldList($fieldList);

			$sql = 'SELECT * FROM `'.$storageName.'` WHERE 1 ';

			foreach ($fieldList as $fieldName => $fieldValue)
			{
				$sql .= 'AND `'.$fieldName.'` = :'.$fieldName.' ';
			}

			$st = $this->prepare($sql);

			$st->execute($fieldList);

			if ($st->rowCount() === 0)
			{
				return NULL;
			}
			elseif ($st->rowCount() === 1)
			{
				return $st->fetch(self::FETCH_ASSOC);
			}
			else
			{
				throw new MultipleRowsException();
			}
		}

		public function checkStorageName($storageName)
		{
			if (!(is_string($storageName) && strval($storageName) && preg_match('/^[\w_\d]{1,}$/', $storageName)))
			{
				throw new InvalidStorageNameException();
			}

			return $this;
		}

		public function checkFieldList(array $fieldList)
		{
			foreach ($fieldList as $fieldName => $fieldValue)
			{
				if (!(is_string($fieldName) && strval($fieldName) && preg_match('/^[\d_\w]{1,}$/', $fieldName)))
				{
					throw new InvalidFieldListException();
				}

				if (!(is_int($fieldValue) || is_string($fieldValue) || is_float($fieldValue) || is_null($fieldValue) || is_bool($fieldValue)))
				{
					throw new InvalidFieldListException();
				}
			}

			return $this;
		}

		public function mkIn(array $values)
		{
			$obj = $this;
			return implode(',', array_map(function($val) use($obj) {return $obj->quote($val);}, $values));
		}

		public function mkInsertUpdate($tableName, array $insert, array $update)
		{
			return 'INSERT INTO `'.$tableName.'` ('.$this->insNm($insert).') VALUES('.$this->insVal($insert).') ON DUPLICATE KEY UPDATE '.$this->updLst($update);
		}

		public function updPart($fieldName, $fieldValue)
		{
			// MYSQL ONLY
			return '`'.$fieldName.'` = '.$this->quote($fieldValue);
		}

		public function updLst(array $data)
		{
			// MYSQL ONLY
			$obj = $this;

			$func = function($fieldName, $fieldValue) use ($obj)
			{
				return $obj->updPart($fieldName, $fieldValue);
			};

			return implode(',', Fp::map($func, array_keys($data), array_values($data)));
		}

		public function insNm(array $data)
		{
			// MYSQL ONLY
			$escape = function($val)
			{
				return '`'.$val.'`';
			};

			return implode(',', Fp::map($escape, array_keys($data)));
		}

		public function insVal(array $data)
		{
			$obj = $this;
			
			$escape = function($val) use ($obj)
			{
				return $obj->quote($val);
			};

			return implode(',', Fp::map($escape, $data));
		}
	}
}

?>
