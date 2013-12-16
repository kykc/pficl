<?php

	function _map()
	{
		$args = func_get_args();

		if (count($args) > 1)
		{
			$args[0] = _l($args[0]);
		}

		return call_user_func_array(array('\pficl\Fp\Util', 'map'), $args);
	}

	function _filter($c, array $l)
	{
		return array_filter($l, _l($c));
	}

	function _all($c, array $l)
	{
		return \pficl\Fp\Util::all(_l($c), $l);
	}

	function _any($c, array $l)
	{
		return \pficl\Fp\Util::all(_l($c), $l);
	}

	function _kzip(array $k, array $v)
	{
		return array_combine($k, $v);
	}

	function _l($str)
	{
		if ($str instanceof \Closure)
		{
			return $str;
		}

		$l = explode('=>', $str);
		$args = array_shift($l);
		$body = implode('=>', $l);
		$exprList = explode(';', $body);
		$last = array_pop($exprList);
		$last = 'return '.$last.';';
		$exprList[] = $last;

		eval('$lambda = function('.$args.') {'.implode(';', $exprList).'};');

		return $lambda;
	}

	function _k(array $a)
	{
		return array_keys($a);
	}

	function _v(array $a)
	{
		return array_values($a);
	}

	function _join()
	{
		return call_user_func_array('implode', func_get_args());
	}

	function _split()
	{
		return call_user_func_array('explode', func_get_args());
	}

	function _merge()
	{
		return call_user_func_array('array_merge', func_get_args());
	}
?>