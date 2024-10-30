<?php

require_once(__PG_ROOT__.'/pg_requests/entranslate.php'); 

function pg_title_settings()
{
	//get selected languages
	$active_lang = pg_list_active_languages();
	
	//add english settings
	add_settings_field(
		'pg_en_blogname_op', // id
		'Englischer Titel des Blogs', // setting title
		'pg_en_title_input', // display callback
		'general', // settings page
		'default' // settings section
	);
	
	register_setting(
		'general', // settings page
		'pg_en_blogname_op' // option name
	);
	
	add_settings_field(
		'pg_en_tagline_op', // id
		'Englischer Untertitel des Blogs', // setting title
		'pg_en_tagline_input', // display callback
		'general', // settings page
		'default' // settings section
	);
	
	register_setting(
		'general', // settings page
		'pg_en_tagline_op' // option name
	);
	
	//add french settings, if selected
	if (in_array('fr', $active_lang))
	{
		add_settings_field(
			'pg_fr_blogname_op', // id
			'Franz&ouml;sischer Titel des Blogs', // setting title
			'pg_fr_title_input', // display callback
			'general', // settings page
			'default' // settings section
		);
		
		register_setting(
			'general', // settings page
			'pg_fr_blogname_op' // option name
		);
		
		add_settings_field(
			'pg_fr_tagline_op', // id
			'Franz&ouml;sischer Untertitel des Blogs', // setting title
			'pg_fr_tagline_input', // display callback
			'general', // settings page
			'default' // settings section
		);
		
		register_setting(
			'general', // settings page
			'pg_fr_tagline_op' // option name
		);
	}
}

//english
function pg_en_title_input() {
    
	//get en blog title
	$value = get_option('pg_en_blogname_op');
	
    // echo the field
    ?> <input id='pg_en_blogname_op' name='pg_en_blogname_op' type='text' value='<?php echo esc_attr( $value ); ?>' size="60" /> <?php
}

function pg_en_tagline_input() {
    
	//get en blog title
	$value = get_option('pg_en_tagline_op');
	
    // echo the field
    ?> <input id='pg_en_tagline_op' name='pg_en_tagline_op' type='text' value='<?php echo esc_attr( $value ); ?>' size="60" /> <?php
}

function pg_english_title_translation()
{
	$translation = pg_get_en_translation(get_bloginfo('name'), get_bloginfo('description'));
	update_option('pg_en_blogname_op', $translation['title']);
	update_option('pg_en_tagline_op', $translation['text']);
}

//french
function pg_fr_title_input() {
    
	//get en blog title
	$value = get_option('pg_fr_blogname_op');
	
    // echo the field
    ?> <input id='pg_fr_blogname_op' name='pg_fr_blogname_op' type='text' value='<?php echo esc_attr( $value ); ?>' size="60" /> <?php
}

function pg_fr_tagline_input() {
    
	//get en blog title
	$value = get_option('pg_fr_tagline_op');
	
    // echo the field
    ?> <input id='pg_fr_tagline_op' name='pg_fr_tagline_op' type='text' value='<?php echo esc_attr( $value ); ?>' size="60" /> <?php
}

function pg_french_title_translation()
{
	$translation = pg_get_fr_translation(get_bloginfo('name'), get_bloginfo('description'));
	update_option('pg_fr_blogname_op', $translation['title']);
	update_option('pg_fr_tagline_op', $translation['text']);
}