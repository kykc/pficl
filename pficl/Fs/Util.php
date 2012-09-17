<?php

namespace pficl\Fs
{
	use \RecursiveDirectoryIterator as RecursiveDirectoryIterator;
	use \RecursiveIteratorIterator as RecursiveIteratorIterator;
	use \SplFileInfo as SplFileInfo;
	
	abstract class Util
	{
		final public static function getFileList($path, $filter = NULL)
		{
			$filter = $filter ? $filter : function($value) {return TRUE;};
			
			$directory = new RecursiveDirectoryIterator($path);
			$iterator = new RecursiveIteratorIterator($directory);
			$filtered = new FileFilterIterator($iterator, $filter);

			$list = array_map(function(SplFileInfo $f) {return $f->getPathName();}, iterator_to_array($filtered, FALSE));
			
			return $list;
		}
	}
}

?>