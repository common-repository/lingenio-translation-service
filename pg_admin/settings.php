<?php

require_once(__PG_ROOT__.'/pg_requests/register.php'); 
require_once(__PG_ROOT__.'/pg_admin/biographie.php');
require_once(__PG_ROOT__.'/pg_admin/general.php');
require_once(__PG_ROOT__.'/pg_includes/dict_table.php');
require_once(__PG_ROOT__.'/pg_requests/dict_request.php');

function pg_settings_option_page()
{
	//only admins can do anything
	if (!current_user_can('activate_plugins'))
		return;
	
	//save data from dict, if needed
	$tmp_dict = array('id'=>'0', 'ge'=>'', 'en'=>'', 'fr'=>'', 'es'=>'');
	
	//handle user input
	if (isset($_REQUEST['action']))
    {   
		$action = $_REQUEST['action'];
        
        if (!check_admin_referer('pg-settings-save_0235'))
            return;
		
		//save login data
		if ('pg_login_data_update' == $action && isset($_REQUEST['api_key']) && isset($_REQUEST['username']) && isset($_REQUEST['password']))
			update_option('pg_login_data', json_encode(array('api_key' => $_REQUEST['api_key'], 'username' => $_REQUEST['username'], 'password' => sha1($_REQUEST['password']))));
	
		//save selected languages
		if ('pg_language_settings_update' == $action)
		{
			$selection = array('de_fr' => '', 'de_es' => '');
			
			if (isset($_REQUEST['de_fr']))
				$selection['de_fr'] = 'checked="checked"';
				
			if (isset($_REQUEST['de_es']))
				$selection['de_es'] = 'checked="checked"';
				
			update_option('pg_selected_languages', json_encode($selection));
			
			//update auto translate option
			if (isset($_REQUEST['auto']))
				update_option('pg_auto_translation_on_view', 'checked="checked"');
			else
				update_option('pg_auto_translation_on_view', '');
				
			//update search translate option
			if (isset($_REQUEST['search']))
				update_option('pg_search_translation', 'checked="checked"');
			else
				update_option('pg_search_translation', '');
			
			//language selection bar
			if (isset($_REQUEST['show_selector']))
				update_option('pg_show_selector', 'checked="checked"');
			else
				update_option('pg_show_selector', '');
		}
		
		//edit dictionary
		if ('pg_edit_dict' == $action)
		{
			$tmp_dict['id'] = $_REQUEST['id'];
			$tmp_dict['ge'] = $_REQUEST['de'];
			$tmp_dict['en'] = $_REQUEST['en'];
			$tmp_dict['fr'] = $_REQUEST['fr'];
			$tmp_dict['es'] = $_REQUEST['es'];
		}
		
		//save value for dict
		if ('pg_save_dict' == $action)
		{
			
		}
		
		//get selected languages
		$active_lang = pg_list_active_languages();
		
		//translate user biographie
		if ('pg_translate_user_bio' == $action)
		{
			pg_save_english_biographie($_REQUEST['pg_user_list'], true);
			if (in_array('fr', $active_lang))
				pg_save_french_biographie($_REQUEST['pg_user_list'], true);
		}
			
		//translate title and tagline
		if ('pg_translate_title' == $action)
		{
			pg_english_title_translation();
			if (in_array('fr', $active_lang))
				pg_french_title_translation();
		}
		
	}	
	
	?>
    <div class='wrap'>
	<div id="icon-tools" class="icon32"></div><h2>&Uuml;bersetzungseinstellungen f&uuml;r <strong><?php echo wp_title(); ?></strong></h2><hr>
	
	<?php
	
	if ( isset ( $_REQUEST['tab'] ) ) $tab = $_REQUEST['tab'];
		else $tab = 'first';
		
	//default tabs
	//$tabs = array('first' => 'Einstellungen', 'second' => 'Shop', 'third' => 'Wiki');
    $tabs = array('first' => 'Einstellungen', 'second' => 'Shop');
	
	echo '<h2 class="nav-tab-wrapper">';
	foreach( $tabs as $t => $name )
	{
		$class = ( $t == $tab ) ? ' nav-tab-active' : '';
		echo "<a class='nav-tab$class' href='?page=".$_REQUEST['page']."&tab=$t'>$name</a>";
	}

	echo '</h2>';
	echo '<table class="form-table"></br>';
	
	switch ( $tab )
	{
		case 'first': //Einstellungen
		
			//load login data
			$login = array('api_key' => '', 'username' => '', 'password' => '');
			
			$opt = get_option('pg_login_data');
			if ($opt)
			{
				$json = json_decode($opt);
				$login['api_key'] = $json->api_key;
				$login['username'] = $json->username;
			}
		
			//load selected languages
			$languages = array('de_fr' => '', 'de_es' => '', 'auto' => get_option('pg_auto_translation_on_view'), 'show_selector'=> get_option('pg_show_selector'), 'search'=> get_option('pg_search_translation'));
			$lang = get_option('pg_selected_languages');
			if ($lang)
			{	
				$json = json_decode($lang);
				$languages['de_fr'] = $json->de_fr;
				$languages['de_es'] = $json->de_es;
			}
			
			//write list of users
			$userlist = '';
			foreach (get_users() as $user) 
			{
				$userlist .= '<option value="'.$user->ID.'">'.$user->display_name.'</option>';
			}
			
			?>
				
				<form id="pg_login_settings" method="post"> <!-- form for login data -->
				
					<table class="form-table">
						<tbody>
							
							<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
							<input type="hidden" name="tab" value="<?php echo $tab ?>" />
							<input type="hidden" name="action" value="pg_login_data_update"/>
                            <?php
                                if ( function_exists('wp_nonce_field') ) 
                                    wp_nonce_field('pg-settings-save_0235'); 
                            ?>
							
							<tr>
								<th><h2>Zugangsdaten</h2></th>
								<td><?php //echo pg_register_plugin(true); ?></td>
							</tr>
							
							<tr>
								<th><label for="api_key">Api Key <i>(im Shop generieren)</i>: </label></th>
								<td><input id="api_key" type="text" maxlength="45" size="20" name="api_key" value="<?php echo $login['api_key'] ?>" /></td>
							</tr>
							<!--
							<tr>
								<th><label for="username">Nutzername: </label></th>
								<td><input id="username" type="text" maxlength="45" size="20" name="username" value="<?php echo $login['username'] ?>" /></td> 
							</tr>
							
							<tr>
								<th><label for="password">Passwort: </label></th>
								<td><input type="password" id="password" maxlength="45" size="20" name="password" value="<?php echo $login['password'] ?>" /></td>   
							</tr>
							-->
							<tr>
								<th><strong>Hol Dir im Shop Deinen Zugang.</strong></th>
							</tr>
						
							<tr>
								<th><p class="submit"><input id="pg_submit_login" class="button button-primary" type="submit" value="Speichern" name="pg_submit_login"></p></th>
							</tr>
									
						</tbody>
					</table>
					                    
				</form>
				
				<form id="pg_language_settings" method="post"> <!-- select languages for translation -->
					
					<table class="form-table">
						<tbody>

							<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
							<input type="hidden" name="tab" value="<?php echo $tab ?>" />
							<input type="hidden" name="action" value="pg_language_settings_update"/>
                            <?php
                                if ( function_exists('wp_nonce_field') ) 
                                    wp_nonce_field('pg-settings-save_0235'); 
                            ?>
                            
							<tr>
								<th><h2>Sprach- und &Uuml;bersetzungseinstellungen</h2></th>
								<td></td>
							</tr>
							
							<tr>
								<th>Artikel ohne &Uuml;bersetzung beim Anzeigen automatisch &uuml;bersetzen und speichern</th>
								<td><input type="checkbox" name="auto" value="auto" <?php echo $languages['auto']; ?>></td>
							</tr>
							
							<tr>
								<th>Suchbegriffe automatisch &uuml;bersetzen</th>
								<td><input type="checkbox" name="search" value="search" <?php echo $languages['search']; ?>></td>
							</tr>
							
							<tr>
								<th>Sprachauswahl nicht anzeigen (<i>kann mit pgLanguageBar() im Theme eingef&uuml;gt werden</i>)</th>
								<td><input type="checkbox" name="show_selector" value="auto" <?php echo $languages['show_selector']; ?>></td>
							</tr>
							
							<tr>
								<th><h3>Sprachauswahl - Auswahl wird jeweils als eigner Editor angezeigt</h3></th>
								<td></td>
							</tr>
							
							<tr>
								<th>Deutsch &rarr; Englisch</th>
								<td><input type="checkbox" name="de_en" value="de_en" disabled="true" checked="checked" ></td>
							</tr>
							
							<tr>
								<th>Deutsch &rarr; Franz&ouml;sisch</th>
								<td><input type="checkbox" name="de_fr" value="de_fr" <?php echo $languages['de_fr']; ?>></td>
							</tr>
						
							<tr>
								<th>Deutsch &rarr; Spanisch <i>(noch nicht verf&uuml;gbar)</i></th>
								<td><input type="checkbox" name="de_es" value="de_es" disabled="true" <?php echo $languages['de_es']; ?>></td>
							</tr>
						
							<tr>
								<th><p class="submit"><input id="pg_submit_lang" class="button button-primary" type="submit" value="Speichern" name="pg_submit_lang"></p></th>
							</tr>
							
						</tbody>
					</table>
				
				</form>
				
				<form id="pg_bio_data" method="post"> <!-- translate user biographie -->
					
					<table class="form-table">
						<tbody>

							<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
							<input type="hidden" name="tab" value="<?php echo $tab ?>" />
							<input type="hidden" name="action" value="pg_translate_user_bio"/>
                            <?php
                                if ( function_exists('wp_nonce_field') ) 
                                    wp_nonce_field('pg-settings-save_0235'); 
                            ?>
                            
							<tr>
								<th><h2>Biographische Daten</h2></th>
								<td> - Biographie eines Nutzers &uuml;bersetzen lassen, Achtung, die Nutzereingaben gehen dabei verloren.</td>
							</tr>
					
							<tr>
								<th><p class="submit"><input id="pg_submit_user_bio" class="button button-primary" type="submit" value="&Uuml;bersetzen" name="pg_submit_user_bio"></p></th>
								<td>
									<select name="pg_user_list" id="pg_user_list" >
										<?php echo $userlist; ?>
									</select>
								</td>
							</tr>
							
						</tbody>
					</table>
				
				</form>
				
				<form id="pg_title_data" method="post"> <!-- translate titel and tagline -->
					
					<table class="form-table">
						<tbody>

							<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
							<input type="hidden" name="tab" value="<?php echo $tab ?>" />
							<input type="hidden" name="action" value="pg_translate_title"/>
                            <?php
                                if ( function_exists('wp_nonce_field') ) 
                                    wp_nonce_field('pg-settings-save_0235'); 
                            ?>
                            
							<tr>
								<th><h2>Titel und Untertitel des Blogs neu &uuml;bersetzen</h2></th>
							</tr>
					
							<tr>
								<th><p class="submit"><input id="pg_submit_title_trans" class="button button-primary" type="submit" value="&Uuml;bersetzen" name="pg_submit_title_trans"></p></th>
							</tr>
							
						</tbody>
					</table>
				
				</form>
							
			<?php
		
			break;
			
		case 'second': //LTS Shop
			
			$login = array('api_key' => '', 'username' => '', 'password' => '');
			
			$opt = get_option('pg_login_data');
			if ($opt)
			{
				$json = json_decode($opt);
				$login['username'] = $json->username;
				$login['password'] = $json->password;
			}
			
			$login_query = '';//"?user=".$login['username']."&pwd=".$login['password'];
			$shop = "http://shop.lingenio.de".$login_query;
			
			?>
				<iframe src="<?php echo $shop ?>" width="90%" height="1500" name="lts_shop_frame">
				  <p>Ihr Browser kann leider keine eingebetteten Frames anzeigen:
				  Sie k&ouml;nnen die eingebettete Seite &uuml;ber den folgenden Verweis
				  aufrufen: <a href="<?php echo $shop ?>">SELFHTML</a></p>
				</iframe>
			<?php
			break;
			
		case 'third': //Wiki
			
			?>
				<p>Feature coming soon ...</p>
			<?php
			break;
		
		}
	
	echo '</table>';
	
	?>
	
	<?php
}

function pg_settings_add_menu()
{
	add_options_page('Lingenio Translation', 'Lingenio Translation', 'activate_plugins', __FILE__, 'pg_settings_option_page');
}