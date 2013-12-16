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
		private $contentType = NULL;

		/** @return \pficl\Web\StaticContent\IncludeManager */
		public static function make($storage, $contentType = NULL)
		{
			return new self($storage, $contentType);
		}

		private function __construct($storage, $contentType)
		{
			$this->storage = $storage;
			$this->contentType = $contentType;
		}

		public function getContentType()
		{
			if ($this->contentType)
			{
				return $this->contentType;
			}
			elseif ($this->getStorage() == self::ST_JS)
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
			return Fs::isAbsolute($this->getStorage()) ? $this->getStorage() : Autoload::PROJECT_ROOT_PATH.'/'.$this->getStorage();
		}

		final public function includeAll($recursive = FALSE, $part = '')
		{
			$filter = function($name) { return !in_array($name, array('.', '..')); };

			if (!$recursive)
			{
				return $this->includeSimpleList(Fp::filter($filter, scandir($this->getStorageLocation().DIRECTORY_SEPARATOR.$part)));
			}
			else
			{
				return $this->includeSimpleList(\pficl\Fs\Util::getFileList($this->getStorageLocation().DIRECTORY_SEPARATOR.$part));
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
