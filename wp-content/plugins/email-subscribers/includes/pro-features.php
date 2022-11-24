<?php

add_filter( 'ig_es_registered_settings', 'ig_es_add_upsale', 10, 2 );

// Add additional tab "Comments" in Audience > Sync
add_filter( 'ig_es_sync_users_tabs', 'ig_es_add_sync_users_tabs', 11, 1 );


add_action( 'ig_es_sync_users_tabs_comments', 'ig_es_add_comments_tab_settings' );
add_action( 'ig_es_sync_users_tabs_woocommerce', 'ig_es_add_woocommerce_tab_settings' );

function ig_es_add_upsale( $fields ) {

	$active_plugins = (array) get_option( 'active_plugins', array() );
	if ( is_multisite() ) {
		$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	}

	if ( ! ( in_array( 'email-subscribers-premium/email-subscribers-premium.php', $active_plugins ) || array_key_exists( 'email-subscribers-premium/email-subscribers-premium.php', $active_plugins ) ) ) {

		// Security settings
		$field_security['es_upsale_security'] = array(
			'id'   => 'ig_es_blocked_domains',
			'type' => 'html',
			'name' => '',
			'html' => '<div class="es-upsale-image" style=""><a target="_blank" href="https://www.icegram.com/email-subscribers-starter/?utm_source=in_app&utm_medium=es_security_settings&utm_campaign=es_upsale"><img src="' . EMAIL_SUBSCRIBERS_URL . '/admin/images/es-captcha-2.png' . '"/></a></div>'
		);
		$fields['security_settings']          = array_merge( $fields['security_settings'], $field_security );

		// SMTP settings
		$field_smtp['es_upsale_smtp'] = array(
			'id'   => 'ig_es_blocked_domains',
			'type' => 'html',
			'name' => '<div class="es-smtp-label" style=""><a target="_blank" href="https://www.icegram.com/email-subscribers-starter/?utm_source=in_app&utm_medium=es_smtp&utm_campaign=es_upsale"><img src="' . EMAIL_SUBSCRIBERS_URL . '/admin/images/es-smtp-label.png' . '"/></a></div>',
			'html' => '<div class="es-upsale-image es-smtp-image" style=""><a target="_blank" href="https://www.icegram.com/email-subscribers-starter/?utm_source=in_app&utm_medium=es_smtp&utm_campaign=es_upsale"><img src="' . EMAIL_SUBSCRIBERS_URL . '/admin/images/es-smtp.png' . '"/></a></div>'
		);
		$fields['email_sending']      = array_merge( $fields['email_sending'], $field_smtp );

	}

	return $fields;
}

function ig_es_add_sync_users_tabs( $tabs ) {

	$tabs['comments'] = array(
		'name'             => __( 'Comments', 'email-subscribers' ),
		'indicator_option' => 'ig_es_show_sync_comment_users_indicator',
		'indicator_label'  => 'Starter'
	);

	$woocommerce_plugin       = 'woocommerce/woocommerce.php';
	$active_plugins = IG_Tracker::get_active_plugins();

	if ( in_array( $woocommerce_plugin, $active_plugins ) ) {
		$tabs['woocommerce'] = array(
			'name'             => __( 'WooCommerce', 'email-subscribers' ),
			'indicator_option' => 'ig_es_show_sync_woocommerce_users_indicator',
			'indicator_label'  => 'Starter'
		);
	}

	return $tabs;
}

function ig_es_add_comments_tab_settings( $tab_options ) {

	// If you want to hide once shown. Set it to 'no'
	// If you don't want to hide. do not use following code or set value as 'yes'
	/*
	if ( ! empty( $tab_options['indicator_option'] ) ) {
		update_option( $tab_options['indicator_option'], 'yes' ); // yes/no
	}
	*/

	$info = array(
		'type' => 'info'
	);

	ob_start();
	?>
    <div class="">
        <h2>Sync Comment Users</h2>
        <p>Quickly add to your mailing list when someone post a comment on your website.</p>
        <h2>How to setup?</h2>
        <p>Once you upgrade to <a href="https://www.icegram.com/email-subscribers-starter/?utm_source=in_app&utm_medium=woocommerce_sync&utm_campaign=es_upsale">Email Subscribers Starter</a>, you will have settings panel where you need to enable Comment user sync and select the list in which you want to add people whenever someone post a comment.</p>
        <hr>
        <p class="help">Checkout <a href="https://www.icegram.com/email-subscribers-starter/?utm_source=in_app&utm_medium=woocommerce_sync&utm_campaign=es_upsale">Email Subscribers Starter</a> Plan Now</p>
    </div>
	<?php

	$content = ob_get_clean();

	?>
    <a target="_blank" href="https://www.icegram.com/email-subscribers-starter/?utm_source=in_app&utm_medium=es_comment_upsale&utm_campaign=es_upsale">
        <img src=" <?php echo EMAIL_SUBSCRIBERS_URL . '/admin/images/es-comments.png' ?> "/>
    </a>
	<?php
	ES_Common::prepare_information_box( $info, $content );
}

function ig_es_add_woocommerce_tab_settings( $tab_options ) {

	$info = array(
		'type' => 'info',
	);

	ob_start();
	?>
    <div class="">
        <h2>Sync WooCommerce Customers</h2>
        <p>Are you using WooCommerce for your online business? You can use this integration to add to a specific list whenever someone purchase something from you</p>
        <h2>How to setup?</h2>
        <p>Once you upgrade to <a href="https://www.icegram.com/email-subscribers-starter/?utm_source=in_app&utm_medium=woocommerce_sync&utm_campaign=es_upsale">Email Subscribers Starter</a>, you will have following settings panel where you need to enable WooCommerce sync and select the list in which you want to add people whenever they purchase something
            from you.</p>
        <hr>
        <p class="help">Checkout <a href="https://www.icegram.com/email-subscribers-starter/?utm_source=in_app&utm_medium=woocommerce_sync&utm_campaign=es_upsale">Email Subscribers Starter</a> Now</p>
    </div>
	<?php $content = ob_get_clean(); ?>

    <a target="_blank" href="https://www.icegram.com/email-subscribers-starter/?utm_source=in_app&utm_medium=es_comment_upsale&utm_campaign=es_upsale">
        <img src=" <?php echo EMAIL_SUBSCRIBERS_URL . '/admin/images/woocommerce-sync.png' ?> "/>
    </a>

	<?php

	ES_Common::prepare_information_box( $info, $content );

	?>

	<?php
}


