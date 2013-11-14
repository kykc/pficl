<?php

namespace pficl\Cache\Encoding
{
	class GenericProcessor implements IProcessor
	{
		protected $method = NULL;
		protected $data = NULL;
		protected $isEncoded = NULL;

		/** @return \pficl\Cache\Encoding\GenericProcessor */
		public static function make()
		{
			return new self;
		}

		protected function __construct()
		{

		}

		public function getMethod()
		{
			return $this->method;
		}

		public function getData()
		{
			return $this->data;
		}

		public function isEncoded()
		{
			return $this->isEncoded === TRUE;
		}

		public function isDecoded()
		{
			return $this->isEncoded === FALSE;
		}

		public function loadEncoded($data, $method)
		{
			$this->data = $data;
			$this->isEncoded = TRUE;
			$this->method = $method;

			return $this;
		}

		public function loadDecoded($data)
		{
			$this->data = $data;
			$this->isEncoded = FALSE;

			return $this;
		}

		public function encode()
		{
			if ($this->isDecoded())
			{
				if ($this->isEligibleForNone())
				{
					$this->method = Util::METHOD_NONE;
					$this->isEncoded = TRUE;
					$this->data = Util::encode($this->data, Util::METHOD_NONE);
				}
				elseif ($this->isEligibleForJson())
				{
					$this->method = Util::METHOD_JSON;
					$this->isEncoded = TRUE;
					$this->data = Util::encode($this->data, Util::METHOD_JSON);
				}
				else
				{
					$this->method = Util::METHOD_PHP;
					$this->isEncoded = TRUE;
					$this->data = Util::encode($this->data, Util::METHOD_PHP);
				}
			}

			return $this;
		}

		protected function isEligibleForNone()
		{
			return is_string($this->data);
		}

		protected function isEligibleForJson()
		{
			if (!is_array($this->data) && !is_object($this->data))
			{
				return TRUE;
			}
			elseif (is_array($this->data))
			{
				$it = new \RecursiveIteratorIterator(new \pficl\Collection\RecursiveArrayOnlyIterator($this->data));

				foreach ($it as $key => $value)
				{
					if (is_object($key) || is_object($value))
					{
						return FALSE;
					}
				}

				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}

		public function decode()
		{
			if ($this->isEncoded())
			{
				$this->data = Util::decode($this->data, $this->method);
				$this->isEncoded = FALSE;
				$this->method = NULL;
			}

			return $this;
		}
	}
}

?>
