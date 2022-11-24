<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'IG_Feedback_V_1_0_0', false ) ) {
	include_once dirname( __FILE__ ) . '/class-ig-feedback-v-1-0-0.php';
}

if ( ! class_exists( 'IG_Deactivation_Survey' ) ) {
	/**
	 * Icegram Deactivation Survey.
	 *
	 * This prompts the user for more details when they deactivate the plugin.
	 *
	 * @version    1.0.0
	 * @package    Icegram
	 * @author     Malay Ladu
	 * @license    GPL-2.0+
	 * @copyright  Copyright (c) 2019
	 */
	class IG_Deactivation_Survey extends IG_Feedback_V_1_0_0 {

		public function __construct( $name = '', $plugin = '', $plugin_abbr = 'ig_fb' ) {
		    parent::__construct($name, $plugin, $plugin_abbr);

			// Don't run deactivation survey on dev sites.
			if ( ! $this->can_show_feedback_widget()) {
				return;
			}

			add_action( 'admin_print_scripts', array( $this, 'js' ), 20 );
			add_action( 'admin_print_scripts', array( $this, 'css' ) );
			add_action( 'admin_footer', array( $this, 'modal' ) );
		}

		/**
		 * Survey javascript.
		 *
		 * @since 1.0.0
		 */
		public function js() {

			if ( ! $this->is_plugin_page() ) {
				return;
			}
			?>
            <script type="text/javascript">
				jQuery(function ($) {
					var $deactivateLink = $('#the-list').find('[data-slug="<?php echo $this->plugin; ?>"] span.deactivate a'),
						$overlay = $('#ig-deactivate-survey-<?php echo $this->plugin; ?>'),
						$form = $overlay.find('form'),
						formOpen = false;
					// Plugin listing table deactivate link.
					$deactivateLink.on('click', function (event) {
						event.preventDefault();
						$overlay.css('display', 'table');
						formOpen = true;
						$form.find('.ig-deactivate-survey-option:first-of-type input[type=radio]').focus();
					});
					// Survey radio option selected.
					$form.on('change', 'input[type=radio]', function (event) {
						event.preventDefault();
						$form.find('input[type=text], .error').hide();
						$form.find('.ig-deactivate-survey-option').removeClass('selected');
						$(this).closest('.ig-deactivate-survey-option').addClass('selected').find('input[type=text]').show();
					});
					// Survey Skip & Deactivate.
					$form.on('click', '.ig-deactivate-survey-deactivate', function (event) {
						event.preventDefault();
						location.href = $deactivateLink.attr('href');
					});
					// Survey submit.
					$form.submit(function (event) {
						event.preventDefault();
						if (!$form.find('input[type=radio]:checked').val()) {
							$form.find('.ig-deactivate-survey-footer').prepend('<span class="error"><?php echo esc_js( __( 'Please select an option', 'email-subscribers' ) ); ?></span>');
							return;
						}

						var data = {
							action: '<?php echo $this->ajax_action; ?>',
							feedback: [{
                                type: 'radio',
								slug: 'why-are-ypu-deactivating-email-subscribers',
								title: 'Why are you deactivating Email Subscribers',
								value: $form.find('.selected input[type=radio]').attr('data-option-slug'),
								details: $form.find('.selected input[type=text]').val()
							}],

							event: 'esfree.plugin.deactivation',
						};

						var submitSurvey = $.post(ajaxurl, data);
						submitSurvey.always(function () {
							location.href = $deactivateLink.attr('href');
						});
					});
					// Exit key closes survey when open.
					$(document).keyup(function (event) {
						if (27 === event.keyCode && formOpen) {
							$overlay.hide();
							formOpen = false;
							$deactivateLink.focus();
						}
					});
				});
            </script>
			<?php
		}

		/**
		 * Survey CSS.
		 *
		 * @since 1.0.0
		 */
		public function css() {

			if ( ! $this->is_plugin_page() ) {
				return;
			}
			?>
            <style type="text/css">
                .ig-deactivate-survey-modal {
                    display: none;
                    table-layout: fixed;
                    position: fixed;
                    z-index: 9999;
                    width: 100%;
                    height: 100%;
                    text-align: center;
                    font-size: 14px;
                    top: 0;
                    left: 0;
                    background: rgba(0, 0, 0, 0.8);
                }

                .ig-deactivate-survey-wrap {
                    display: table-cell;
                    vertical-align: middle;
                }

                .ig-deactivate-survey {
                    background-color: #fff;
                    max-width: 550px;
                    margin: 0 auto;
                    padding: 30px;
                    text-align: left;
                }

                .ig-deactivate-survey .error {
                    display: block;
                    color: red;
                    margin: 0 0 10px 0;
                }

                .ig-deactivate-survey-title {
                    display: block;
                    font-size: 18px;
                    font-weight: 700;
                    text-transform: uppercase;
                    border-bottom: 1px solid #ddd;
                    padding: 0 0 18px 0;
                    margin: 0 0 18px 0;
                }

                .ig-deactivate-survey-title span {
                    color: #999;
                    margin-right: 10px;
                }

                .ig-deactivate-survey-desc {
                    display: block;
                    font-weight: 600;
                    margin: 0 0 18px 0;
                }

                .ig-deactivate-survey-option {
                    margin: 0 0 10px 0;
                }

                .ig-deactivate-survey-option-input {
                    margin-right: 10px !important;
                }

                .ig-deactivate-survey-option-details {
                    display: none;
                    width: 90%;
                    margin: 10px 0 0 30px;
                }

                .ig-deactivate-survey-footer {
                    margin-top: 18px;
                }

                .ig-deactivate-survey-deactivate {
                    float: right;
                    font-size: 13px;
                    color: #ccc;
                    text-decoration: none;
                    padding-top: 7px;
                }
            </style>
			<?php
		}

		/**
		 * Survey modal.
		 *
		 * @since 1.0.0
		 */
		public function modal() {

			if ( ! $this->is_plugin_page() ) {
				return;
			}

			$options = array(
				1 => array(
					'title' => esc_html__( 'I no longer need the plugin', 'email-subscribers' ),
					'slug'  => 'i-no-longer-need-the-plugin'
				),
				2 => array(
					'title'   => esc_html__( 'I\'m switching to a different plugin', 'email-subscribers' ),
					'slug'    => 'i-am-switching-to-a-different-plugin',
					'details' => esc_html__( 'Please share which plugin', 'email-subscribers' ),
				),
				3 => array(
					'title' => esc_html__( 'I couldn\'t get the plugin to work', 'email-subscribers' ),
					'slug'  => 'i-could-not-get-the-plugin-to-work'
				),
				4 => array(
					'title' => esc_html__( 'It\'s a temporary deactivation', 'email-subscribers' ),
					'slug'  => 'it-is-a-temporary-deactivation'
				),
				5 => array(
					'title'   => esc_html__( 'Other', 'email-subscribers' ),
					'slug'    => 'other',
					'details' => esc_html__( 'Please share the reason', 'email-subscribers' ),
				),
			);
			?>
            <div class="ig-deactivate-survey-modal" id="ig-deactivate-survey-<?php echo $this->plugin; ?>">
                <div class="ig-deactivate-survey-wrap">
                    <form class="ig-deactivate-survey" method="post">
                        <span class="ig-deactivate-survey-title"><span class="dashicons dashicons-testimonial"></span><?php echo ' ' . esc_html__( 'Quick Feedback', 'email-subscribers' ); ?></span>
                        <span class="ig-deactivate-survey-desc"><?php echo sprintf( esc_html__( 'If you have a moment, please share why you are deactivating %s:', 'email-subscribers' ), $this->name ); ?></span>
                        <div class="ig-deactivate-survey-options">
							<?php foreach ( $options as $id => $option ) : ?>
                                <div class="ig-deactivate-survey-option">
                                    <label for="ig-deactivate-survey-option-<?php echo $this->plugin; ?>-<?php echo $id; ?>" class="ig-deactivate-survey-option-label">
                                        <input id="ig-deactivate-survey-option-<?php echo $this->plugin; ?>-<?php echo $id; ?>" class="ig-deactivate-survey-option-input" type="radio" name="code" value="<?php echo $id; ?>" data-option-slug="<?php echo $option['slug']; ?>" />
                                        <span class="ig-deactivate-survey-option-reason"><?php echo $option['title']; ?></span>
                                    </label>
									<?php if ( ! empty( $option['details'] ) ) : ?>
                                        <input class="ig-deactivate-survey-option-details" type="text" placeholder="<?php echo $option['details']; ?>"/>
									<?php endif; ?>
                                </div>
							<?php endforeach; ?>
                        </div>
                        <div class="ig-deactivate-survey-footer">
                            <button type="submit" class="ig-deactivate-survey-submit button button-primary button-large"><?php echo sprintf( esc_html__( 'Submit %s Deactivate', 'email-subscribers' ), '&amp;' ); ?></button>
                            <a href="#" class="ig-deactivate-survey-deactivate"><?php echo sprintf( esc_html__( 'Skip %s Deactivate', 'email-subscribers' ), '&amp;' ); ?></a>
                        </div>
                    </form>
                </div>
            </div>
			<?php
		}

		/**
         * Is plugin in development mode?
		 * @return bool
		 */
		public function is_dev_mode() {

			if ( defined( 'IG_ES_DEV_MODE' ) && IG_ES_DEV_MODE ) {
				return true;
			}

			return false;
        }

		/**
         * Can we show feedback widget in this environment
         *
		 * @return bool
		 */
        public function can_show_feedback_widget() {

            // Is development mode? Enable it.
            if($this->is_dev_mode()) {
                return true;
            }

            // Don't show on dev setup if dev mode is off.
		    if($this->is_dev_url()) {
		        return false;
            }

		    return true;
        }

		/**
		 * Checks if current admin screen is the plugins page.
		 *
		 * @return bool
		 * @since 1.0.0
		 */
		public function is_plugin_page() {

			$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
			if ( empty( $screen ) ) {
				return false;
			}

			return ( ! empty( $screen->id ) && in_array( $screen->id, array( 'plugins', 'plugins-network' ), true ) );
		}

		/**
         * Get additional plugin specific information
         */
		public function get_additional_info() {
			$additional_info = array();

			$additional_info['ig_es_version'] = ES_PLUGIN_VERSION;

			return $additional_info;
		}

	}
} // End if().