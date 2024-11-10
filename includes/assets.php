<?php
/**
 * Assets for Dynamic Template Parts.
 *
 * @package dynamic-template-parts
 */

namespace Dynamic_Template_Parts\Assets;

use function Dynamic_Template_Parts\Helpers\get_switchable_template_part_options;

use const Dynamic_Template_Parts\PLUGIN_ROOT_DIR;
use const Dynamic_Template_Parts\PLUGIN_ROOT_FILE;
use const Dynamic_Template_Parts\Meta\DYNAMIC_TEMPLATE_PARTS_KEY;

/**
 * Bootstrap the assets by hooking into WordPress actions.
 */
function bootstrap(): void {
	add_action( 'enqueue_block_assets', __NAMESPACE__ . '\\enqueue_editor_scripts' );
}

/**
 * Enqueue scripts and styles for the WordPress editor.
 *
 * Ensures that JavaScript and CSS assets are properly enqueued for the editor.
 * Verifies that the build assets exist, and localizes data for switching template parts.
 *
 * @throws \Error If the asset path does not exist, indicating that the build has not been run.
 */
function enqueue_editor_scripts(): void {
	$asset_path = PLUGIN_ROOT_DIR . '/build/editor.asset.php';

	// Ensure build assets exist; throw error if they don't.
	if ( ! file_exists( $asset_path ) ) {
		throw new \Error(
			esc_html__( 'You need to run `npm start` or `npm run build` in the root of the plugin "Dynamic Template Parts" first.', 'dynamic-template-parts' )
		);
	}

	// Load asset metadata, including dependencies and version.
	$assets = include $asset_path;

	// Enqueue editor JavaScript.
	wp_enqueue_script(
		'dynamic-template-parts-editor-js',
		plugins_url( 'build/editor.js', PLUGIN_ROOT_FILE ),
		$assets['dependencies'],
		$assets['version'],
		false
	);

	// Set localization defaults and permissions.
	$localization = [];

	/**
	 * Filters the permission to determine if the current user can switch template parts.
	 *
	 * This filter allows developers to customize which users have access to the template part
	 * switching functionality in the editor. By default, only users with the `edit_posts` capability
	 * can switch template parts.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $can_switch Whether the current user has permission to switch template parts.
	 *                         Default is based on the `edit_posts` capability.
	 */
	$user_can_switch = apply_filters( 'dynamic_template_parts_user_can_switch', current_user_can( 'edit_posts' ) );

	// If the user has permission, load template part options for switching.
	if ( $user_can_switch ) {
		$localization = [
			'DynamicTemplatePartsOptions' => get_switchable_template_part_options(),
			'DynamicTemplatePartsKey'     => DYNAMIC_TEMPLATE_PARTS_KEY,
		];
	}

	// Localize data for dynamic template part options.
	wp_localize_script(
		'dynamic-template-parts-editor-js',
		'dynamicTemplateParts',
		$localization
	);

	// Enqueue editor CSS with cache-busting based on file modification time.
	wp_enqueue_style(
		'dynamic-template-parts-editor-css',
		plugins_url( 'build/editor.css', PLUGIN_ROOT_FILE ),
		[],
		(string) filemtime( plugin_dir_path( PLUGIN_ROOT_FILE ) . 'build/editor.css' )
	);
}
