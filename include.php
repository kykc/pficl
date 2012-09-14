<?php

	require_once(__DIR__.'/pficl/Core/Autoload.php');
	
	spl_autoload_register(\Pficl\Core\PficlLoader::getCallable());

?>