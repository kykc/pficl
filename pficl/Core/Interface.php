<?php

namespace pficl\Core;

interface IComparable
{
	public function areEqual(IComparable $subj);
}

interface ITrace
{
	public function addRecord($string);
	public function getRecordList();
}
