<?php

require_once(__PG_ROOT__.'/pg_public/language_bar.php');
require_once(__PG_ROOT__.'/pg_requests/entranslate.php');

function pg_show_post_content($content)
{
	global $post;
	//print language bar
	//call first to set session variable for language
	if (isset($_REQUEST['lang']))
		$_SESSION['language'] = $_REQUEST['lang'];

	$language_sel = '';
	if (!('checked="checked"' == get_option('pg_show_selector')))
		$language_sel = pg_print_language_selector();

	//init vars
	$language = $_SESSION['language'];
	
	//build content
	$id = $post->ID;
	$post_meta = get_post_custom($id);
	
	//go for selected language
	if ('en' == $language)
		if (isset($post_meta['pg_post_en']))
			$content = $post_meta['pg_post_en'][0];
		else //get content on the fly, if selected
			if (get_option('pg_auto_translation_on_view') == 'checked="checked"')
			{
				$enversion = pg_get_en_translation(get_the_title($id), $content);
				$content = $enversion['text'];
				
				update_post_meta($id, 'pg_post_en', $content);
			}
	if ('fr' == $language)
		if (isset($post_meta['pg_post_fr']))
			$content = $post_meta['pg_post_fr'][0];
		else //get content on the fly, if selected
			if (get_option('pg_auto_translation_on_view') == 'checked="checked"')
			{
				$frversion = pg_get_fr_translation(get_the_title($id), $content);
				$content = $frversion['text'];
				
				update_post_meta($id, 'pg_post_fr', $content);
			}
	
	return $language_sel.'<p>'.$content.'</p>';
}

//show the excerpt, used for searches, etc. in some themes
function pg_show_the_excerpt($excerpt)
{
    global $post;
	//print language bar
	//call first to set session variable for language
	if (isset($_REQUEST['lang']))
		$_SESSION['language'] = $_REQUEST['lang'];

	$language_sel = '';
	if (!('checked="checked"' == get_option('pg_show_selector')))
		$language_sel = pg_print_language_selector();

	//init vars
	$language = $_SESSION['language'];
	
    //return the excerpt for german
    if ('de' == $language)
        return $language_sel.'<p>'.$excerpt.'</p>';
	
    //build content
	$id = $post->ID;
	$post_meta = get_post_custom($id);
    
    //build custom excerpt from fist 100 words
    $content = '';
    if ('en' == $language)
		if (isset($post_meta['pg_post_en']))
			$content = $post_meta['pg_post_en'][0];
		else //get content on the fly, if selected
			if (get_option('pg_auto_translation_on_view') == 'checked="checked"')
			{
				$enversion = pg_get_en_translation(get_the_title($id), $content);
				$content = $enversion['text'];
				
				update_post_meta($id, 'pg_post_en', $content);
			}
	if ('fr' == $language)
		if (isset($post_meta['pg_post_fr']))
			$content = $post_meta['pg_post_fr'][0];
		else //get content on the fly, if selected
			if (get_option('pg_auto_translation_on_view') == 'checked="checked"')
			{
				$frversion = pg_get_fr_translation(get_the_title($id), $content);
				$content = $frversion['text'];
				
				update_post_meta($id, 'pg_post_fr', $content);
			}
    
    
    
    return $language_sel.'<p>'.strip_tags(implode(' ', array_slice(explode(' ', $content), 0, 85))).' [...]</p>';
}