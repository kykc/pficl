<?php

namespace pficl\Fp
{	
	class FpCollectionEmptyException extends \Exception
	{
	}

	abstract class Util
	{
		final protected function __construct()
		{
		}

		final public static function id()
		{
			return function($a)
			{
				return $a;
			};
		}

		final public static function null()
		{
			return function() {return NULL;};
		}

		final public static function any($mappingLambda, array $collection)
		{
			return array_reduce(array_map($mappingLambda, $collection), function($a, $b) {return $a || $b;}, FALSE);
		}

		final public static function all($mappingLambda, array $collection)
		{
			return array_reduce(array_map($mappingLambda, $collection), function($a, $b) {return $a && $b;}, TRUE);
		}

		/* Just an alias of array_map, because map in php is map and zip *doh*/
		final public static function zipWith()
		{
			return call_user_func_array('array_map', func_get_args());
		}

		final public static function one($mappingLambda, array $collection)
		{
			return array_sum(array_map($mappingLambda, $collection)) === 1;
		}

		final public static function cnst($a, $b)
		{
			return $a;
		}

		final public static function merge()
		{
			return call_user_func_array('array_merge', func_get_args());
		}

		final public static function makeCallLambda($funcName)
		{
			return function($a) use ($funcName)
			{
				return call_user_func(array($a, $funcName));
			};
		}

		final public static function makeFetchElementLambda($key)
		{
			return function($a) use ($key)
			{
				return $a[$key];
			};
		}

		final public static function flip($function)
		{
			return function() use ($function)
			{
				return call_user_func_array($function, array_reverse(func_get_args()));
			};
		}

		final public static function map()
		{
			return call_user_func_array('array_map', func_get_args());
		}

		final public static function foldl($lambda, array $collection)
		{
			if (count($collection) > 0)
			{
				$initial = array_pop($collection);
				return array_reduce($collection, $lambda, $initial);
			}
			else
			{
				throw new FpCollectionEmptyException();
			}
		}

		final public static function foldr($lambda, array $collection)
		{
			$collection = array_reverse($collection);
			$lambda = Util::flip($lambda);

			return Util::foldl($lambda, $collection);
		}

		final public static function filter($lambda, array $collection)
		{
			return array_filter($collection, $lambda);
		}

		final public static function dot($c1, $c2)
		{
			return function($a) use ($c1, $c2)
			{
				return $c2($c1($a));
			};
		}
	}

	class Func
	{
		private $callable;

		private function __construct($callable)
		{
			$this->callable = $callable;
		}

		/** @return Func */
		public static function make($callable)
		{
			return new self($callable);
		}

		public function getResult()
		{
			return $this->callable;
		}

		public function isCallable()
		{
			return is_callable($this->callable);
		}

		public function call()
		{
			if (is_callable($this->callable))
			{
				$this->callable = call_user_func_array($this->callable, func_get_args());
			}
			else
			{
				throw new FuncNotCallableException(serialize($this->callable));
			}

			return $this;
		}
	}
}

?>
