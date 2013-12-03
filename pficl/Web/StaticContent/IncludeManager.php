<?php

namespace pficl\Web\StaticContent
{
	use \pficl\Fs\Util as Fs;
	use \pficl\Fp\Util as Fp;
	use \Autoload as Autoload;

	class IncludeManagerCannotReadFileException extends \Exception
	{
	}

	class IncludeManager
	{
		const ST_JS = 'js';
		const ST_CSS = 'css';

		private $storage;

		/** @return \pficl\Web\StaticContent\IncludeManager */
		public static function make($storage)
		{
			return new self($storage);
		}

		private function __construct($storage)
		{
			$this->storage = $storage;
		}

		public function getContentType()
		{
			if ($this->getStorage() == self::ST_JS)
			{
				return 'text/javascript';
			}
			elseif ($this->getStorage() == self::ST_CSS)
			{
				return 'text/css';
			}
			else
			{
				return NULL;
			}
		}

		final public function getStorage()
		{
			return $this->storage;
		}

		public function getStorageLocation()
		{
			return Autoload::PROJECT_ROOT_PATH.'/'.$this->getStorage();
		}

		final public function includeAll($recursive = FALSE)
		{
			$filter = function($name) { return !in_array($name, array('.', '..')); };

			if (!$recursive)
			{
				return $this->includeSimpleList(Fp::filter($filter, scandir($this->getStorageLocation())));
			}
			else
			{
				return $this->includeSimpleList(\pficl\Fs\Util::getFileList($this->getStorageLocation()));
			}
		}

		final public function includeSimpleList(array $list)
		{

			$storageLocation = $this->getStorageLocation();

			$map = function($a) use ($storageLocation)
			{
				// TODO: check isUri
				if (!Fs::isAbsolute($a))
				{
					return $storageLocation.'/'.$a;
				}
				else
				{
					return $a;
				}
			};

			$list = Fp::map($map, $list);
			
			foreach ($list as $key => $fileName)
			{
				if (is_readable($fileName))
				{
					$list[$key] = file_get_contents($fileName);
				}
				else
				{
					throw new IncludeManagerCannotReadFileException($fileName);
				}
			}

			return implode($this->getSeparator(), $list);
		}

		public function getSeparator()
		{
			return PHP_EOL;
		}
	}
}

?>
