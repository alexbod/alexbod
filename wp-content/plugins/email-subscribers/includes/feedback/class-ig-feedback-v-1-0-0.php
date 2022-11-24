<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'IG_Feedback_V_1_0_0' ) ) {
	/**
	 * Icegram Deactivation Survey.
	 *
	 * This prompts the user for more details when they deactivate the plugin.
	 *
	 * @version    1.0
	 * @package    Icegram
	 * @author     Malay Ladu
	 * @license    GPL-2.0+
	 * @copyright  Copyright (c) 2019
	 */
	class IG_Feedback_V_1_0_0 {

		/**
		 * The API URL where we will send feedback data.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $api_url = 'https://api.icegram.com/store/feedback/'; // Production

		/**
		 * Name for this plugin.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $name;

		/**
		 * Unique slug for this plugin.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $plugin;

		/**
		 * Ajax action where feedback data will post
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $ajax_action;

		/**
		 * Plugin Abbreviation
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $plugin_abbr;

		/**
		 * Primary class constructor.
		 *
		 * @param string $name Plugin name.
		 * @param string $plugin Plugin slug.
		 *
		 * @since 1.0.0
		 */
		public function __construct( $name = '', $plugin = '', $plugin_abbr = 'ig_fb' ) {
			$this->name        = $name;
			$this->plugin      = $plugin;
			$this->plugin_abbr = $plugin_abbr;
			$this->ajax_action = 'ig-submit-feedback';

			add_action( 'wp_ajax_' . $this->ajax_action, array( $this, 'submit_feedback' ) );
		}

		/**
		 * It's a default constant for Feedback.
		 *
		 * @return bool
		 */
		public function is_dev_mode() {

			if ( defined( 'IG_FEEDBACK_DEV_MODE' ) && IG_FEEDBACK_DEV_MODE ) {
				return true;
			}

			return false;
		}

		/**
		 * Get API Url. It's a different for dev & production mode
		 *
		 * @return string
		 */
		public function get_api_url() {

			if ( $this->is_dev_mode() ) {
				$this->api_url = 'http://192.168.0.130:9094/store/feedback/'; // Malay: Development
			}

			return $this->api_url;
		}

		/**
		 * Checks if current site is a development one.
		 *
		 * @return bool
		 * @since 1.2.0
		 */
		public function is_dev_url() {

			$url          = network_site_url( '/' );
			$is_local_url = false;

			// Trim it up
			$url = strtolower( trim( $url ) );

			// Need to get the host...so let's add the scheme so we can use parse_url
			if ( false === strpos( $url, 'http://' ) && false === strpos( $url, 'https://' ) ) {
				$url = 'http://' . $url;
			}
			$url_parts = parse_url( $url );
			$host      = ! empty( $url_parts['host'] ) ? $url_parts['host'] : false;
			if ( ! empty( $url ) && ! empty( $host ) ) {
				if ( false !== ip2long( $host ) ) {
					if ( ! filter_var( $host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
						$is_local_url = true;
					}
				} elseif ( 'localhost' === $host ) {
					$is_local_url = true;
				}

				$tlds_to_check = array( '.dev', '.local', ':8888' );
				foreach ( $tlds_to_check as $tld ) {
					if ( false !== strpos( $host, $tld ) ) {
						$is_local_url = true;
						continue;
					}

				}
				if ( substr_count( $host, '.' ) > 1 ) {
					$subdomains_to_check = array( 'dev.', '*.staging.', 'beta.', 'test.' );
					foreach ( $subdomains_to_check as $subdomain ) {
						$subdomain = str_replace( '.', '(.)', $subdomain );
						$subdomain = str_replace( array( '*', '(.)' ), '(.*)', $subdomain );
						if ( preg_match( '/^(' . $subdomain . ')/', $host ) ) {
							$is_local_url = true;
							continue;
						}
					}
				}
			}

			return $is_local_url;
		}

		/**
		 * Collect Meta information
		 *
		 * @return array|mixed|void
		 */
		public function get_additional_info() {
			return array();
		}

		/**
		 * Hook to ajax_action
		 *
		 * Send feedback to server
		 */
		function submit_feedback() {

			$data = ! empty( $_POST ) ? $_POST : array();

			$data['site'] = esc_url( home_url() );

			$meta_info = array(
				'plugin'     => sanitize_key( $this->plugin ),
				'locale'     => get_locale(),
				'wp_version' => get_bloginfo( 'version' )
			);

			$additional_info = $this->get_additional_info(); // Get Additional meta information

			if ( is_array( $meta_info ) && count( $meta_info ) > 0 ) {
				$meta_info = $meta_info + $additional_info;
			}

			$data['meta'] = $meta_info;

			$args = array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $data,
				'blocking'  => false
			);

			$response = wp_remote_post( $this->get_api_url(), $args );

			$result['status'] = 'success';
			if ( $response instanceof WP_Error ) {
				$error_message     = $response->get_error_message();
				$result['status']  = 'error';
				$result['message'] = $error_message;
			}

			die( json_encode( $result ) );

		}

	}
} // End if().