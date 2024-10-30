<?php

require_once(__PG_ROOT__.'/pg_includes/textdomain.php'); 

function pg_tiny_to_english() 
{
	$screens = array( 'post', 'page' );
	foreach ($screens as $screen) 
	{
        add_meta_box(
			'pg_tiny_to_english_sectionid',
			__( 'Englische Version der Seite', 'pg_tiny_to_english_textdomain' ),
			'pg_tiny_to_english_inner_custom_box',
			$screen
		);
	}
}

function pg_tiny_to_french() 
{
	$screens = array( 'post', 'page' );
	foreach ($screens as $screen) 
	{
		add_meta_box(
			'pg_tiny_to_french_sectionid',
			__( 'Franz&ouml;sische Version der Seite', 'pg_tiny_to_french_textdomain' ),
			'pg_tiny_to_french_inner_custom_box',
			$screen
		);
	}
}

function pg_tiny_to_english_inner_custom_box( $post ) 
{
	$post_id = $post->ID;
	$input = '';
	$title = '';
	
	//check for en version of post, create custom meta
	add_post_meta($post_id, "pg_post_en", '', true);
	$input = get_post_meta($post_id, "pg_post_en", true); 
	
	add_post_meta($post_id, "pg_title_en", '', true);
	$title = get_post_meta($post_id, "pg_title_en", true); 
	
	//additional data for post; title, translate
	echo '<p><strong>Titel: </strong><input id="pg_title_en" name="pg_title_en" type="text" size="60" maxlength="60" value="'.$title.'"></p>';
	echo '<p><input type="checkbox" name="translate_to_en" value="translate_to_en"> Beim n&auml;chsten Speichern Seite automatisch ins Englische &uuml;bersetzen.</p>';
	echo '<p><select id="pg_domain_to_en" name="pg_domain_to_en">';
	pg_get_domains();
	echo '</select> Textdom&auml;ne w&auml;hlen</p>';
	
	//the tinymce, have fun
	wp_editor( $input, 'pg_en_editor', array( 'textarea_name' => 'pg_en_editor', 'media_buttons' => false ) );
}

function pg_tiny_to_french_inner_custom_box( $post ) 
{
	$post_id = $post->ID;
	$input = '';
	$title = '';
	
	//check for en version of post, create custom meta
	add_post_meta($post_id, "pg_post_fr", '', true);
	$input = get_post_meta($post_id, "pg_post_fr", true); 
	
	add_post_meta($post_id, "pg_title_fr", '', true);
	$title = get_post_meta($post_id, "pg_title_fr", true); 
	
	//additional data for post; title, translate
	echo '<p><strong>Titel: </strong><input id="pg_title_fr" name="pg_title_fr" type="text" size="60" maxlength="60" value="'.$title.'"></p>';
	echo '<p><input type="checkbox" name="translate_to_fr" value="translate_to_fr"> Beim n&auml;chsten Speichern Seite automatisch ins Franz&ouml;sische &uuml;bersetzen.</p>';
	echo '<p><select id="pg_domain_to_fr" name="pg_domain_to_fr">';
	pg_get_domains();
	echo '</select> Textdom&auml;ne w&auml;hlen</p>';
	
	//the tinymce, have fun
	wp_editor( $input, 'pg_fr_editor', array( 'textarea_name' => 'pg_fr_editor', 'media_buttons' => false ) );
}