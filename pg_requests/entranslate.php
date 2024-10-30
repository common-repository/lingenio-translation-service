<?php

require_once(__PG_ROOT__.'/pg_requests/http_request.php'); 
require_once(__PG_ROOT__.'/pg_includes/pg_functions.php'); 
require_once(__PG_ROOT__.'/pg_includes/pg_debug.php'); 
require_once(__PG_ROOT__.'/pg_config.php'); 

//called to translate any text german-englisch
//title can be an empty string

function pg_translation_selector($value, $target)
{
	$retval = array();
	switch ($target) 
	{
		case 'en':
			$retval = pg_get_en_translation('', $value);
			break;
		case 'fr':
			$retval = pg_get_fr_translation('', $value);
			break;
		case 'es':
			$retval = pg_get_es_translation('', $value);
			break;
		default:
			$retval = array('title'=>'', 'text'=>$value);
			break;
	}
	
	return $retval['text'];
}

//build for searching, choose src and target language
function pg_short_translation($text, $src, $trg)
{
	$retval = pg_lts_connector('', $text, $trg, 'general', $src, 50, true);
	if (!$retval)
		return '';
	else
		return $retval['text'];
}

function pg_get_en_translation($title, $text, $domain='general')
{
	return pg_lts_connector($title, $text, 'en', $domain);
}

function pg_get_fr_translation($title, $text, $domain='general')
{
	return pg_lts_connector($title, $text, 'fr', $domain);
}

function pg_get_es_translation($title, $text, $domain='general')
{
	return pg_lts_connector($title, $text, 'es', $domain);
}

//build query, open post request, handle result
function pg_lts_connector($title, $text, $language, $domain, $src = 'de', $timelimit = PG_TIMELIMIT, $ignore_errors = false)
{
	//$language: en, fr, es
	if (!(in_array($language, array('de', 'en','fr','es'))))
		wp_die('Zielsprache nicht verf&uuml;gbar');
	
	$query = array(
				'src'=>$src, 
				'trg'=>$language, 
				"dom"=>$domain, 
				"uid"=>"", 
				"tid"=>"", 
				"format"=>"html", 
				"tr_options"=>array(
								"timelimit"=>$timelimit,
								"tr_alt"=>array(
										"sent"=>true,
										"word"=>array(
													"limit"=>PG_ALTCOUNT,
													"factor"=>PG_ALTCOUNT,
													"wordmax"=>PG_ALTCOUNT,
													"statdict"=>array(
																"show"=>true,
																"mark"=>true
														)
											)
									),
								"dicts"=>array(array("name"=>"opt1")), 
								"de"=>array("split_compounds"=>true)
								)
			);
	$query = json_encode($query);
	
	//put title in front of text an remove it later
	$html = '<h1>'.$title.'</h1>'.$text;
	
	$result = pg_postRequest($query.$html);
	
	//debugging
	if (PG_DEBUG)
		pg_logQuery(array("Query"=>$query, "Data"=>$html, "Result"=>$result));
	
	//test result and process it
	if (!$result && !$ignore_errors)
		wp_die('&Uuml;bersetzung fehlgeschlagen');
	else if (!$result && $ignore_errors)
		return false;
	
	$title = multi_between('<h1>', '</h1>', $result);
	
	return array("title" => $title[1][0], "text" => str_replace('<h1>'.$title[1][0].'</h1>', '', $result));
}