<?php
/*
Author: Marcin Gierada
Author URI: http://www.teastudio.pl/
Author Email: m.gierada@teastudio.pl
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

?>
		
<style type="text/css">
table {font-size:12px;}
</style>
<script type="text/javascript">
function insert_shortcode() {
    var taxonomies = jQuery("#tags-form input[name='taxonomy']:checked").map(function() {
        return jQuery(this).val();
    }).get().join();

    var shortcode = '[tags_all_in_one';    
    
    jQuery('#tags-form').find(':input').filter(function() {
        var val = null;        
        
        if(this.type != "button") {  
            if(jQuery.trim( this.name ) != "taxonomy") {
                if(this.type == "checkbox") {
                    val = this.checked ? "true" : "false";                
                }else {
                    val = this.value;
                }

                shortcode += ' '+jQuery.trim( this.name )+'="'+jQuery.trim( val )+'"';
            }
        }
    });
    
    if( taxonomies.length > 0) {
        shortcode += ' taxonomy="'+taxonomies.toString()+'"';
    }    

    shortcode +=']';

    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
    tb_remove();
}
</script>

<div class="widget" id="tags-form">
    <table cellspacing="5" cellpadding="5">     
        <tr>
            <td align="left"><?php _e('Limit', 'tags-all-in-one'); ?>:</td>
            <td>
                <input type="text" name="number" id="number" value="20" size="5">
            </td>
        </tr>   
        <tr>
            <td align="left"><?php _e('Smallest font size', 'tags-all-in-one'); ?>:</td>
            <td>
                <input type="text" name="smallest" id="smallest" value="12" size="5">
            </td>
        </tr>    
        <tr>
            <td align="left"><?php _e('Largest font size', 'tags-all-in-one'); ?>:</td>
            <td>
                <input type="text" name="largest" id="largest" value="24" size="5">
            </td>
        </tr>         
        <tr>
            <td align="left"><?php _e('Unit of font size', 'tags-all-in-one'); ?>:</td>
            <td>
                <select name="unit" id="unit" class="select">
                <?php          
                        $unit_list = Tags_All_In_One_Utils::getUnits();
                        foreach($unit_list as $key => $list) {
                            echo "<option value=\"". $key ."\">". $list ."</option>";
                        }
                ?>   
                </select>	
            </td>
        </tr> 
        <tr>
            <td align="left"><?php _e('Separator', 'tags-all-in-one'); ?>:</td>
            <td>
                <input type="text" name="separator" id="separator" value="" size="5">
            </td>
        </tr>         
        <tr>
            <td align="left"><?php _e('Order by', 'tags-all-in-one'); ?>:</td>
            <td>
                <select name="orderby" id="orderby" class="select">
                <?php          
                        $orderby_list = Tags_All_In_One_Utils::getOrdersBy();
                        foreach($orderby_list as $key => $list) {
                            echo "<option value=\"". $key ."\">". $list ."</option>";
                        }
                ?>   
                </select>	
            </td>
        </tr>          
        <tr>
            <td align="left"><?php _e('Order', 'tags-all-in-one'); ?>:</td>
            <td>
                <select name="order" id="order" class="select">
                <?php          
                        $order_list = Tags_All_In_One_Utils::getOrders();
                        foreach($order_list as $key => $list) {
                            echo "<option value=\"". $key ."\">". $list ."</option>";
                        }
                ?>   
                </select>	
            </td>
        </tr> 
        <tr>
            <td colspan="2">
                <fieldset style="border:1px solid #dfdfdf;padding:0 10px 10px 10px;">
                    <legend><strong><?php _e('Select what you want to display', 'tags-all-in-one') ?></strong></legend>
                    

                    <p><?php _e('Tags from', 'tags-all-in-one'); ?>:</p>  
                    <ul>
                    <?php          
                            $taxanomies_list = Tags_All_In_One_Utils::getTaxonomies();
                            foreach($taxanomies_list as $key => $list) {
                                echo "<li><input type=\"checkbox\" value=\"". $key ."\" name=\"taxonomy\" id=\"taxonomy-". $key ."\"><label for=\"taxonomy-". $key ."\">". $list ."</label></li>";
                            }
                    ?>                     
                    </ul>
                    <p><?php _e('or', "wp-posts-carousel") ?></p>
                    <input type="checkbox" value="1" name="post" id="post"> <?php _e('tags from this content', 'tags-all-in-one'); ?></td>
                </fieldset>
            </td>
        </tr>  
        <tr>
            <td colspan="2">
                <input type="button" class="button button-primary button-large" value="<?php _e('Insert Shortcode', 'tags-all-in-one') ?>" onClick="insert_shortcode();">
            </td>
        </tr>         
    </table>
</div>
