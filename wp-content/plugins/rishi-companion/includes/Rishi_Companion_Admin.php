<?php
/**
 * Admin area settings and hooks.
 *
 * @package Rishi_Companion
 */

namespace Rishi;

defined( 'ABSPATH' ) || exit;

/**
 * Global Settings.
 */
class Rishi_Companion_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialization.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function init() {

		// Initialize hooks.
		$this->init_hooks();

		// Allow 3rd party to remove hooks.
		do_action( 'rishi_companion_admin_unhook', $this );
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @return void
	 */
	private function init_hooks() {
		// Admin Scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'category_add_form_fields', array( $this, 'rishi_companion_add_color_uploader' ) );
		add_action( 'created_category', array( $this, 'rishi_companion_save_category_color' ) , 10, 2 );
		add_action( 'category_edit_form_fields', array( $this, 'rishi_companion_update_category_color' ) , 10, 2 );
		add_action( 'edited_category', array( $this, 'rishi_companion_updated_category_color' ) , 10, 2 );
		add_action( 'wp_ajax_rc_local_font_flush', array( $this, 'ajax_delete_fonts_folder' ) );

		if( get_theme_mod( 'ed_favicon', 'yes' ) === 'yes' ){
			add_action( 'admin_head', array( $this, 'rishi_remove_favicon_request' ), 10 );
			add_action( 'wp_head', [ $this, 'rishi_remove_favicon_request' ], 10 );
		}

	}

	/**
     * Disable Automatic Favicon Request
    */
    public function rishi_remove_favicon_request() {
        echo '<link rel="icon" href="data:,">';
    }

	/**
	 * Enqueue Admin Scripts
	 *
	 * @return void
	 */
	public function enqueue_scripts() {

		$manager = new Rishi_Companion_Plugin_Manager();
		$free_plugins = $manager->get_config();
		
		$screen = get_current_screen();
        $global_deps = include_once plugin_dir_path( RISHI_COMPANION_PLUGIN_FILE ) . '/assets/build/dashboard.asset.php';
        
        if ( 'appearance_page_rishi-dashboard' === $screen->id ) {

            // Recipe global screen assets.
            wp_register_script( 'rishi-companion-dashboard', plugin_dir_url( RISHI_COMPANION_PLUGIN_FILE ) . 'assets/build/dashboard.js', $global_deps['dependencies'], $global_deps['version'], true );

            // Add localization vars.
            wp_localize_script(
                'rishi-companion-dashboard',
                'RishiCompanionDashboard',
                array(
					'ajaxURL'   	   => esc_url( admin_url('admin-ajax.php') ),
					'adminURL'  	   => esc_url( admin_url() ),
                    'siteURL'          => esc_url( home_url( '/' ) ),
                    'pluginUrl'        => esc_url( plugin_dir_url( RISHI_COMPANION_PLUGIN_FILE ) ),
                    'pluginPRO'        => class_exists( 'Rishi\Rishi_Pro' ),
                    'pluginName'       => $free_plugins,
                    'customizeURL'     => admin_url('/customize.php?autofocus'),
                    'plugin_data' 	   => apply_filters( 'rishi_companion_dashboard_localizations', [] ),
                )
            );
            wp_enqueue_script( 'rishi-companion-dashboard' );
        }

		//color picker option in category page
		if ( 'edit-category' === $screen->id ) {
			wp_enqueue_style( 'wp-color-picker' );

			wp_enqueue_script( 'rishi-companion-color-uploader', plugin_dir_url( RISHI_COMPANION_PLUGIN_FILE ) . 'assets/admin/other/colorUploader.js', array( 'wp-color-picker' ), false, true );

		}

	}

