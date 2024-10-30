<?php

/* rewrite some default widgets for mutlilingual support*/
/* see default_widgets.php*/

/*****register******/
add_action( 'widgets_init', 'pg_register_custom_widgets' );

function pg_register_custom_widgets() 
{
    register_widget('PG_Widget_Recent_Posts');
}

/*******************/

class PG_Widget_Recent_Posts extends WP_Widget {

	function __construct() 
    {
		$widget_ops = array('classname' => 'pg_widget_recent_entries', 'description' => "Die neusten Artikel auf Deiner Seite" );
		parent::__construct('pg_recent-posts', 'Letzte Artikel Multilingual', $widget_ops);
	}

	function widget($args, $instance) 
    {
		extract($args);

        //get active language
        $language = pg_print_language_selector(false);
        $active = $language['active'];
 
        $title = ( ! empty( $instance['title_de'] ) ) ? $instance['title_de'] : 'Letzte Artikel Multilingual';
        if ('en' === $active)
            $title = $instance['title_en'];
        if ('fr' === $active)
            $title = $instance['title_fr'];
                
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 10;
		if ( ! $number )
 			$number = 10;
		$show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;

		$r = new WP_Query( apply_filters( 'widget_posts_args', array( 'posts_per_page' => $number, 'no_found_rows' => true, 'post_status' => 'publish', 'ignore_sticky_posts' => true ) ) );
		if ($r->have_posts()) :
        ?>
            <?php echo $before_widget; ?>
            <?php if ( $title ) echo $before_title . $title . $after_title; ?>
            <ul>
            <?php while ( $r->have_posts() ) : $r->the_post(); ?>
                <?php 
                    //get title in correct language
                    $post_title = get_the_title() ? the_title('', '', false) : get_the_id();
                    $post_meta = get_post_custom(get_the_id());
                    if ('en' == $active)
                        if (isset($post_meta['pg_title_en']))
                            $post_title = $post_meta['pg_title_en'][0];
                    if ('fr' == $active)
                        if (isset($post_meta['pg_title_fr']))
                            $post_title = $post_meta['pg_title_fr'][0];
                ?>
                <li>
                    <a href="<?php the_permalink(); ?>"><?php echo $post_title; ?></a>
                <?php if ( $show_date ) : ?>
                    <span class="post-date"><?php echo get_the_date(); ?></span>
                <?php endif; ?>
                </li>
            <?php endwhile; ?>
            </ul>
            <?php echo $after_widget; ?>
        <?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		endif;
    }

	function update( $new_instance, $old_instance ) 
    {
		$instance = $old_instance;
		
        if (isset($new_instance['title_de']))
            $instance['title_de'] = strip_tags($new_instance['title_de']);
        if (isset($new_instance['title_en']))
            $instance['title_en'] = strip_tags($new_instance['title_en']);
        if (isset($new_instance['title_fr']))
            $instance['title_fr'] = strip_tags($new_instance['title_fr']);
        
        $instance['number'] = (int) $new_instance['number'];
		$instance['show_date'] = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
		
		return $instance;
	}

	function form( $instance ) 
    {
        $active_lang = pg_list_active_languages();
        
		$title_de     = isset( $instance['title_de'] ) ? esc_attr( $instance['title_de'] ) : '';
        $title_en     = isset( $instance['title_en'] ) ? esc_attr( $instance['title_en'] ) : '';
        $title_fr     = isset( $instance['title_fr'] ) ? esc_attr( $instance['title_fr'] ) : '';
        
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
        
        ?>
            <p><label for="<?php echo $this->get_field_id( 'title_de' ); ?>">Titel (Deutsch)</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title_de' ); ?>" name="<?php echo $this->get_field_name( 'title_de' ); ?>" type="text" value="<?php echo $title_de; ?>" /></p>
            
            <p><label for="<?php echo $this->get_field_id( 'title_en' ); ?>">Titel (English)</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title_en' ); ?>" name="<?php echo $this->get_field_name( 'title_en' ); ?>" type="text" value="<?php echo $title_en; ?>" /></p>
            
            <?php
                if (in_array('fr', $active_lang))
                {
                    ?>
                        <p><label for="<?php echo $this->get_field_id( 'title_fr' ); ?>">Titel (Franz&ouml;sisch)</label>
                        <input class="widefat" id="<?php echo $this->get_field_id( 'title_fr' ); ?>" name="<?php echo $this->get_field_name( 'title_fr' ); ?>" type="text" value="<?php echo $title_fr; ?>" /></p>
                    <?php
                }
            ?>

            <p><label for="<?php echo $this->get_field_id( 'number' ); ?>">Anzahl der Artikel</label>
            <input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>

            <p><input class="checkbox" type="checkbox" <?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
            <label for="<?php echo $this->get_field_id( 'show_date' ); ?>">Datum anzeigen</label></p>
        <?php
	}
}