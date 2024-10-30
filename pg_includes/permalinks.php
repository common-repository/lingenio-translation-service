<?php

function pg_get_the_permalink($id)
{
	if (isset($_REQUEST['lang']))
		$_SESSION['language'] = $_REQUEST['lang'];
	
	$language = $_SESSION['language'];
		
	if ('en' == $language)
		$id = pg_add_url_param($id, 'lang', 'en');
    elseif ('fr' == $language)
		$id = pg_add_url_param($id, 'lang', 'fr');
		
	return $id;
}

//sanitize url params, called from navigation.php as well
function pg_add_url_param($url, $param, $value)
{
	$query = parse_url($url, PHP_URL_QUERY);

	if (!isset($r[$param]))
		if( $query ) 
			$url .= '&'.$param.'='.$value;
		else
			$url .= '?'.$param.'='.$value;
			
	return $url;
}

