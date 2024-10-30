<?php

function pg_print_language_selector($html = true, $home_url = false)
{
	if (isset($_REQUEST['lang']))
		$_SESSION['language'] = $_REQUEST['lang'];
	
	$language = $_SESSION['language'];
	
	$image_path = dirname(plugin_dir_url(__FILE__)).'/pg_image/';

    //set default page value
    $page = site_url()."?";
      
    if (in_the_loop())
        $page = get_post_permalink()."&";

    if ($home_url)
        $page = site_url()."?";
	    
	$navigation = '<div class="pg_language_bar">';
	
	$sel_languages = get_option('pg_selected_languages');
	if (!$sel_languages) //init default language
	{
		$json = json_encode(array('de_fr' => '', 'de_es' => ''));
		update_option('pg_selected_languages', $json);
		$sel_languages = js_decode($json);
	}
	else
		$sel_languages = json_decode($sel_languages);
	
	//print all languages
	$print_lang = array('de', 'en');	
	if ('' != $sel_languages->de_fr)
		$print_lang[] = 'fr';
	if ('' != $sel_languages->de_es)
		$print_lang[] = 'es';
	
	//build selection bar, exclude active language
	foreach ($print_lang as $lang)
		if ($language != $lang)
			$navigation .= '<span style="float:left;"><a class="pg_language_ref" href="'.$page.'lang='.$lang.'" rel="nofollow"><img class="pg_language_ref_img" src="'.$image_path.$lang.'.png" width="20px;" heights="20px;"/></a></span>';
	
	if ($html)
		return $navigation.'</div><div style="clear:both;"></div>';
	
	return array('active'=>$language, 'available'=>$print_lang);
}
