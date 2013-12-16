<?php

namespace pficl\Core
{
	class Exception extends \Exception
	{
	}

	class TemplateNotFound extends Exception
	{
	}

	class TemplateLocationNotFound extends Exception
	{
	}

	final class Template
	{
		private $name;
		private $location;

		/** @return \pficl\Core\Template */
		final public static function make($name, $location, $ext = 'php')
		{
			return new self($name, $location, $ext);
		}

		private function __construct($name, $location, $ext)
		{
			$this->name = $name.'.'.$ext;
			$this->location = rtrim($location, DIRECTORY_SEPARATOR);

			if (!is_array(file($this->location.DIRECTORY_SEPARATOR.$this->name, FILE_USE_INCLUDE_PATH)))
			{
				throw new TemplateNotFound($this->location.DIRECTORY_SEPARATOR.$this->name);
			}
		}

		public function getName()
		{
			return $this->name;
		}

		public function getLocation()
		{
			return $this->location;
		}

		public function process(array $data = array())
		{
			$data = TemplateData::make($this, $data);
			$_t = $data->tpl();
			$_d = $data->data();

			ob_start();

			require($this->location.DIRECTORY_SEPARATOR.$this->name);

			return ob_get_clean();
		}
	}
}

?>
