<?php

require_once(__PG_ROOT__.'/pg_requests/register.php'); 
require_once(__PG_ROOT__.'/pg_requests/http_request.php');

function pg_dashboard_widget() 
{
	if ( isset ( $_REQUEST['tab'] ) ) $tab = $_REQUEST['tab'];
		else $tab = 'first';
		
	//default tabs
	$tabs = array('first' => 'Lingenio News', 'second' => 'Mein Abo');
	
    //set third tab with debug info
    if (PG_DEBUG)
        $tabs['third'] = 'Info';
    
    //check for curl, notify the user
    if (!function_exists('curl_version'))
        echo '<font style="color:red; font-weight:bold;">Achtung: Curl ist nicht verf&uuml;gbar. Ohne Curl kann der Lingenio Server nicht genutzt werden.</font>';
    
    
	echo '<h2 class="nav-tab-wrapper">';
	foreach( $tabs as $t => $name )
	{
		$class = ( $t == $tab ) ? ' nav-tab-active' : '';
		echo "<a class='nav-tab$class' href='?tab=$t'>$name</a>";
	}

	echo '</h2>';
	echo '<table class="form-table"></br>';
	
	switch ( $tab )
	{
		case 'first': //RSS
			
			//get options
			$pg_widget_options = get_option( 'pg_widget_options', array('feed'=>'https://ltp.schmersow-it.com/feed','amount'=>'5','trg'=>'_blank') ) ;
			
            $feed = simplexml_load_file($pg_widget_options['feed']);

            foreach ($feed->channel->item as $node) 
            {
                $items[] = array(
                    'title' => (string) $node->title,
                    'desc' => (string) $node->description,
                    'date' => strtotime((string) $node->pubDate),
                    'link' => (string) $node->link
                );
            }
            
            for($x=0; $x<$pg_widget_options['amount']; $x++) 
            {
                $title = str_replace(' & ', ' &amp; ', $items[$x]['title']);
                $link = $items[$x]['link'];
                $date = date('l F d, Y', $items[$x]['date']);
                $desc = pg_cut_description($items[$x]['desc']);
                echo '<p><strong><a class="company_name_a" href="'.$link.'" title="'.$title.'" target="'.$pg_widget_options['trg'].'">'.$title.'</a></strong>';
                echo '<small><em>  Ver&ouml;ffentlicht am '.$date.'</em></small></p>';
                echo '<p>'.$desc.'<a style="text-decoration:none;" href="'.$link.'" title="'.$title.'" target="'.$pg_widget_options['trg'].'"> mehr ...</a></p>';
            }
           			
			break;
		
		case 'second': //Abo
			
            //get Quota
            $quota = pg_get_quota(); //array('max'=>500, 'used'=>318)
            
            if (0 == $quota['max'])
                $perc = 0;
            else
                $perc = 100 - round($quota['used']/$quota['max']*100); 
                        
            ?>
                <div><h2><?php echo $quota['max']-$quota['used']; ?> Zeichen zur Verf&uuml;gung von <?php echo $quota['max'].' ('.$perc.' %)'; ?> </h2></div>
          
                <div id="pg_quota"><p style="width: 0%;"><span>0%</span></p></div>
                <script type="text/javascript">
                    calcQuota(<?php echo $perc; ?>);
                </script>
        
                <!--<div><a href="<?php echo $shop; ?>" target="_blank"><strong>Zum Shop und auftanken</strong></a></div>-->
        
			<?php
                      
            break;
        
        case 'third': //Debug and info
        
            //get server info
            
            $result = pg_postRequest('', 'info');
            $quota = pg_postRequest('', 'hasquota');
            
            if ($result)
            {
                $info = json_decode($result);
                
                ?>
                
                    <table class="form-table">
						<tbody>
							
							<tr>
								<th><label for="version">Version: </label></th>
								<td><input id="version" type="text" maxlength="100" size="60" name="version" value="<?php echo $info->version; ?>"  disabled/></td>
							</tr>
                            
                            <tr>
								<th><label for="vendor">Vendor: </label></th>
								<td><input id="vendor" type="text" maxlength="100" size="60" name="vendor" value="<?php echo $info->vendor; ?>"  disabled/></td>
							</tr>
                            
                            <tr>
								<th><label for="engine">Engine: </label></th>
								<td><input id="engine" type="text" maxlength="100" size="60" name="engine" value="<?php echo $info->engine; ?>"  disabled/></td>
							</tr>
                            
                            <tr>
								<th><label for="quota">Quota: </label></th>
								<td><input id="quota" type="text" maxlength="100" size="60" name="quota" value="<?php echo "has quota: ".(bool)$quota; ?>"  disabled/></td>
							</tr>
                            
                        </tbody>
                    </table>
                
                <?php
            }
            
            
            //ask for quota
                      
        
            break;
	}

	echo '</table>';	
} 

//create short description for feed items
function pg_cut_description($desc)
{
	$words = explode(' ', $desc);
	$length = sizeof($words);
	if ($length > 13)
		$length = 13;
	
	array_splice($words, $length);
	
	return implode(' ', $words);
}

//configure feed on first dashboard widget tab
function pg_configure_dashboard_widget($widget_id)
{
	// Get widget options
	$pg_widget_options = get_option( 'pg_widget_options', array('feed'=>'http://blog.schmersow-it.de/feed','amount'=>'5','trg'=>'_blank') ) ;
	
	// Update widget options
	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['pg_widget_post']) ) 
		update_option( 'pg_widget_options', $_POST['pg_widget'] );
	
	// Retrieve feed URLs
	$feed = $pg_widget_options['feed'];
	$amount = $pg_widget_options['amount'];
	$trg = $pg_widget_options['trg'];
	
	?>
	<p>
		<label for="pg_feed">Gib hier die URL zu deinem RSS an:</label>
		<input class="widefat" id="pg_feed" name="pg_widget[feed]" type="text" value="<?php if( isset($feed) ) echo $feed; ?>" />
	</p>
	
	<p>
		<label for="pg_amount">Viele Eintr&auml;ge sollen gezeigt werden:</label>
		<input class="widefat" id="pg_amount" name="pg_widget[amount]" type="text" value="<?php if( isset($amount) ) echo $amount; ?>" />
	</p>
	
	<p>
		<label for="pg_trg">Wo soll der Klick hinf&uuml;hren (html-target):</label>
		<input class="widefat" id="pg_trg" name="pg_widget[trg]" type="text" value="<?php if( isset($trg) ) echo $trg; ?>" />
	</p>
	
	<input name="pg_widget_post" type="hidden" value="1" />
	<?php
}
 
function pg_add_dashboard_widget() 
{
	wp_add_dashboard_widget('pg_dashboard_widget', 'Lingenio Translation', 'pg_dashboard_widget', 'pg_configure_dashboard_widget');
}