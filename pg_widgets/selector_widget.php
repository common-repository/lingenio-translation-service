<?php
 
require_once(__PG_ROOT__.'/pg_public/language_bar.php');  
 
//widget for the language bar
class PG_LanguageBar extends WP_Widget 
{
    function PG_LanguageBar() 
    {
        $wp_options = array('description' => 'Zeigt die Sprachleiste');
        parent::WP_Widget(false, $name = 'Sprache waehlen', $wp_options);
    }
 
    //show the widget
    function widget($args, $instance) 
    {
        extract( $args );
        
        $language = pg_print_language_selector(false);
        $active = $language['active'];
 
        $value = $instance['german'];
        if ('en' === $active)
            $value = $instance['english'];
        if ('fr' === $active)
            $value = $instance['french'];
        
        ?>
            <div class="widget pg_selector_w">
        
                <span><?php echo $value; ?></span>
                <?php echo pg_print_language_selector(); ?>
            
            </div>
        <?php
    }
 
    //save the input
    function update($new_instance, $old_instance) 
    {
        $instance = $old_instance;
    
        if (isset($new_instance['german']))
            $instance['german'] = $new_instance['german'];
        if (isset($new_instance['english']))
            $instance['english'] = $new_instance['english'];
        if (isset($new_instance['french']))
            $instance['french'] = $new_instance['french'];
            
        return $instance;
    }
 
    //admin form for widget
    function form($instance) 
    {
        $default_settings = array('german' => '', 'english' => '', 'french' => '');
        $instance = wp_parse_args((array) $instance, $default_settings); 
        
        $active_lang = pg_list_active_languages();
        
        ?>
            <p>
                <span>Sage deinen Nutzern etwas zur Wahl der Sprache</span><br><br>
                
                <label for="<?php echo $this->get_field_id('german'); ?>">Deutsch</label><br>
                <input style="width:98%;" type="text" name="<?php echo $this->get_field_name('german'); ?>" id="<?php echo $this->get_field_id('german'); ?>" value="<?php echo $instance['german']; ?>"/>
                
                <label for="<?php echo $this->get_field_id('english'); ?>">Englisch</label><br>
                <input style="width:98%;" type="text" name="<?php echo $this->get_field_name('english'); ?>" id="<?php echo $this->get_field_id('english'); ?>" value="<?php echo $instance['english']; ?>"/>
                
                <?php
                    if (in_array('fr', $active_lang))
                    {
                        ?>
                            <label for="<?php echo $this->get_field_id('french'); ?>">Franz&ouml;sisch</label><br>
                            <input style="width:98%;" type="text" name="<?php echo $this->get_field_name('french'); ?>" id="<?php echo $this->get_field_id('french'); ?>" value="<?php echo $instance['french']; ?>"/>
                        <?php
                    }
                ?>
            </p>
        
        <?php
    }
}

add_action( 'widgets_init', 'pg_register_lbar_widget' );
function pg_register_lbar_widget() 
{
    register_widget('PG_LanguageBar');
}
