<?php

namespace pficl\Web\Skel;

use \ArrayObject as ArrayObject;
use \pficl\Core\Trace;
use \pficl\Collection\SafeAccess;
use \pficl\Collection\DefaultValue;

abstract class ActionListProvider
{
	public static function simpleWebProjectSkeleton(ArrayObject $state)
	{
		$actionList = array();

		$actionList['downloadAssets'] = function(Trace $t, SafeAccess $options) use ($state)
		{
			$t->addRecord('Downloading assets');

			$list = [
					'http://code.jquery.com/jquery-1.10.2.min.js',
					'https://raw.github.com/jashkenas/underscore/master/underscore-min.js',
					'https://raw.github.com/josscrowcroft/accounting.js/master/accounting.min.js',
					'https://raw.github.com/douglascrockford/JSON-js/master/json2.js'
					];

			$files = _kzip($list, _map('$a => file_get_contents($a)', $list));

			if (_all('$a => $a !== FALSE', $files))
			{
				$state->assets['js'] = $files;

				return 'prepareTemplates';
			}
			else
			{
				$t->addRecord('Failed to download assets');
				throw new ActionFailedException();
			}
		};

		$actionList['prepareTemplates'] = function(Trace $t, SafeAccess $options) use ($state)
		{
			$t->addRecord('Preparing templates');

			$list = ['_conf.php', 'web/master.php', 'lib/'.$options->sub('mainName')->val().'/Handler/MainPage.php'];

			$action = function($a) use ($options, $t)
			{
				$path = PFICL_PATH.'/pficl/Web/Skel/tpl/'.basename($a);
				$contents = file_get_contents($path);

				if ($contents !== FALSE)
				{
					foreach ($options->val() as $key => $value)
					{
						$contents = str_replace('<<<'.$key.'>>>', $value, $contents);
					}

					return $contents;
				}
				else
				{
					$t->addRecord('Failed to fetch template ['.$path.']');
					throw new ActionFailedException();
				}
			};

			$files = _kzip($list, _map($action, $list));

			if (_all('$a => is_string($a) && strlen($a) > 0', $files))
			{
				$state->assets['tpl'] = $files;

				return 'checkConfig';
			}
			else
			{
				$t->addRecord('Failed to create project content from templates');
				throw new ActionFailedException();
			}
		};

		$actionList['checkConfig'] = function(Trace $t, SafeAccess $options) use ($state)
		{
			$configStr = json_encode($options->val(), JSON_PRETTY_PRINT);

			$t->addRecord('Checking config: ');
			$t->addRecord($configStr);

			$assert = function($name, $condition) use ($options, $t)
			{
				$l = _l($condition);

				if (!$l($options->def(DefaultValue::make(NULL))->sub($name)->val()))
				{
					$t->addRecord('Invalid config option ['.$name.']');
					throw new ActionFailedException();
				}
			};

			$assert('mainName', '$a => is_string($a) && strlen($a) > 0');
			$assert('location', '$a => is_string($a) && strlen($a) > 0');

			return 'checkLocation';
		};

		$actionList['checkLocation'] = function(Trace $t, SafeAccess $options) use ($state)
		{
			$t->addRecord('Checking project location');

			$location = $options->sub('location')->val();

			$parent = dirname($location);

			if (is_dir($location) && is_writable($location))
			{
				return 'createFolderStructure';
			}
			elseif (is_dir($parent) && is_writable($parent) && !file_exists($location))
			{
				return 'createParentFolder';
			}
			else
			{
				$t->addRecord('Project location missing or not writable');
				throw new ActionFailedException();
			}
		};

		$actionList['createParentFolder'] = function(Trace $t, SafeAccess $options) use ($state)
		{
			$t->addRecord('Creating project location');

			if (mkdir($options->sub('location')->val(), 0755))
			{
				return 'createFolderStructure';
			}
			else
			{
				$t->addRecord('Failed to create project location');
				throw new ActionFailedException();
			}
		};

		$actionList['createFolderStructure'] = function(Trace $t, SafeAccess $options) use ($state)
		{
			$c = function($path) use ($t, $options)
			{
				if (!mkdir($options->sub('location')->val().DIRECTORY_SEPARATOR.$path, 0755))
				{
					$t->addRecord('Failed to create directory ['.$path.']');
					throw new ActionFailedException();
				}
			};

			$t->addRecord('Creating folder structure');

			$name = $options->sub('mainName')->val();

			$c('js/');
			$c('js/lib/');
			$c('js/tp/');
			$c('css/');
			$c('css/lib/');
			$c('css/tp/');
			$c('lib/');
			$c('lib/'.$name.'/');
			$c('lib/'.$name.'/Handler/');
			$c('tpl/');
			$c('tpl/Screen/');
			$c('web/');
			$c('route/');

			return 'thirdPartyJs';
		};

		$actionList['thirdPartyJs'] = function(Trace $t, SafeAccess $options) use ($state)
		{
			$t->addRecord('Placing third-party JS libraries');

			foreach ($state->assets['js'] as $link => $content)
			{
				$name = basename($link);
				$path = $options->sub('location')->val().'/js/tp/'.$name;

				if (file_put_contents($path, $content) === FALSE)
				{
					$t->addRecord('Failed to write file ['.$path.']');
					throw new ActionFailedException();
				}
			}

			return 'createRoute';
		};

		$actionList['createRoute'] = function(Trace $t, SafeAccess $options) use ($state)
		{
			$t->addRecord('Creating route file');

			$route = [
				["equals", "", "class/MainPage"],
				["equals", "/static/js", "lambda/JsInclude"],
				["equals", "/static/css", "lambda/CssInclude"],
				["equals", "/test", "lambda/Test"],
				["default", "lambda/NotFound"]
			];

			$filename = $options->sub('location')->val().'/route/route.js';

			if (file_put_contents($filename, json_encode($route, JSON_PRETTY_PRINT)) === FALSE)
			{
				$t->addRecord('Failed to write route file');
				throw new ActionFailedException();
			}
			else
			{
				return 'writeTemplates';
			}
		};

		$actionList['writeTemplates'] = function(Trace $t, SafeAccess $options) use ($state)
		{
			$t->addRecord('Writing templates');

			foreach ($state->assets['tpl'] as $path => $content)
			{
				if (file_put_contents($options->sub('location')->val().'/'.$path, $content) === FALSE)
				{
					$t->addRecord('Failed to write file ['.$path.']');
					throw new ActionFailedException();
				}
			}
		};

		$state->actionName = 'downloadAssets';
		$state->assets = array();

		return $actionList;
	}
}

