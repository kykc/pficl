<?php

namespace pficl\Core;

interface IComparable
{
	public function areEqual(IComparable $subj);
}