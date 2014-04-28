<?php

	function _jsdec()
	{
		return call_user_func_array('json_decode', func_get_args());
	}

	function _jsenc()
	{
		return call_user_func_array('json_encude', func_get_args());
	}

	function _fget()
	{
		return call_user_func_array('file_get_contents', func_get_args());
	}

	function _fput()
	{
		return call_user_func_array('file_put_contents', func_get_args());
	}

?>
