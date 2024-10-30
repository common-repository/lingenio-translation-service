<?php

require_once(__PG_ROOT__.'/pg_config.php');

//$value: array, log Key: value...
function pg_logQuery($values)
{
	$handle = fopen(PG_DEBUG_FILE, "ab");
	
	if (!$handle)
		wp_die("Kann Debug Datei nicht &ouml;ffnen");
	
	fwrite($handle, "//******************************************//\r\n");
	fwrite($handle, date("d.m.Y - H:i", time()).'\r\n');
		
	foreach ($values as $key => $value) 
		fwrite($handle, "$key:\r\n $value\r\n --------- \r\n");
	
	fclose($handle);
}