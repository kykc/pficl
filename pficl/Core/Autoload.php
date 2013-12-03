<?php

namespace pficl\Core
{
	abstract class Autoload
	{
		/** @return \pficl\Core\Autoload */
		final public static function getInstance()
		{
			return new static;
		}

		/** @return \pficl\Core\Autoload */
		final public static function inst()
		{
			return static::getInstance();
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
				$stdFileList = array('Util.php', 'Exception.php', 'Interface.php');
				
				foreach ($stdFileList as $fileName)
				{
					$fullName = dirname($file).'/'.$fileName;
					
					if (file_exists($fullName) && is_readable($fullName))
					{
						require_once($fullName);
					}
				}

				require_once($file);
				
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}

		/** @return \pficl\Db\Manager */
		public function defaultDb()
		{
			return \pficl\Db\Manager::make(\pficl\Db\DefaultCredentials::make(static::DB_NAME));
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