	/**
	 * Adding category color
	*/
	public function rishi_companion_add_color_uploader ( $taxonomy ) { ?>
		
		<tr class="form-field term-group-wrap">
			<th scope="row">
				<label for="rc-uploader"><?php esc_html_e( 'Background Color', 'rishi-companion' ); ?></label>
			</th>
			<td>
			<p>
				<input type="text" value="#307ac9" name="rc-uploader" class="rc-uploader" data-default-color="#307ac9" />
			</p>
			</td>
		</tr>
		<tr class="form-field term-group-wrap">
			<th scope="row">
				<label for="rc-uploader"><?php esc_html_e( 'Text Color', 'rishi-companion' ); ?></label>
			</th>
			<td>
			<p>
				<input type="text" value="#ffffff" name="rc-text-uploader" class="rc-uploader" data-default-color="#ffffff" />
			</p>
			</td>
		</tr>
	<?php
	}

	/*
	* Save the form field
	* @since 1.0.0
	*/
	public function rishi_companion_save_category_color( $term_id, $tt_id ) {
		if ( isset( $_POST['rc-uploader'] ) && '' !== $_POST['rc-uploader'] ) {
			$image = $_POST['rc-uploader'];
			add_term_meta( $term_id, 'rc-uploader', $image, true );
		}
		if ( isset( $_POST['rc-text-uploader'] ) && '' !== $_POST['rc-text-uploader'] ) {
			$image = $_POST['rc-text-uploader'];
			add_term_meta( $term_id, 'rc-text-uploader', $image, true );
		}
	}

	/*
	* Edit the form field
	* @since 1.0.0
	*/
	public function rishi_companion_update_category_color( $term, $taxonomy ) { ?>
		<tr class="form-field term-group-wrap">
			<th scope="row">
				<label for="rc-uploader"><?php esc_html_e( 'Background Color', 'rishi-companion' ); ?></label>
			</th>
			<?php $color_id = get_term_meta ( $term -> term_id, 'rc-uploader', true ); ?>
			<td>
				<p>
					<input type="text" value="<?php if( $color_id ) echo $color_id; ?>" name="rc-uploader" class="rc-uploader" data-default-color="#307ac9" />
				</p>
			</td>
			</tr>
			<br />
		<tr class="form-field term-group-wrap">
			<th scope="row">
				<label for="rc-uploader"><?php esc_html_e( 'Text Color', 'rishi-companion' ); ?></label>
			</th>
			<?php $textcolor_id = get_term_meta ( $term -> term_id, 'rc-text-uploader', true ); ?>
			<td>
				<p>
					<input type="text" value="<?php if( $textcolor_id ) echo $textcolor_id; ?>" name="rc-text-uploader" class="rc-uploader" data-default-color="#ffffff" />
				</p>
			</td>
		</tr>
		<?php 
	}

	public function rishi_companion_updated_category_color( $term_id, $tt_id ) {
		if ( isset( $_POST['rc-uploader'] ) && '' !== $_POST['rc-uploader'] ) {
			$bgcolor = $_POST['rc-uploader'];
			update_term_meta( $term_id, 'rc-uploader', $bgcolor );
		} else {
			update_term_meta( $term_id, 'rc-uploader', '' );
		}

		if ( isset( $_POST['rc-text-uploader'] ) && '' !== $_POST['rc-text-uploader'] ) {
			$text_color = $_POST['rc-text-uploader'];
			update_term_meta( $term_id, 'rc-text-uploader', $text_color );
		} else {
			update_term_meta( $term_id, 'rc-text-uploader', '' );
		}
	}

	/**
	 * Reset font folder
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_delete_fonts_folder() {
		// Check request.
		if ( ! check_ajax_referer( 'rt-flush-fonts', 'nonce', false ) ) {
			wp_send_json_error( 'invalid_nonce' );
		}
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_send_json_error( 'invalid_permissions' );
		}
		if ( class_exists( '\RishiCompanionWebFontLoader' ) ) {
			$font_loader = new \RishiCompanionWebFontLoader( '' );
			$removed = $font_loader->delete_fonts_folder();
			if ( ! $removed ) {
				wp_send_json_error( 'failed_to_flush' );
			}
			wp_send_json_success();
		}
		wp_send_json_error( 'no_font_loader' );
	}
}