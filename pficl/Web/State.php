<?php

namespace pficl\Web
{
	use \pficl\Web\Route\Route as Route;

	class NotWebRequestException extends \Exception
	{
	}

	class NotRedirectException extends \Exception
	{
	}

	class State
	{
		private static $inst = NULL;

		private $post;
		private $get;
		private $route;
		private $rawPost;
		private $request;

		/** @return \pficl\Web\State */
		public static function inst()
		{
			if (self::$inst === NULL)
			{
				self::$inst = new self;
			}

			return self::$inst;
		}

		private function __construct()
		{
			$this->post = \pficl\Collection\SafeAccess::make($_POST);
			$this->get = \pficl\Collection\SafeAccess::make($_GET);
			$this->request = \pficl\Collection\SafeAccess::make($_REQUEST);
			$this->route = Route::makeCurrent();
			$this->rawPost = \pficl\Collection\SafeAccess::make($GLOBALS, \pficl\Collection\DefaultValue::make())->sub('HTTP_RAW_POST_DATA')->val();
		}

		/** @return \pficl\Collection\SafeAccess */
		public function rq()
		{
			return $this->request;
		}

		/** @return \pficl\Collection\SafeAccess */
		public function get()
		{
			return $this->get;
		}

		/** @return \pficl\Collection\SafeAccess */
		public function post()
		{
			return $this->post;
		}

		/** @return \pficl\Collection\SafeAccess */
		public function rawPost()
		{
			return $this->rawPost;
		}

		/** @return \pficl\Web\Route\Route */
		public function route()
		{
			return $this->route;
		}

		public function forceWeb()
		{
			if (!is_array($_SERVER))
			{
				throw new NotWebRequestException();
			}

			return $this;
		}

		public function forceRedirect()
		{
			if (!array_key_exists('REDIRECT_URL', $_SERVER))
			{
				throw new NotRedirectException();
			}

			return $this;
		}
	}
}

?>
