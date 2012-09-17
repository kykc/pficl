<?php

namespace pficl\Core
{
	abstract class Util
	{
		const DIR_NAME_TEST_SUITE = 'tests';
		const DIR_NAME_CLASS_LIBRARY = 'pficl';
		
		final public static function getMainLocation()
		{
			return PFICL_PATH;
		}
		
		final public static function getTestsLocation()
		{
			return PFICL_PATH.'/'.self::DIR_NAME_TEST_SUITE;
		}
		
		final public static function isInNamespace($fullName, $namespace)
		{
			$namespace = ltrim($namespace, '\\');
			
			if (strpos($fullName, '\\') === FALSE && !strval($namespace))
			{
				return TRUE;
			}
			else
			{
				return strpos($fullName, $namespace) === 0; //&& strpos('\\', str_replace($namespace, '', $fullName)) === 0;
			}
		}
	}
}

?>