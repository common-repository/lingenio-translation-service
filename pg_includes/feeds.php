<?php

require_once(__PG_ROOT__.'/pg_includes/permalinks.php');

//not really done yet
function pg_sanatize_feed($output)
{
	if (isset($_REQUEST['lang']))
		$_SESSION['language'] = $_REQUEST['lang'];
	
	$language = $_SESSION['language'];
		
	if ('en' == $language)
		$output = pg_add_url_param($output, 'lang', 'en');
		
	return $output;
}

function pg_sanatize_author_feed()
{
}