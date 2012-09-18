<?php

namespace pficl\Db
{
	abstract class Util
	{
		final public static function inpc(array $values)
		{
			return rtrim(str_repeat('?, ', count($values)), ', ');
		}
	}
}

?>
