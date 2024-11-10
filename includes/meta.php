<?php
/**
 * Register meta fields.
 *
 * @package dynamic-template-parts
 */

namespace Dynamic_Template_Parts\Meta;

use const Dynamic_Template_Parts\PLUGIN_PREFIX;

const DYNAMIC_TEMPLATE_PARTS_KEY = PLUGIN_PREFIX . 'data';

/**
 * Bootstrap the meta fields registration.
 */
function bootstrap(): void {
	add_action( 'init', __NAMESPACE__ . '\\register_meta_fields' );
}

/**
 * Register meta fields for storing dynamic template part data.
 */
function register_meta_fields(): void {
	register_meta(
		'post',
		DYNAMIC_TEMPLATE_PARTS_KEY,
		[
			'auth_callback' => '__return_true',
			'default'       => [],
			'single'        => true,
			'type'          => 'object',
			'show_in_rest'  => [
				'schema' => [
					'type'                 => 'object',
					'additionalProperties' => [
						'type'       => 'object',
						'properties' => [
							'area'    => [ 'type' => 'string' ],
							'default' => [ 'type' => 'boolean' ],
							'id'      => [ 'type' => 'string' ],
							'slug'    => [ 'type' => 'string' ],
							'title'   => [ 'type' => 'string' ],
							'theme'   => [ 'type' => 'string' ],
						],
					],
				],
			],
		]
	);
}
