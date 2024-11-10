<?php
/**
 * Handle the switcher functionality.
 *
 * @package dynamic-template-parts
 */

namespace Dynamic_Template_Parts\Switcher;

use const Dynamic_Template_Parts\Meta\DYNAMIC_TEMPLATE_PARTS_KEY;

/**
 * Bootstrap.
 */
function bootstrap(): void {
	add_filter( 'render_block_data', __NAMESPACE__ . '\\override_template_part_attributes' );
}

/**
 * Modify the parsed block attributes to apply dynamic template part switching.
 *
 * This function checks if the current block is a `core/template-part` block and, if so,
 * applies stored meta data to override certain block attributes (such as area, id, slug, title, and theme).
 * This allows dynamically switching template parts based on custom metadata set for the current post.
 *
 * @param array<mixed> $parsed_block An associative array of the block attributes and settings being rendered.
 *                                   Expected attributes include:
 *                                   - 'blockName' (string): The name of the block.
 *                                   - 'attrs' (array): The block attributes, including 'slug' and optional 'theme'.
 *
 * @global \WP_Post $post The current post object, required to retrieve post meta.
 *
 * @return array<mixed> The modified or original block attributes and settings.
 *                      Returns $parsed_block as modified with meta data if criteria are met;
 *                      otherwise, the original $parsed_block.
 */
function override_template_part_attributes( array $parsed_block ): array {
	global $post;

	if ( ! $post ) {
		return $parsed_block;
	}

	if ( ! isset( $parsed_block['blockName'] ) || 'core/template-part' !== $parsed_block['blockName'] ) {
		return $parsed_block;
	}

	// Retrieve template part switcher data stored in post meta.
	$switcher_data = (array) get_post_meta( $post->ID, DYNAMIC_TEMPLATE_PARTS_KEY, true );

	$slug  = (string) $parsed_block['attrs']['slug'];
	$theme = $parsed_block['attrs']['theme'] ?? '';

	// Construct an identifier for the current block.
	$id = $theme . '//' . $slug;

	foreach ( $switcher_data as $key => $option ) {
		$key    = (string) $key ?? ''; // @phpstan-ignore-line.
		$option = (array) $option ?? []; // @phpstan-ignore-line.

		// Match identifier and apply stored attributes if found.
		if ( count( $option ) === 0 || empty( $key ) || $key !== $id ) {
			continue;
		}

		$parsed_block['attrs']['area']  = $option['area'];
		$parsed_block['attrs']['id']    = $option['id'];
		$parsed_block['attrs']['slug']  = $option['slug'];
		$parsed_block['attrs']['title'] = $option['title'];
		$parsed_block['attrs']['theme'] = $option['theme'];
		break;
	}

	return $parsed_block;
}
