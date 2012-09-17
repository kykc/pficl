<?php

namespace pficl\Core
{
	abstract class Autoload
	{
		final public static function getInstance()
		{
			$className = get_called_class();
			
			return new $className;
		}
		
		final private function __construct()
		{
			
		}
		
		final public static function getCallable()
		{
			$calledClass = get_called_class();
			$provider = $calledClass::getInstance();
			
			return function($className) use ($provider)
			{
				return $provider->includeIfExists($className);
			};
		}
		
		final public function includeIfExists($className)
		{
			$file = $this->getPath().'/'.str_replace('\\', '/', $className).'.php';

			if (file_exists($file) && is_readable($file))
			{
				require_once($file);
				
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		
		abstract public function getPath();
	}
	
	class PficlLoader extends Autoload
	{
		public function getPath()
		{
			return dirname(dirname(dirname(__FILE__)));
		}
	}
}

?>