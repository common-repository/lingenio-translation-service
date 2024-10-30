<?php

require_once(__PG_ROOT__.'/pg_requests/entranslate.php');
require_once(__PG_ROOT__.'/pg_includes/permalinks.php');
require_once(ABSPATH.WPINC.'/post-template.php');

class My_Custom_Walker extends Walker_page 
{
	function start_el(&$output, $page, $depth, $args, $current_page) 
	{
		//get title in correct language
		$language = $_SESSION['language'];
		$the_post_title = $page->post_title;
		$url_appendix = get_permalink($page->ID);
		
		if ('en' == $language)
			if ('' != get_post_meta($page->ID, 'pg_title_en', true))
			{
				$the_post_title = get_post_meta($page->ID, 'pg_title_en', true);
				$url_appendix = pg_add_url_param(get_permalink($page->ID), 'lang', 'en');
			}
			else //get content on the fly, if selected
				if (get_option('pg_auto_translation_on_view') == 'checked="checked"')
				{
					$enversion = pg_get_en_translation(get_the_title($page->ID), '');
					$the_post_title = $enversion['text'];
				
					update_post_meta($page->ID, 'pg_title_en', $the_post_title);
					$url_appendix = pg_add_url_param(get_permalink($page->ID), 'lang', 'en');
				}
		if ('fr' == $language)
			if ('' != get_post_meta($page->ID, 'pg_title_fr', true))
			{
				$the_post_title = get_post_meta($page->ID, 'pg_title_fr', true);
				$url_appendix = pg_add_url_param(get_permalink($page->ID), 'lang', 'fr');
			}
			else //get content on the fly, if selected
				if (get_option('pg_auto_translation_on_view') == 'checked="checked"')
				{
					$frversion = pg_get_fr_translation(get_the_title($page->ID), '');
					$the_post_title = $frversion['text'];
				
					update_post_meta($page->ID, 'pg_title_fr', $the_post_title);
					$url_appendix = pg_add_url_param(get_permalink($page->ID), 'lang', 'en');
				}
		
		if ($depth)
			$indent = str_repeat("\t", $depth);
		else
			$indent = '';
	
		extract($args, EXTR_SKIP);
		$css_class = array('page_item', 'page-item-'.$page->ID);
		
		if (!empty($current_page)) 
		{
			$_current_page = get_post( $current_page );
			if (in_array($page->ID, $_current_page->ancestors))
				$css_class[] = 'current_page_ancestor';
			if ($page->ID == $current_page)
				$css_class[] = 'current_page_item';
			elseif ($_current_page && $page->ID == $_current_page->post_parent)
				$css_class[] = 'current_page_parent';
		} 
		elseif ($page->ID == get_option('page_for_posts')) 
			$css_class[] = 'current_page_parent';
		
		$css_class = implode(' ', apply_filters('page_css_class', $css_class, $page, $depth, $args, $current_page));
				
		$output .= $indent.'<li class="'.$css_class.'"><a href="'.$url_appendix.'">'.$link_before.$the_post_title.$link_after.'</a>';

		if ( !empty($show_date) ) {
			if ('modified' == $show_date)
				$time = $page->post_modified;
			else
				$time = $page->post_date;

			$output .= " ".mysql2date($date_format, $time);
		}
	}
}

//filter for wp_list_pages, due to bug in nav menu, has to be fixed!
function pg_nav_menu($args)
{
	$My_Walker = new My_Custom_Walker();
	
	if (isset($_REQUEST['lang']))
		$_SESSION['language'] = $_REQUEST['lang'];
	
	$defaults = array(
		'depth' => 0, 'show_date' => '',
		'date_format' => get_option('date_format'),
		'child_of' => 0, 'exclude' => '',
		'title_li' => __('Pages'), 'echo' => 1,
		'authors' => '', 'sort_column' => 'menu_order, post_title',
		'link_before' => '', 'link_after' => '', 'walker' => $My_Walker,
	);

	$r = wp_parse_args($args, $defaults);
	extract($r, EXTR_SKIP);

	$output = '';
	$current_page = 0;
	
	// Query pages.
	$r['hierarchical'] = 0;
	$pages = get_pages($r);
	
	if ( !empty($pages) ) 
	{
		global $wp_query;
		if (is_page() || is_attachment() || $wp_query->is_posts_page)
			$current_page = $wp_query->get_queried_object_id();
			
		$output .= walk_page_tree($pages, $r['depth'], $current_page, $r);
	}
	
	return $output;
}

//set post links for next and previous posts, see link-template.php, line 1414
function pg_adjecent_post_link($format, $link)
{
    //get title in correct language
    $language = $_SESSION['language'];
    
    if ('de' == $language)
        return $format;
        
    $previous = 'previous_post_link' === current_filter();
    $post = get_adjacent_post(false, '', $previous);
    
    if ( ! $post ) //first or latest post? do nothing
		return '';
    
    //get title and prepare url for correct language
    $the_post_title = $post->post_title;
	$url_appendix = get_permalink($post->ID);

    //handle empty titles
    if (empty($the_post_title))
        $the_post_title = $previous ? __( 'Previous Post' ) : __( 'Next Post' );
	
    //get title in actvie language
    if ('en' == $language)
        if ('' != get_post_meta($post->ID, 'pg_title_en', true))
        {
            $the_post_title = get_post_meta($post->ID, 'pg_title_en', true);
            $url_appendix = pg_add_url_param(get_permalink($post->ID), 'lang', 'en');
        }
        else //get content on the fly, if selected
            if (get_option('pg_auto_translation_on_view') == 'checked="checked"')
            {
                $enversion = pg_get_en_translation(get_the_title($post->ID), '');
                $the_post_title = $enversion['text'];
            
                update_post_meta($post->ID, 'pg_title_en', $the_post_title);
                $url_appendix = pg_add_url_param(get_permalink($post->ID), 'lang', 'en');
            }
    if ('fr' == $language)
        if ('' != get_post_meta($post->ID, 'pg_title_fr', true))
        {
            $the_post_title = get_post_meta($post->ID, 'pg_title_fr', true);
            $url_appendix = pg_add_url_param(get_permalink($post->ID), 'lang', 'fr');
        }
        else //get content on the fly, if selected
            if (get_option('pg_auto_translation_on_view') == 'checked="checked"')
            {
                $frversion = pg_get_fr_translation(get_the_title($post->ID), '');
                $the_post_title = $frversion['text'];
            
                update_post_meta($post->ID, 'pg_title_fr', $the_post_title);
                $url_appendix = pg_add_url_param(get_permalink($post->ID), 'lang', 'en');
            }

    //build link to adjecent post
    $rel = $previous ? 'prev' : 'next';

    $string = '<a href="'.$url_appendix.'" rel="'.$rel.'">'.$the_post_title.'</a>';
    
    return $string;
}
