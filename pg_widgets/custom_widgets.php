<?php
 
require_once(__PG_ROOT__.'/pg_requests/entranslate.php');  
require_once(__PG_ROOT__.'/pg_public/language_bar.php');  
 
//widget for html in different languages
class PG_Multilingual_HTML extends WP_Widget 
{
    function PG_multilingual_html() 
    {
        $wp_options = array('description' => 'Dieses Widget kann html in der aktiven Sprache anzeigen.');
        parent::WP_Widget(false, $name = 'Multilinguales html', $wp_options);
    }
 
    //show the widget
    function widget($args, $instance) 
    {
        extract( $args );
        //get active language
        $language = pg_print_language_selector(false);
        $active = $language['active'];
 
        $value = $instance['german'];
        if ('en' === $active)
            $value = $instance['english'];
        if ('fr' === $active)
            $value = $instance['french'];
        
        ?>
            <div class="widget pg_free_html">
        
                <?php echo $value; ?>
            
            </div>
        <?php
        
    }
 
    //save the input
    function update($new_instance, $old_instance) 
    {
        $instance = $old_instance;
     
        //translate the text
        if (isset($new_instance['auto_save']) AND isset($new_instance['german']))
        {
            $instance['german'] = $new_instance['german'];
            
            if (isset($new_instance['english']))
            {
                $trans_en = pg_translation_selector($new_instance['german'], 'en');
                $instance['english'] = $trans_en;
            }
            if (isset($new_instance['french']))
            {
                $trans_fr = pg_translation_selector($new_instance['german'], 'fr');
                $instance['french'] = $trans_fr;
            }
        }
        else //just save the text
        {
            if (isset($new_instance['german']))
                $instance['german'] = $new_instance['german'];
            if (isset($new_instance['english']))
                $instance['english'] = $new_instance['english'];
            if (isset($new_instance['french']))
                $instance['french'] = $new_instance['french'];
        }
        
        return $instance;
    }
 
    //admin form for widget
    function form($instance) 
    {
        $default_settings = array('german' => '', 'english' => '', 'french' => '', 'auto_save' => '');
        $instance = wp_parse_args((array) $instance, $default_settings); 
        
        $active_lang = pg_list_active_languages();
        
        ?>
            <p>
                <label for="<?php echo $this->get_field_id('auto_save'); ?>">Beim n&auml;chsten Speichern &uuml;bersetzen</label>
                <input type="checkbox" name="<?php echo $this->get_field_name('auto_save'); ?>" id="<?php echo $this->get_field_id('auto_save'); ?>" value="autosave" />
                <br><br>
                
                <label for="<?php echo $this->get_field_id('german'); ?>">Deutsch</label>
                <textarea name="<?php echo $this->get_field_name('german'); ?>" id="<?php echo $this->get_field_id('german'); ?>" cols="35" rows="10"><?php echo $instance['german']  ?></textarea>
                
                <label for="<?php echo $this->get_field_id('english'); ?>">Englisch</label>
                <textarea name="<?php echo $this->get_field_name('english'); ?>" id="<?php echo $this->get_field_id('english'); ?>" cols="35" rows="10"><?php echo $instance['english']  ?></textarea>
                
                <?php
                    if (in_array('fr', $active_lang))
                    {
                        ?>
                            <label for="<?php echo $this->get_field_id('french'); ?>">Franz&ouml;sisch</label>
                            <textarea name="<?php echo $this->get_field_name('french'); ?>" id="<?php echo $this->get_field_id('french'); ?>" cols="35" rows="10"><?php echo $instance['french']  ?></textarea>
                        <?php
                    }
                ?>
            </p>
        
        <?php
        
     
    }
}

add_action( 'widgets_init', 'pg_register_html_widget' );
function pg_register_html_widget() 
{
    register_widget('PG_Multilingual_HTML');
}
