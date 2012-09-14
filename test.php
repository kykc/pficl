<?php

	require_once('include.php');

	$id = \pficl\Core\Fp::id();
	
	echo $id('ZHOPA');
	
	echo \pficl\Core\Col::getFirst(array(3, 2, 1));
?>
