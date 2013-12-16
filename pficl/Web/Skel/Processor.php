<?php

namespace pficl\Web\Skel;

use \ArrayObject as ArrayObject;
use \pficl\Core\Trace;

final class Processor
{
	private function __construct()
	{
	}

	/** @return \pficl\Core\Trace */
	public static function process(array $actionList, ArrayObject $state, \pficl\Collection\SafeAccess $options)
	{
		$trace = Trace::make();
		$current = $actionList[$state->actionName];

		while (is_callable($current))
		{
			try
			{
				$name = $current($trace, $options);

				if (is_string($name) && array_key_exists($name, $actionList))
				{
					$current = $actionList[$name];
				}
				else
				{
					$current = NULL;
				}
			}
			catch (ActionFailedException $ex)
			{
				$current = NULL;
			}
		}

		return $trace;
	}
}

?>