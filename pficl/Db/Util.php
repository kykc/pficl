<?php

namespace pficl\Db
{
	abstract class Util
	{
		final public static function inpc(array $values)
		{
			return rtrim(str_repeat('?, ', count($values)), ', ');
		}

		final public static function fromMysqlClientXml($xml)
		{
			$doc = new DOMDocument('1.0');
			$doc->loadXML($xml);

			$rows = $doc->getElementsByTagName('row');

			$result = array();

			foreach ($rows as $row)
			{
				$rrow = array();

				foreach ($row->getElementsByTagName('field') as $field)
				{
					$rrow[$field->getAttribute('name')] = $field->nodeValue;
				}

				$result[] = $rrow;
			}

			return $result;
		}
	}
}

?>
