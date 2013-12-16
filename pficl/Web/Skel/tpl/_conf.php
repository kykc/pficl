<?php

	error_reporting(E_STRICT | E_ALL);
	define('PROJECT_NAME', '<<<mainName>>>');

	require_once('pficl/include.php');

	if (file_exists(__DIR__.'/.local.config.php'))
	{
		require_once(__DIR__.'/.local.config.php');
	}

	class Autoload extends pficl\Core\Autoload
	{
		private $cacheStorage = NULL;
		private $sessionStorage = NULL;

		const PROJECT_ROOT_PATH = __DIR__;
		const DB_NAME = PROJECT_NAME;

		public function getPath()
		{
			return __DIR__.'/lib';
		}

		public function getWebHandlerNamespace()
		{
			return '\\'.PROJECT_NAME.'\\Handler\\';
		}

		public function getProjectRootPath()
		{
			return self::PROJECT_ROOT_PATH;
		}

		public function getDbName()
		{
			return self::DB_NAME;
		}

		/**
		 * Returns router 
		 * @return string
		 */
		public function getRouterLocation()
		{
			return 'master.php';
		}

		/**
		 * Determines web location of project root
		 * @return string
		 */
		public function webRoot()
		{
			$routerLocation = $this->getRouterLocation();
			$scriptLocation = $_SERVER['SCRIPT_FILENAME'];
			$documentRoot = $_SERVER['DOCUMENT_ROOT'];

			$relativeRouterLocation = rtrim(str_replace($routerLocation, '', str_replace($documentRoot, '', $scriptLocation)), '/');

			return $relativeRouterLocation;
		}

		/** 
		 * Returns object representing current http request
		 * @return \pficl\Web\State
		 */
		public function webState()
		{
			return \pficl\Web\State::inst();
		}

		/**
		 * Generates absolute path (without protocol and hostname) for a route within a project
		 * @param string $str relative route path
		 * @return string absolute route
		 */
		public function webLink($str)
		{
			return $this->webRoot().'/'.ltrim($str, '/');
		}

		/**
		 * Generates absolute path to file within project directory
		 * @param string $str relative path
		 * @return string absolute path to file
		 */
		public function fsLink($str)
		{
			return $this->getProjectRootPath().DIRECTORY_SEPARATOR.$str;
		}

		/**
		 * Generates link to file from directly accessible file storage (configurable via local config)
		 * @param string $str relative path to file
		 */
		public function webFileLink($str)
		{

		}

		/** @return \pficl\Cache\IStorage */
		public function cacheStorage()
		{
			if ($this->cacheStorage === NULL)
			{
				$this->cacheStorage = \pficl\Cache\MysqlStorage::make($this->defaultDb(), 'cacheStorage');
			}

			return $this->cacheStorage;
		}

		/** @return \pficl\Web\Session */
		public function session()
		{
			if ($this->sessionStorage === NULL)
			{
				$this->sessionStorage = \pficl\Cache\MysqlStorage::make($this->defaultDb(), 'sessionStorage');
			}

			return \pficl\Web\Session::make($this->sessionStorage, 100, static::DB_NAME.'_'.gethostname());
		}

		public function defJs(array $rules = array())
		{
			$default = array('folderListRecursive' => array('js/tp', 'js/lib'));

			return $this->webLink('static/js?'.\pficl\Web\Json\StdRequest::mkGetString(array_merge($default, $rules)));
		}

		public function defCss(array $rules = array())
		{
			$default = array('folderListRecursive' => array('css/tp', 'css/lib'));

			return $this->webLink('static/css?'.\pficl\Web\Json\StdRequest::mkGetString(array_merge($default, $rules)));
		}
	}

	if (isset($error_handler) && is_callable($error_handler))
	{
		set_error_handler($error_handler, E_STRICT | E_ALL);
	}

	spl_autoload_register(Autoload::getCallable());

	gc_enable();

	\pficl\Fp\Util::enableShortcuts();
	\pficl\Core\Util::enableShortcuts();

	/** @return \Autoload */
	function _meta()
	{
		return \Autoload::inst();
	}
?>
