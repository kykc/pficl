<?php

namespace pficl\Core;

/**
 * Simple list, which can be passed like a trace without a hassle of adding &amp; and pass vanilla php array
 */
class Trace implements ITrace
{
	private $list = array();

	public function addRecord($string)
	{
		$this->list[] = $string;
	}

	public function getRecordList()
	{
		return $this->list;
	}

	private function __construct()
	{
	}

	/** @return \pficl\Core\Trace */
	public static function make()
	{
		return new static;
	}
}