<?php

namespace pficl\Model\Deprecated
{
	abstract class Normalization
	{
		public static function int()
		{
			return function($val)
			{
				return intval($val);
			};
		}

		public static function float($nullable = FALSE)
		{
			return function($val) use ($nullable)
			{
				if ($val === NULL && $nullable)
				{
					return NULL;
				}
				else
				{
					return floatval($val);
				}
			};
		}

		public static function string($limit = 0)
		{
			return function($val) use($limit)
			{
				if ($limit > 0 && strlen(strval($val)) > $limit)
				{
					return substr(strval($val), 0, $limit);
				}
				else
				{
					return strval($val);
				}
			};
		}

		public static function datetime($nullable = FALSE)
		{
			return function($val) use ($nullable)
			{
				if (is_null($val) || !$val || $val == '0000-00-00 00:00:00' || $val == '0000-00-00')
				{
					if ($nullable)
					{
						return NULL;
					}
					else
					{
						return '0000-00-00 00:00:00';
					}
				}
				else
				{
					return date('Y-m-d H:i:s', strtotime($val));
				}
			};
		}

		public static function bool($nullable = FALSE)
		{
			return function($val) use ($nullable)
			{
				if ($nullable && is_null($val))
				{
					return NULL;
				}
				else
				{
					return $val ? TRUE : FALSE;
				}
			};
		}
	}
}

?>
