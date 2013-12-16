#!/usr/bin/php
<?php

	require_once(__DIR__.'/include.php');

	\pficl\Fp\Util::enableShortcuts();

	if (count($argv) !== 3)
	{
		print('Invalid args'.PHP_EOL);
		die();
	}

	$options = \pficl\Collection\SafeAccess::make(array(
				'location' => $argv[2],
				'mainName' => $argv[1],
				));

	$state = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);

	$actionList = \pficl\Web\Skel\ActionListProvider::simpleWebProjectSkeleton($state);

	$trace = \pficl\Web\Skel\Processor::process($actionList, $state, $options);

	echo implode(PHP_EOL, $trace->getRecordList());
	echo PHP_EOL;
?>