<?php

require_once(__PG_ROOT__.'/pg_requests/entranslate.php');

function pg_author_meta($field)
{
	if (isset($_REQUEST['lang']))
		$_SESSION['language'] = $_REQUEST['lang'];
	
	$language = $_SESSION['language'];

	//go for selected language
	if ('en' == $language)
		if ('' != get_the_author_meta('pg_en_bio'))
			$field = get_the_author_meta('pg_en_bio');
	if ('fr' == $language)
		if ('' != get_the_author_meta('pg_fr_bio'))
			$field = get_the_author_meta('pg_fr_bio');
		
	
	return $field;
}