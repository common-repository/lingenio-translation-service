<?php

/*
* methods to use in your theme
* file is embedded in main plugin file
* methods are available to frontend
*/

require_once(__PG_ROOT__.'/pg_public/language_bar.php');
require_once(__PG_ROOT__.'/pg_requests/entranslate.php');

//echo language selection bar
//use echo = false for array with active language
//Array ( [active] => en [available] => Array ( [0] => de [1] => en [2] => fr ) ) 
function pgLanguageBar($echo = true)
{
	if ($echo)
		print_r(pg_print_language_selector(true, true));
	else
		return pg_print_language_selector(false, true);
}

//translate string to active language
//source will be considered german
//do not use for static content, results will not be cached, use for user input, i.e.
function pgTranslateToActive($value)
{
	$active_lang = pg_print_language_selector(false);
	if ('de' == $active_lang['active'])
		return $value;
	return pg_translation_selector($value, $active_lang['active']);
}

