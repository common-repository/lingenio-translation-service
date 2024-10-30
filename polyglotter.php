<?php
/*
Plugin Name: Lingenio Translation Server Plugin
Plugin URI: https://ltp.schmersow-it.com/
Description: Create multilingual posts and translate your content automatically with Lingenio Translation Server
Version: 1.0
Author: Schmersow-IT UG haftungsbeschraenkt
Author URI: http://schmersow-it.de
License: GPLv2
*/

/*  
    Copyright 2013 Tobias Schmersow (email : info@schmersow-it.de)

	This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('__PG_ROOT__', dirname(__FILE__));

require_once(__PG_ROOT__.'/pg_admin/dashboard.php'); 
require_once(__PG_ROOT__.'/pg_admin/metabox.php'); 
require_once(__PG_ROOT__.'/pg_admin/save.php'); 
require_once(__PG_ROOT__.'/pg_admin/general.php'); 
require_once(__PG_ROOT__.'/pg_admin/settings.php'); 
require_once(__PG_ROOT__.'/pg_admin/biographie.php'); 

require_once(__PG_ROOT__.'/pg_public/content.php'); 
require_once(__PG_ROOT__.'/pg_public/titels.php'); 
require_once(__PG_ROOT__.'/pg_public/navigation.php'); 
require_once(__PG_ROOT__.'/pg_public/author_meta.php');
require_once(__PG_ROOT__.'/pg_public/search_translation.php');

require_once(__PG_ROOT__.'/pg_includes/permalinks.php');
require_once(__PG_ROOT__.'/pg_includes/feeds.php');

require_once(__PG_ROOT__.'/pg_widgets/custom_widgets.php');
require_once(__PG_ROOT__.'/pg_widgets/selector_widget.php');
require_once(__PG_ROOT__.'/pg_widgets/default_widgets_new.php');

require_once(__PG_ROOT__.'/pg_theme_functions.php');


//register styles and javascripts
add_action( 'wp_enqueue_scripts', 'pg_register_style' );  
add_action( 'admin_init', 'pg_register_style' );

function pg_list_active_languages()
{
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
	$retval = array('de', 'en');	
	if ('' != $sel_languages->de_fr)
		$retval[] = 'fr';
	if ('' != $sel_languages->de_es)
		$retval[] = 'es';
		
	return $retval;
}

//use to show only features of selected languages: array(de, en, fr, es)
$active_lang = pg_list_active_languages();

function pg_register_style()  
{  
    //styles
	wp_enqueue_style( 'pg_language_bar', plugin_dir_url(__FILE__).'/pg_language_bar.css' );  
    wp_enqueue_style( 'pg_admin', plugin_dir_url(__FILE__).'/pg_admin/pg_admin.css' ); 
    wp_enqueue_style( 'pg_widgets', plugin_dir_url(__FILE__).'/pg_widgets/widget_styles.css' ); 
    
    //java
    wp_enqueue_script('pg_quota', plugins_url('/pg_admin/pg_quota.js', __FILE__));
}  

//activate, deactive and uninstall plugin
register_activation_hook( __PG_ROOT__.'/activation.php', 'pg_activate_plugin' );
register_deactivation_hook( __PG_ROOT__.'/activation.php', 'pg_deactivate_plugin' );

//**************************************************//
//***************Backend actions********************//
//**************************************************//

//add dashboard widget, see dashboard.php
add_action( 'wp_dashboard_setup', 'pg_add_dashboard_widget' );

//actions to add meta boxes, see metabox.php
add_action( 'add_meta_boxes', 'pg_tiny_to_english' );
if (in_array('fr', $active_lang))
	add_action( 'add_meta_boxes', 'pg_tiny_to_french' );

//save data, see save.php
add_action( 'post_updated', 'pg_save_postdata' );

//set custom field to the general section for blog title and tagline, see general.php
add_action( 'admin_init', 'pg_title_settings' );

//add option page, see settings.php
add_action( 'admin_menu', 'pg_settings_add_menu' );

//multilingual user bios, see biographie.php
add_action( 'show_user_profile', 'pg_biographie_to_english' );
add_action( 'edit_user_profile', 'pg_biographie_to_english' );
if (in_array('fr', $active_lang))
{
	add_action( 'show_user_profile', 'pg_biographie_to_french' );
	add_action( 'edit_user_profile', 'pg_biographie_to_french' );
}

add_action( 'personal_options_update', 'pg_save_english_biographie' );
add_action( 'edit_user_profile_update', 'pg_save_english_biographie' );
if (in_array('fr', $active_lang))
{
	add_action( 'personal_options_update', 'pg_save_french_biographie' );
	add_action( 'edit_user_profile_update', 'pg_save_french_biographie' );
}

//**************************************************//

//**************************************************//
//**************Frontend actions********************//
//**************************************************//

//init session, see polyglotter.php
add_action( 'init', 'myStartSession', 1 );

//print content and language bar, see content.php
add_filter( 'the_content', 'pg_show_post_content' );
add_filter( 'the_excerpt', 'pg_show_the_excerpt' );

//print titles, see titles.php
add_filter( 'the_title', 'pg_show_post_title' );
add_filter( 'wp_title', 'pg_blog_title' );
add_filter( 'bloginfo', 'pg_blogtitle_name', 10, 2 );
add_filter( 'option_blogdescription', 'pg_blog_description' );
add_filter( 'home_url', 'pg_sanatize_home_url' );

//build new navi, see navigation.php ->used for wp_nav_menu
add_filter( 'wp_list_pages', 'pg_nav_menu', 10, 2 );

//previous/ next post link, see navigation.php
add_filter( 'previous_post_link', 'pg_adjecent_post_link', 10, 2 );
add_filter( 'next_post_link', 'pg_adjecent_post_link', 10, 2 );

//show author description, see author_bio.php
add_filter( 'the_author_description', 'pg_author_meta' );

//modifiy permalinks to have better seo, see permalink.php
add_filter( 'post_link', 'pg_get_the_permalink' );

//modify feed, see feeds.php, not completed yet
add_filter( 'feed_link', 'pg_sanatize_feed', 10, 1 );
add_filter( 'author_feed_link', 'pg_sanatize_author_feed', 10, 1 );

//set terms, categories, taxonomies, etc...
//add_filter( 'get_term', '' ); //see http://core.trac.wordpress.org/browser/tags/3.5.1/wp-includes/taxonomy.php#L0 line 900

//**************************************************//

//**************************************************//
//*******************Search*************************//
//**************************************************//

//translate the query, see search_translation.php
add_filter( 'posts_search', 'pg_posts_search' );

//**************************************************//

function myStartSession() 
{
	session_cache_limiter('private, must-revalidate');
	session_cache_expire(0);
	session_start();
	
	//used languages are: de, en, fr, es
	if(!isset($_SESSION['language']))
		$_SESSION['language'] = 'de';
}