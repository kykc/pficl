<?php

namespace pficl\Cache\Encoding
{
	interface IProcessor
	{
		public function getMethod();
		public function getData();
		public function isEncoded();
		public function isDecoded();
		public function encode();
		public function decode();
		public function loadDecoded($data);
		public function loadEncoded($data, $method);
	}

	class Exception extends \Exception
	{
	}

	final class UnknownMethod extends Exception
	{
	}

	abstract class Util
	{
		const METHOD_NONE = 'none';
		const METHOD_JSON = 'json';
		const METHOD_PHP = 'php';

		public static function encode($val, $method)
		{
			if ($method == self::METHOD_NONE)
			{
				return $val;
			}
			elseif ($method == self::METHOD_JSON)
			{
				return json_encode($val);
			}
			elseif ($method == self::METHOD_PHP)
			{
				return serialize($val);
			}
			else
			{
				throw new UnknownMethod($method);
			}
		}

		public static function decode($val, $method)
		{
			if ($method == self::METHOD_NONE)
			{
				return $val;
			}
			elseif ($method == self::METHOD_JSON)
			{
				return json_decode($val, TRUE);
			}
			elseif ($method == self::METHOD_PHP)
			{
				return unserialize($val);
			}
			else
			{
				throw new UnknownMethod($method);
			}
		}
	}
}

?>
