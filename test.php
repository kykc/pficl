<?php

	require_once('include.php');
	
	date_default_timezone_set('Europe/Riga');
	
	$testsLocation = pficl\Core\Util::getTestsLocation();
    
	foreach (pficl\Fs\Util::getFileList($testsLocation) as $testFile)
	{
		require_once($testFile);
	}
    
	$classList = get_declared_classes();
    
	$filter = function($a)
	{
		return pficl\Core\Util::isInNamespace($a, 'pficlTests');
	};
    
	$testList = \pficl\Fp\Util::filter($filter, $classList);
    
	foreach ($testList as $test)
	{
		echo $test::make()->run()->makeCliTrace();
	}
    
?>
