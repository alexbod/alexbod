<?php
/*
Author: Marcin Gierada
Author URI: http://www.teastudio.pl/
Author Email: m.gierada@teastudio.pl
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/*
 * 
 */
class Tags_All_In_One_Utils {
    
        public static function getTaxonomies() {
                $taxonomies = array();
                $taxonomies_list = get_taxonomies( array('show_tagcloud' => true), 'objects' );
                
                if ( count($taxonomies_list) > 0 ) {
                    foreach ( $taxonomies_list as $key => $taxonomy ) {
                        //if ( preg_match( '/_tag/', $key) ) {
                            $taxonomies[$key] = $taxonomy->label;
                        //}
                    }
                }

                return $taxonomies;
        }
        
        public static function getUnits() {
                return array("px" => "px",
                             "pt" => "pt",
                            );
        }
        
        public static function getOrdersBy() {
                return array("name"  => __("Name", "tags-all-in-one"),
                             "count" => __("Count", "tags-all-in-one"),
                            );
        }
        
        public static function getOrders() {
                return array("asc"  => __("Ascending", "tags-all-in-one"),
                             "desc" => __("Descending", "tags-all-in-one"),
                             "rand" => __("Random", "tags-all-in-one")
                            );
        }        
        
}
