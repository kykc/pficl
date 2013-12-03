<?php

namespace pficl\Web\Json
{
	class StdRequestParseFailedException extends \Exception
	{
	}

	class StdRequestInvalidFormatException extends \Exception
	{
	}

	class StdRequest
	{
		private $data;
		private $method;
		private $raw;

		/** @return \pficl\Web\Json\StdRequest */
		public static function make($val)
		{
			return new self($val);
		}

		public static function makeGet($fieldName = 'stdRequest')
		{
			return self::make(\pficl\Web\State::inst()->get()->sub($fieldName)->val());
		}

		public static function makePost($fieldName = 'stdRequest')
		{
			return self::make(\pficl\Web\State::inst()->post()->sub($fieldName)->val());
		}

		private function __construct($val)
		{
			if (is_string($val) && strlen($val) > 0)
			{
				$dec = json_decode($val, TRUE);

				if ($dec !== NULL)
				{
					$data = \pficl\Collection\SafeAccess::make($dec);

					if ($data->has('data'))
					{
						$this->data = $data->sub('data');
						
						$this->raw = $data;
						$this->method = $data->def(\pficl\Collection\DefaultValue::make())->sub('method')->val();

						return;
					}
				}
			}

			throw new StdRequestParseFailedException($val);
		}

		/** @return \pficl\Collection\SafeAccess */
		public function method()
		{
			return $this->method;
		}

		/** @return \pficl\Collection\SafeAccess */
		public function data()
		{
			return $this->data;
		}

		/** @return \pficl\Collection\SafeAccess */
		public function raw()
		{
			return $this->raw;
		}

		public function response($data)
		{
			return json_encode($data);
		}

		public static function mkGetString(array $rq, $field = 'stdRequest')
		{
			return 'stdRequest='.urlencode(json_encode(array('data' => $rq)));
		}
	}
}

?>
