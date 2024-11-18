<?php
/**
 * Plugin namespace.
 *
 * @package dynamic-template-parts
 */

namespace Dynamic_Template_Parts;

/**
 * Bootstrap.
 */
function bootstrap(): void {
	add_filter( 'plugin_action_links', __NAMESPACE__ . '\\add_plugin_list_table_links', 10, 2 );

	Assets\bootstrap();
	Meta\bootstrap();
	Switcher\bootstrap();
}

/**
 * Add the plugin settings link in the plugin list table.
 *
 * @param string[] $links An array of plugin action links.
 * @param string   $file  Path to the plugin file relative to the plugins directory.
 *
 * @return string[] Modified array of action links with the added support link.
 */
function add_plugin_list_table_links( array $links, string $file ): array {
    if ( plugin_basename( PLUGIN_ROOT_FILE ) !== $file ) {
		return $links;
	}

	$support_link = sprintf(
		/* translators: support URL, support link text */
		'<a href="%s" target="_blank">%s</a>',
		esc_url( 'https://mattwatson.blog/dynamic-template-parts/#support/' ),
		esc_html__( 'Support', 'dynamic-template-parts' )
	);

	array_unshift( $links, $support_link );

    return $links;
}
