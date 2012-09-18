<?php

namespace pficl\Collection
{
	final class Util
	{
		final static public function arrayUnique(array $array)
		{
			$result = array();

			for ($index = 0; $index != count($array); ++$index)
			{
				$value = $array[$index];

				if (! in_array($value, $result))
				{
					$result[] = $value;
				}
			}

			return $result;
		}

		final static public function arraySliceAssoc($array, $from, $to)
		{
			$result = array();

			$open = FALSE;

			foreach ($array as $key => $value)
			{
				if ($key === $to)
				{
					$open = FALSE;
				}

				if ($open)
				{
					$result[$key] = $value;
				}

				if ($key === $from)
				{
					$open = TRUE;
				}
			}

			return $result;
		}

		final static public function arrayTranspose(array $array)
		{
			array_unshift($array, NULL);
			return call_user_func_array('array_map', $array);
		}

		final static public function containAllKeys(array $keyList, array $array)
		{
			return Fp::all(function($key) use($array) {return array_key_exists($key, $array);}, $keyList);
		}

		final static public function arrayConcat(array $array)
		{
			return call_user_func_array('array_merge', $array);
		}

		final static public function getFirst(array $array)
		{
			$keys = array_keys($array);

			if (count($keys))
			{
				$firstKey = array_shift($keys);

				return $array[$firstKey];
			}
			else
			{
				return NULL;
			}
		}

		final static public function getLast(array $array)
		{
			$keys = array_keys($array);
			$lastKey = array_pop($keys);

			return $array[$lastKey];
		}

		final static public function isList(array $array)
		{
			if (count($array))
			{
				return range(0, max(array_keys($array))) === array_keys($array) && Fp::all('is_int', array_keys($array));
			}
			else
			{
				return NULL;
			}
		}

		final static public function makeAssoc(array $array, $fieldName, $saveKey = FALSE)
		{
			$assocArray = array();

			foreach ($array as $key => $value)
			{
				$assocArray[$value[$fieldName]] = $value;

				if (strval($saveKey))
				{
					$assocArray[$value[$fieldName]][$saveKey] = $key;
				}
			}

			return $assocArray;
		}

		final static public function isAssoc(array $arr)
		{
			return array_keys($arr) !== range(0, count($arr) - 1);
		}

		final static public function find(array $collection, $predicate)
		{
			$iter = new ArrayIterator($collection);

			while ($iter->valid())
			{
				if (call_user_func($predicate, $iter->current()))
				{
					return $iter;
				}
				else
				{
					$iter->next();
				}
			}

			return $iter;
		}

		final static public function findObject(array $collection, $predicate)
		{
			$iter = self::find($collection, $predicate);

			if ($iter->valid())
			{
				return $iter->current();
			}
			else
			{
				return NULL;
			}
		}

		final static public function collectionContainsObject(array $collection, $object)
		{
			foreach ($collection as $collectionMember)
			{
				if ($collectionMember === $object)
				{
					return TRUE;
				}
			}

			return FALSE;
		}

		final static public function ensureKeys(array $subj, array $keyCol)
		{
			$existingKeys = array_keys($subj);

			foreach ($keyCol as $key)
			{
				if (!in_array($key, $existingKeys))
				{
					return FALSE;
				}
			}

			return TRUE;
		}

		final static public function createKeysIfNotExists(array $subj, array $keyCol, $defaultValue = NULL)
		{
			$existingKeys = array_keys($subj);

			foreach ($keyCol as $key)
			{
				if (!in_array($key, $existingKeys))
				{
					$subj[$key] = $defaultValue;
				}
			}

			return $subj;
		}

		final public static function getField($array, $fieldName, $default = NULL)
		{
			if (array_key_exists($fieldName, $array))
			{
				return $array[$fieldName];
			}
			else
			{
				return $default;
			}
		}

		final public static function mergeBySameKeys()
		{
			$args = func_get_args();

			foreach ($args as $key => $value)
			{
				$args[$key] = array_map(create_function('$a', 'return array('.$key.' => $a);'), $value);
			}

			return call_user_func_array('array_merge_recursive', $args);
		}

		final public static function keyFilter(array $keyList, array $collection)
		{
			$filtered = array();

			foreach ($keyList as $key)
			{
				if (array_key_exists($key, $collection))
				{
					$filtered[$key] = $collection[$key];
				}
			}

			return $filtered;
		}

		final public static function transformArray(array $was, array $matrix)
		{
			$now = array();

			foreach ($matrix as $oldKey => $newKey)
			{
				if (array_key_exists($oldKey, $was))
				{
					$now[$newKey] = $was[$oldKey];
				}
			}

			return $now;
		}

		final public static function areEqual(array $arr1, array $arr2)
		{
			return count(array_diff($arr1, $arr2)) === 0 && count(array_diff($arr2, $arr1)) === 0;
		}

		final private function __construct()
		{
		}
	}
}

?>
