<?php

require_once(__PG_ROOT__.'/pg_requests/entranslate.php'); 

function pg_save_postdata( $post_id ) 
{
	// If it is our form has not been submitted, so we dont want to do anything
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	  return;
        
	// Check permissions
	if ( isset($_POST['post_type']) && 'page' == $_POST['post_type'] ) 
		if ( !current_user_can( 'edit_page', $post_id ) )
			return;
	else
		if ( !current_user_can( 'edit_post', $post_id ) )
			return;
	
	//get user data in german
	$mydetitle = $_POST['post_title'];
	$mydedata = $_POST['content'];
	$mydomain = $_POST['pg_domain_to_en'];
	
	//sanitize english user input
	$myentitle = sanitize_text_field( $_POST['pg_title_en'] );
	$myendata = $_POST['pg_en_editor'];
	
	//sanitize french user input
	$myfrtitle = sanitize_text_field( $_POST['pg_title_fr'] );
	$myfrdata = $_POST['pg_fr_editor'];
	
	//check for translate checkbox
	if (isset($_POST['translate_to_en']))
	{
		$enversion = pg_get_en_translation($mydetitle, $mydedata, $mydomain);
		$myentitle = $enversion['title'];
		$myendata = $enversion['text'];
	}
	
	if (isset($_POST['translate_to_fr']))
	{
		$frversion = pg_get_fr_translation($mydetitle, $mydedata, $mydomain);
		$myfrtitle = $frversion['title'];
		$myfrdata = $frversion['text'];
	}
	
	//update english data
	update_post_meta($post_id, 'pg_post_en', $myendata);
	update_post_meta($post_id, 'pg_title_en', $myentitle);

	//update french data
	update_post_meta($post_id, 'pg_post_fr', $myfrdata);
	update_post_meta($post_id, 'pg_title_fr', $myfrtitle);
}