<?php
/*
Plugin Name: Tags all in one
Plugin URI: http://www.teastudio.pl/produkt/tags-all-in-one/
Description: Display tags cloud for selected post types by widget or display tags cloud for current post by shortcode generator.
Version: 1.0.3
Author: Marcin Gierada
Author URI: http://www.teastudio.pl/
Author Email: m.gierada@teastudio.pl
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/

/*
 * load i18n
 */
add_action("init", "tags_all_in_one_init");
function tags_all_in_one_init() {
        load_plugin_textdomain("tags-all-in-one", false, dirname(plugin_basename( __FILE__ )) .  "/i18n/languages/");
}


/*
 * plugin
 */
$tags_All_In_One = new Tags_All_In_One();
class Tags_All_In_One {
        const VERSION = '1.0.3';
        private $plugin_name = 'Tags all in one';
        private $plugin_slug = 'tags-all-in-one';
        private $options = array();
        
	public function __construct() {
                /*
                 * get options
                 */
                $this->options = array_merge( $this->get_defaults(), get_option($this->plugin_slug . '_options') ? get_option($this->plugin_slug . '_options') : array() );
                
                /*
                 * include utils
                 */
                require_once("includes/utils.class.php");                

                //include required files based on admin or site
                if (is_admin()) {   
                        /*
                        * activate plugin
                        */                            
                        add_action( 'init', array($this, 'tags_all_in_one_button') );	 
                        add_action( 'admin_head', array($this, 'tags_all_in_one_button') );     

                        /*
                         * ajax page for shortcode generator
                         */
                        add_action("wp_ajax_tags_all_in_one_shortcode_generator", array($this, "TagsAllInOneShortcodeGenerator") );

                        /*
                         * clear settings
                         */
                        register_deactivation_hook(__FILE__,  array($this, 'deactivation') );
                } else {
                        require_once("shortcode-decode.class.php");
                } 

                /*
                 * widget
                 */
                require_once("tags-generator.class.php");
                require_once("tags-widget.class.php");  
        }          
        
	/**
	 * deactivate the plugin
	 */
	public function deactivation()
	{
                if ( ! current_user_can( 'activate_plugins' ) ) {
                       return;            
                }
                delete_option( $this->plugin_slug . '_options' );
	}       
                
        /**
         * retrieves the plugin options from the database.
         */
        private function get_defaults() {
                return array();
        }    
            
        function TagsAllInOneShortcodeGenerator() {
                require_once("shortcode-generator.php");
                exit();
        }    
       
        /*
         * add button to editor
         */
        function tags_all_in_one_button() {
                // check user permissions
                if ( !current_user_can( "edit_posts" ) && !current_user_can( "edit_pages" ) ) {
                        return;
                }        

                //adds button to the visual editor
                add_filter("mce_external_plugins", array($this, "add_tags_all_in_one_plugin") );  
                add_filter("mce_buttons", array($this, "register_add_tags_all_in_one_button") );  
        }

        /*
         * callback function
         */
        function add_tags_all_in_one_plugin($plugin_array) {        
                $blog_version = floatval(get_bloginfo("version"));

                if($blog_version >= 4.0) {
                        $version = "plugin-4.0.js";
                }else if($blog_version < 4.0 && $blog_version >= 3.9) {
                        $version = "plugin-3.9.js";
                } else {
                        $version = "plugin-3.6.js";            
                }

                $plugin_array["tags_all_in_one_button"] = plugin_dir_url(__FILE__)."js/".$version;
                return $plugin_array;
        } 
    
        /* 
         * callback function
         */
        function register_add_tags_all_in_one_button($buttons) {
                array_push($buttons, "tags_all_in_one_button");
                return $buttons;
        } 
}




