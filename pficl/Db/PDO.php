<?php

namespace pficl\Db
{
	use \pficl\Fp\Util as Fp;

	final class PDO extends \PDO
	{
		public function mkIn(array $values)
		{
			$obj = $this;
			return implode(',', array_map(function($val) use($obj) {return $obj->quote($val);}, $values));
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
