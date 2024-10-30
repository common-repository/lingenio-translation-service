<?php

require_once(__PG_ROOT__.'/pg_requests/entranslate.php');

//*** modify query before searching ***//
//*** see query.php, line 1917 ***//
//*** stopwords, counting and ordering are still a big problem ***//

//guess the language
function pg_active_language_for_search()
{
	//asume source language from session or GET
	//target language, always blog language
	if (isset($_REQUEST['lang']))
		$_SESSION['language'] = $_REQUEST['lang'];
	
	return array('src'=>$_SESSION['language'], 'trg'=>'de');
}

//build join for $wpdb->postmeta
function pg_join_paged($join)
{
	global $wpdb;
	return $join .= " LEFT JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) ";		
}

//eliminate duplicates
function pg_search_distinct() 
{
	return "DISTINCT";
}

//rebuild where clause
function pg_posts_search($search)
{
	global $wpdb, $wp;
		
	if (is_search())
		if ( !empty( $search ) ) 
		{
			$q = $wp->query_vars;
			
			$q['s'] = stripslashes($q['s']);
			if ( empty( $_GET['s'] ) )
				$q['s'] = urldecode($q['s']);
			if ( !empty($q['sentence']) ) {
				$q['search_terms'] = array($q['s']);
			} else 
            {
				if ( preg_match_all( '/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', $q['s'], $matches ) )  //stopword are not working, for now
                {
                    //$q['search_terms_count'] = count( $matches[0] );
                    //$q['search_terms'] = $this->parse_search_terms( $matches[0] );
                    // if the search string has only short terms or stopwords, or is 10+ terms long, match it as sentence
                    //if ( empty( $q['search_terms'] ) || count( $q['search_terms'] ) > 9 )
                    $q['search_terms'] = array( $q['s'] );
                } 
                else 
                    $q['search_terms'] = array( $q['s'] );
			}
			$n = !empty($q['exact']) ? '' : '%';
			
			//kill search and rebuild it
			$search = '';
			//search in meta content for english and french
			$meta_search_en = '';
			$meta_search_fr = '';
			
			$searchand = '';
			foreach( (array) $q['search_terms'] as $word ) 
			{
				$word = esc_sql( like_escape( $word ) );
				$search .= "{$searchand}(($wpdb->posts.post_title LIKE '{$n}{$word}{$n}') OR ($wpdb->posts.post_content LIKE '{$n}{$word}{$n}'))";
				$meta_search_en .= "{$searchand}(($wpdb->postmeta.meta_key = 'pg_post_en' AND $wpdb->postmeta.meta_value LIKE '{$n}{$word}{$n}') OR ($wpdb->postmeta.meta_key = 'pg_title_en' AND $wpdb->postmeta.meta_value LIKE '{$n}{$word}{$n}'))";
				$meta_search_fr .= "{$searchand}(($wpdb->postmeta.meta_key = 'pg_post_fr' AND $wpdb->postmeta.meta_value LIKE '{$n}{$word}{$n}') OR ($wpdb->postmeta.meta_key = 'pg_title_fr' AND $wpdb->postmeta.meta_value LIKE '{$n}{$word}{$n}'))";
				$searchand = ' AND ';
			}
			
			//translate everything and go over it again
			if ( 'checked="checked"' == get_option('pg_search_translation'))
			{
				//languages available
				$la = array('de', 'en', 'fr');
				$la_combi = array(
								'de'=>array('en','fr'),
								'en'=>array('de'),
								'fr'=>array('de')
								);
				
				$active = pg_active_language_for_search();
				
				//result value for all translated words
				$translated_search = array();
				
				//check for correct active language
				if (in_array($active['src'], $la))
					foreach ($la_combi[$active['src']] as $lang)
					{
						//get translation, src=$active, trg=$lang
						$searchand = '';
						
						$search_tr = '';
						$meta_search_en_tr = '';
						$meta_search_fr_tr = '';
						
						//translate complete search term and split into words
						foreach (explode(" ", trim(pg_short_translation($q['s'], $active['src'], $lang))) as $word)
						{
							$word = esc_sql( like_escape( $word ) );
							$search_tr .= "{$searchand}(($wpdb->posts.post_title LIKE '{$n}{$word}{$n}') OR ($wpdb->posts.post_content LIKE '{$n}{$word}{$n}'))";
							$meta_search_en_tr .= "{$searchand}(($wpdb->postmeta.meta_key = 'pg_post_en' AND $wpdb->postmeta.meta_value LIKE '{$n}{$word}{$n}') OR ($wpdb->postmeta.meta_key = 'pg_title_en' AND $wpdb->postmeta.meta_value LIKE '{$n}{$word}{$n}'))";
							$meta_search_fr_tr .= "{$searchand}(($wpdb->postmeta.meta_key = 'pg_post_fr' AND $wpdb->postmeta.meta_value LIKE '{$n}{$word}{$n}') OR ($wpdb->postmeta.meta_key = 'pg_title_fr' AND $wpdb->postmeta.meta_value LIKE '{$n}{$word}{$n}'))";
							$searchand = ' AND ';
						}
						
						$translated_search[] = $search_tr;
						$translated_search[] = $meta_search_en_tr;
						$translated_search[] = $meta_search_fr_tr;
					}
			
			}
			
			$translated_result = '';
			if (!empty($translated_search))
				foreach ($translated_search as $result)
					$translated_result .= " OR({$result}) ";
			
			//build the query
			if ( !empty($search) ) 
			{
				$search = " AND ({$search} OR ({$meta_search_en}) OR ({$meta_search_fr}) {$translated_result}) ";
				if ( !is_user_logged_in() )
					$search .= " AND ($wpdb->posts.post_password = '') ";
			}
			
			add_filter( 'posts_distinct', 'pg_search_distinct' );
			add_filter( 'posts_join_paged', 'pg_join_paged' );
		}
	
	return $search;
}

