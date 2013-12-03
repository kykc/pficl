<?php

	namespace pficl\Web\Route;

	use \pficl\Web\Json\StdRequest as JsonRequest;
	use \pficl\Web\StaticContent\IncludeManager;

	class IncludeHandler extends \pficl\Web\Route\Handler
	{
		private $method;
		private $storage;

		public static function mkFactory($method, $storage)
		{
			return function(Route $r) use ($method, $storage)
			{
				return static::make($r)->setMethod($method)->setStorage($storage)->process();
			};
		}

		public function setMethod($method)
		{
			$this->method = $method;

			return $this;
		}

		public function setStorage($storage)
		{
			$this->storage = $storage;

			return $this;
		}

		protected function handleValidRoute()
		{
			$rq = $this->method === 'get' ? JsonRequest::makeGet() : JsonRequest::makePost();

			$include = IncludeManager::make($this->storage);

			if ($include->getContentType())
			{
				header('Content-Type: '.$include->getContentType());
			}

			if ($rq->data()->has('fileList'))
			{
				print($include->includeSimpleList($rq->data()->sub('fileList')->val()));
			}
			elseif ($rq->data()->has('all-recursive') && $rq->data()->sub('all-recursive')->val())
			{
				print($include->includeAll(TRUE));
			}
		}
	}

?>