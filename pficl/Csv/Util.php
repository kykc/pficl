<?php

namespace pficl\Csv
{
	abstract class Util
	{
		final public static function toCsv(array $fields, $delimiter = ',', $enclosure = '"')
		{
			$buffer = fopen('php://temp', 'r+');
			$csv = '';
			$line = '';

			foreach ($fields as $row)
			{
				fputcsv($buffer, $row, $delimiter, $enclosure);
			}

			rewind($buffer);

			do
			{
				$line = fgets($buffer);
				$csv .= strval($line);
			}
			while ($line !== FALSE);

			fclose($buffer);

			return $csv;
		}
	}
}

?>
