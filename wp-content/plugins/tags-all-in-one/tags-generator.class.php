<?php
/*
Author: Marcin Gierada
Author URI: http://www.teastudio.pl/
Author Email: m.gierada@teastudio.pl
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

class TagsAllInOneGenerator {
    
        public static function generateId() {
                return rand();
        }
    
        public static function getDefaults() {
                return array('id'           => self::generateId(),
                             'number'       => 45,                            
                             'smallest'     => 8,
                             'largest'      => 22,
                             'unit'         => 'pt',
                             'separator'    => '',
                             'orderby'      => 'name',
                             'order'        => 'asc',                             
                             'taxonomy'     => 'post_tag',
                             'post'         => 'false',
                             'echo'         => false
                            );        
        }
    
    
        public static function generate($atts) {
                
                /*
                 * default parameters
                 */
                $params = self::prepareSettings($atts);
                /*
                 * Make this string uppercase
                 */
                $params['order'] = strtoupper($params['order']);
                
                $out = '<div id="tags-all-in-one-'. $params['id'] .'" class="tags-all-in-one tagcloud">';               
                        if ( $params['post'] === 'true' ) {
                                global $post;
                                
                                $params['taxonomy'] = get_post_type( $post->ID ) . '_tag';
                                $params['include'] = implode( ',', wp_get_post_tags( $post->ID, array( 'fields' => 'ids' ) ) );

                                $out .= wp_tag_cloud( $params );
                        } else {
                                $out .= wp_tag_cloud( $params );
                        }
                $out .= '</div>';
                
                return $out;
        }
         
        
        public static function prepareSettings($settings) {                
                $settings['id'] = self::generateId(); 
                
                $checkboxes = array('post'  => 'false');

                foreach($checkboxes as $k => $v) {
                        if (!array_key_exists($k, $settings)) {
                                $settings[$k] = 'false';
                        } else { 
                                $settings[$k] = ($settings[$k] == 1 || $settings[$k] == 'true') ? 'true' : 'false';
                        }
                }
                
                /*
                 * if there are no all settings
                 */
                $defaults = self::getDefaults();
                foreach($defaults as $k => $v) {
                    if (!array_key_exists($k, $settings)) {
                        $settings[$k] = $defaults[$k];
                    }
                }
                return $settings;
        }        
}