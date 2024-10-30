<?php

require_once(__PG_ROOT__.'/pg_requests/entranslate.php');
require_once(__PG_ROOT__.'/pg_includes/permalinks.php');


//set post title
function pg_show_post_title($title, $id = null)
{
	if (isset($_REQUEST['lang']))
		$_SESSION['language'] = $_REQUEST['lang'];
		
	//init vars
	
	$language = $_SESSION['language'];
		
	//go for selected language
	if (in_the_loop())
		$post_meta = get_post_custom($id);
		if ('en' == $language)
			if (isset($post_meta['pg_title_en']))
				$title = $post_meta['pg_title_en'][0];
			else //get content on the fly, if selected
				if (get_option('pg_auto_translation_on_view') == 'checked="checked"')
				{
					$enversion = pg_get_en_translation(get_the_title($id), '');
					$title = $enversion['text'];
				
					update_post_meta($id, 'pg_title_en', $title);
				}
		if ('fr' == $language)
			if (isset($post_meta['pg_title_fr']))
				$title = $post_meta['pg_title_fr'][0];
			else //get content on the fly, if selected
				if (get_option('pg_auto_translation_on_view') == 'checked="checked"')
				{
					$frversion = pg_get_fr_translation(get_the_title($id), '');
					$title = $frversion['text'];
				
					update_post_meta($id, 'pg_title_fr', $title);
				}
		
	return $title;
}

//set blog title, in browser head
function pg_blog_title($title, $sep = '|')
{
	global $paged, $page;
	
	if (isset($_REQUEST['lang']))
		$_SESSION['language'] = $_REQUEST['lang'];
		
	//init vars
	$language = $_SESSION['language'];
	
	//go for selected language
	if ('en' == $language)
		if (get_option('pg_en_blogname_op'))
		{
			if (is_feed())
				return get_option('pg_en_blogname_op');

			// Add the site name.
			$title .= get_option('pg_en_blogname_op');

			// Add the site description for the home/front page.
			$site_description = get_option('pg_en_tagline_op');
			if ($site_description && (is_home() || is_front_page()))
				$title = "$title $sep $site_description";

			// Add a page number if necessary.
			if ( $paged >= 2 || $page >= 2 )
				$title = "$title $sep ".sprintf(__( 'Page %s', 'twentytwelve' ), max($paged, $page));
		}
	if ('fr' == $language)
		if (get_option('pg_fr_blogname_op'))
		{
			if (is_feed())
				return get_option('pg_fr_blogname_op');

			// Add the site name.
			$title .= get_option('pg_fr_blogname_op');

			// Add the site description for the home/front page.
			$site_description = get_option('pg_fr_tagline_op');
			if ($site_description && (is_home() || is_front_page()))
				$title = "$title $sep $site_description";

			// Add a page number if necessary.
			if ( $paged >= 2 || $page >= 2 )
				$title = "$title $sep ".sprintf(__( 'Page %s', 'twentytwelve' ), max($paged, $page));
		}	

	return $title.' ';
}

//set blog title for description: name
function pg_blogtitle_name($title, $show)
{
	if (isset($_REQUEST['lang']))
		$_SESSION['language'] = $_REQUEST['lang'];
	
	//init vars
	$language = $_SESSION['language'];
	
	//leave if request for wrong data
	if ('name' != $show)
		return $title;
	
	//go for selected language
	if ('en' == $language)
		if (get_option('pg_en_blogname_op'))
			$title = get_option('pg_en_blogname_op');
	if ('fr' == $language)
		if (get_option('pg_fr_blogname_op'))
			$title = get_option('pg_fr_blogname_op');
		
	
	return $title;
}

//set blog description
function pg_blog_description($info)
{
	if (isset($_REQUEST['lang']))
		$_SESSION['language'] = $_REQUEST['lang'];
	
	//init vars
	$language = $_SESSION['language'];
	
	//go for selected language
	if ('en' == $language)
		if (get_option('pg_en_tagline_op'))
			$info = get_option('pg_en_tagline_op');
	if ('fr' == $language)
		if (get_option('pg_fr_tagline_op'))
			$info = get_option('pg_fr_tagline_op');
		
				
	return $info;
}

function pg_sanatize_home_url($url)
{
	if (isset($_REQUEST['lang']))
		$_SESSION['language'] = $_REQUEST['lang'];
	
	//init vars
	$language = $_SESSION['language'];
		
	return $url;
}