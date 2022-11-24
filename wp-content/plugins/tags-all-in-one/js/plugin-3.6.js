(function() {
    tinymce.create('tinymce.plugins.tags_all_in_one_button', {
        init : function(ed, url) {            
            ed.addButton('tags_all_in_one_button', {
                title : 'Tags all in one',
                image : url+'/../images/shortcode-icon.png',
                onclick : function() {
                    var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
                    W = W - 80;
                    H = 300;                       
                    tb_show('Tags all in one','admin-ajax.php?action=tags_all_in_one_shortcode_generator&width=' + W + '&height=' + H );
               }
            });
        },
        createControl : function(n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('tags_all_in_one_button', tinymce.plugins.tags_all_in_one_button);
})();