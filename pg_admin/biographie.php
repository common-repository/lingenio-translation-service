<?php

require_once(__PG_ROOT__.'/pg_requests/entranslate.php'); 

function pg_biographie_to_english($user)
{
	?>
	
		<table class="form-table">
			<tr>
				<th><label for="pg_en_bio">Biographie in Englisch</label></th>
				<td>
					<textarea name="pg_en_bio" id="pg_en_bio" cols="30" rows="5" ><?php echo esc_attr(get_the_author_meta('pg_en_bio', $user->ID)); ?></textarea><br />
					<span class="description">Hier steht die automatische &Uuml;bersetzung Deiner Biographie. </span>
				</td>
			</tr>
		</table>

	<?php
	
	
}

function pg_save_english_biographie($user_id, $translate = false)
{
	if ( !current_user_can('edit_user', $user_id))
		return false;
    
	//$translate is used in settings.php to translate the text without user interaction
	if ($translate)
	{
		$description = get_the_author_meta('description', $user_id);
		if ('' != $description)
		{
			$translation = pg_get_en_translation('', $description);
			update_user_meta($user_id, 'pg_en_bio', $translation['text']);
		}
	}
	else
		update_user_meta($user_id, 'pg_en_bio', $_POST['pg_en_bio']);
}

function pg_biographie_to_french($user)
{
	?>
	
		<table class="form-table">
			<tr>
				<th><label for="pg_fr_bio">Biographie in Franz&ouml;sisch</label></th>
				<td>
					<textarea name="pg_fr_bio" id="pg_fr_bio" cols="30" rows="5" ><?php echo esc_attr(get_the_author_meta('pg_fr_bio', $user->ID)); ?></textarea><br />
					<span class="description">Hier steht die automatische &Uuml;bersetzung Deiner Biographie. </span>
				</td>
			</tr>
		</table>

	<?php
	
	
}

function pg_save_french_biographie($user_id, $translate = false)
{
	if ( !current_user_can('edit_user', $user_id))
		return false;
    
	//$translate is used in settings.php to translate the text without user interaction
	if ($translate)
	{
		$description = get_the_author_meta('description', $user_id);
		if ('' != $description)
		{
			$translation = pg_get_fr_translation('', $description);
			update_user_meta($user_id, 'pg_fr_bio', $translation['text']);
		}
	}
	else
		update_user_meta($user_id, 'pg_fr_bio', $_POST['pg_fr_bio']);
}