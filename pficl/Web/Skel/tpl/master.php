<?php

	$projectName = '<<<mainName>>>';

	use \pficl\Web\State as State;
	use \pficl\Web\Route\Walk as Walk;
	use \pficl\Web\Route\IncludeHandler;

	require_once($projectName.'/_conf.php');

	$notFound = function(\pficl\Web\Route\Route $route)
	{
		print(State::inst()->route()->toJson());
		print('<br>');
		print('404');
		echo '<pre>';
		print_r($_SERVER);
	};

	$test = function(\pficl\Web\Route\Route $route)
	{
		echo '<pre>'.PHP_EOL;
		echo Autoload::inst()->defCss().PHP_EOL;
		echo Autoload::inst()->defJs().PHP_EOL;
		echo 'It\'s a test';
	};

	$handlers = array(
			'CssInclude' => IncludeHandler::mkFactory('get', '', 'text/css'),
			'JsInclude' => IncludeHandler::mkFactory('get', '', 'text/javascript'),
			'NotFound' => $notFound,
			'Test' => $test,
			);

	$routingTable = json_decode(file_get_contents(Autoload::PROJECT_ROOT_PATH.'/route/route.js', TRUE), TRUE);
	$route = State::inst()->route();

	Walk::handleRoute(State::inst()->route(), $routingTable, $handlers);
?>
