<?php

	namespace pficl\Web\Route;

	use \pficl\Web\Json\StdRequest as JsonRequest;
	use \pficl\Web\StaticContent\IncludeManager;

	class IncludeHandler extends \pficl\Web\Route\Handler
	{
		private $method;
		private $storage;
		private $contentType;

		public static function mkFactory($method, $storage, $contentType)
		{
			return function(Route $r) use ($method, $storage, $contentType)
			{
				return static::make($r)->setMethod($method)->setStorage($storage)->setContentType($contentType)->process();
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

		public function setContentType($type)
		{
			$this->contentType = $type;

			return $this;
		}

		protected function handleValidRoute()
		{
			$rq = $this->method === 'get' ? JsonRequest::makeGet() : JsonRequest::makePost();

			$include = IncludeManager::make($this->storage, $this->contentType);

			if ($include->getContentType())
			{
				header('Content-Type: '.$include->getContentType());
			}

			if ($rq->data()->has('folderListRecursive'))
			{
				foreach ($rq->data()->sub('folderListRecursive')->val() as $folder)
				{
					print($include->includeAll(TRUE, $folder));
				}
			}

			if ($rq->data()->has('fileList'))
			{
				print($include->includeSimpleList($rq->data()->sub('fileList')->val()));
			}
		}
	}

?>