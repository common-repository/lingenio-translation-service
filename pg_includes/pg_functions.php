<?php
/*
*	functions for daily use :)
*
*
*/

//get array of string between to substrings, i.e. <p>my string</p>
function multi_between($start, $stop, $string)
{
	$code = preg_match_all("~".$start."(.*?)".$stop."~si", $string, $matches);
	return $matches;
} 