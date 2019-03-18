<?php
/**
 * Automatic postMessage scripts calculation for Kirki controls.
 *
 * @package     Kirki
 * @category    Modules
 * @author      Ari Stathopoulos (@aristath)
 * @copyright   Copyright (c) 2019, Ari Stathopoulos (@aristath)
 * @license    https://opensource.org/licenses/MIT
 * @since       3.0.0
 */

namespace Kirki\Modules\Postmessage;

use Kirki\Core\Kirki;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds styles to the customizer.
 */
class Module {

	/**
	 * The object instance.
	 *
	 * @static
	 * @access private
	 * @since 3.0.0
	 * @var object
	 */
	private static $instance;

	/**
	 * Constructor.
	 *
	 * @access protected
	 * @since 3.0.0
	 */
	protected function __construct() {
		add_action( 'customize_preview_init', [ $this, 'postmessage' ] );
	}

	/**
	 * Gets an instance of this object.
	 * Prevents duplicate instances which avoid artefacts and improves performance.
	 *
	 * @static
	 * @access public
	 * @since 3.0.0
	 * @return object
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Enqueues the postMessage script
	 * and adds variables to it using the wp_localize_script function.
	 * The rest is handled via JS.
	 */
	public function postmessage() {
		$url = apply_filters(
			'kirki_package_url_module_postmessage',
			trailingslashit( Kirki::$url ) . 'vendor/kirki-framework/module-postmessage/src'
		);
		wp_enqueue_script( 'kirki_auto_postmessage', $url . '/assets/scripts/script.js', [ 'jquery', 'customize-preview' ], KIRKI_VERSION, true );
		$fields = Kirki::$fields;
		$data   = [];
		foreach ( $fields as $field ) {
			if ( isset( $field['transport'] ) && 'postMessage' === $field['transport'] && isset( $field['js_vars'] ) && ! empty( $field['js_vars'] ) && is_array( $field['js_vars'] ) && isset( $field['settings'] ) ) {
				$data[] = $field;
			}
		}
		wp_localize_script( 'kirki_auto_postmessage', 'kirkiPostMessageFields', $data );
		$extras = apply_filters( 'kirki_postmessage_script', false );
		if ( $extras ) {
			wp_add_inline_script( 'kirki_auto_postmessage', $extras, 'after' );
		}
	}
}
