<?php

//make the call to get the dictionary

function pg_get_dictionary()
{
	//the returned value has to look like this:
	$retval = array(array('id'=>'1', 'de'=>'Test Wert', 'en'=>'sample value', 'fr'=>'', 'es'=>''));
	
	return $retval;
}