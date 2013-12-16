<?php

namespace pficl\Web;

final class Validator
{
	private $errorList = array();

	/** @return \pficl\Web\Validator */
	public static function make()
	{
		return new static;
	}

	private function __construct()
	{
	}

	/** @return \pficl\Web\Validator */
	public function assert($result, $errorMessage)
	{
		if (!$result)
		{
			$this->errorList[] = $errorMessage;
		}

		return $this;
	}

	/** @return \pficl\Web\Validator */
	public function nonEmpty($str, $errorMessage)
	{
		if (!(is_string($str) && strlen($str) > 0))
		{
			$this->errorList[] = $errorMessage;
		}

		return $this;
	}

	public function getErrorList()
	{
		return $this->errorList;
	}

	public function hasErrors()
	{
		return count($this->errorList) > 0;
	}
}

