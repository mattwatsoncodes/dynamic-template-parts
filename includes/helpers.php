<?php
/**
 * Helper Functions for Dynamic Template Parts.
 *
 * This file contains only helper functions and does not require a run method.
 *
 * @package dynamic-template-parts
 */

namespace Dynamic_Template_Parts\Helpers;

use WP_Block_Template;
use WP_Query;
use function get_block_templates;

/**
 * Retrieve the template associated with the current content type.
 *
 * Dynamically determines the appropriate block template based on the current post type context (e.g., page, post).
 * This function performs a query to populate the global `$_wp_current_template_id` variable, which is then used
 * to retrieve the correct block template.
 *
 * Available template types:
 * - get_index_template
 * - get_404_template
 * - get_archive_template
 * - get_post_type_archive_template
 * - get_author_template
 * - get_category_template
 * - get_tag_template
 * - get_taxonomy_template
 * - get_date_template
 * - get_home_template
 * - get_front_page_template
 * - get_privacy_policy_template
 * * get_page_template
 * - get_search_template
 * * get_single_template
 * - get_embed_template
 * - get_singular_template
 * - get_attachment_template
 *
 * Templates marked with an asterisk (*) are implemented in this function.
 *
 * @global string $_wp_current_template_id The ID of the current template.
 * @global WP_Query $wp_query The main WordPress query object.
 *
 * @return WP_Block_Template|null The matching block template for the current type, or `null` if none is found.
 */
function get_template_by_current_type(): ?WP_Block_Template {
	global $_wp_current_template_id, $wp_query;

	// Store the original query for resetting later.
	$original_query = $wp_query;

	// Process template for editing of singular posts or pages.
	// @codingStandardsIgnoreLine - WordPress.Security.NonceVerification.Recommended.
	if ( isset( $_GET['post'] ) ) {
		// @codingStandardsIgnoreLine - WordPress.Security.NonceVerification.Recommended.
		$post_id = sanitize_text_field( wp_unslash( $_GET['post'] ) );

		// Populate global query variables with WP_Query.
		// @codingStandardsIgnoreLine - WordPress.WP.GlobalVariablesOverride.Prohibited.
		$wp_query = new WP_Query( [
			'p'         => $post_id,
			'post_type' => 'any',
		] );
	}

	// Determine the appropriate template type based on post type.
	if ( get_post_type() === 'page' ) {
		get_page_template();
	} elseif ( is_single() ) {
		get_single_template();
	} else {
		return null;
	}

	// Restore the original query.
	// @codingStandardsIgnoreLine - WordPress.WP.GlobalVariablesOverride.Prohibited.
	$wp_query = $original_query;

	// Retrieve block template using the current template ID.
	$templates = get_block_templates( [ 'slug__in' => [ basename( $_wp_current_template_id ) ] ] );

	return $templates[0] ?? null;
}

/**
 * Recursively parses blocks and sub-blocks from the given content.
 *
 * @param array<int, array<string, mixed>> $blocks Array of parsed blocks from parse_blocks.
 * @return array<int, array<string, mixed>> Flattened array of all blocks and sub-blocks.
 */
function parse_all_blocks( array $blocks ): array {
    $all_blocks = [];

    foreach ( $blocks as $block ) {
        // Add the current block to the list
        $all_blocks[] = $block;

        // Check if the block has inner blocks and recursively parse them
        if ( ! empty( $block['innerBlocks'] ) ) {
            $inner_blocks = parse_all_blocks( $block['innerBlocks'] );
            $all_blocks   = array_merge( $all_blocks, $inner_blocks );
        }
    }

    return $all_blocks;
}

/**
 * Retrieve switchable template part options for dynamic parts.
 *
 * Builds an array of template part options, including available switchable parts and applicable metadata
 * (e.g., area, theme, and title). This allows certain template parts to be replaced with other compatible parts.
 *
 * @return array<string, mixed> An associative array containing switchable template part options.
 */
function get_switchable_template_part_options(): array {
	$switchable_template_parts = [];
	$template = get_template_by_current_type();

	if ( ! $template ) {
		return $switchable_template_parts;
	}

	// Parse blocks from the template content.
	$blocks     = parse_blocks( $template->content );
	$all_blocks = parse_all_blocks( $blocks );

	// Retrieve all template parts.
	$template_parts = get_block_templates(
		[ 'post_type' => 'wp_template_part' ],
		'wp_template_part'
	);

	// Sort template parts by title before entering the loop.
	usort( $template_parts, function ( $a, $b ) {
		return strcmp( $a->title, $b->title );
	} );

	// Populate options for each switchable template part.
	foreach ( $all_blocks as $block ) {
		// Ensure the block is a switchable template part.
		if ( 'core/template-part' !== $block['blockName'] || empty( $block['attrs']['isSwitchable'] ) ) {
			continue;
		}

		$slug  = (string) $block['attrs']['slug'];
		$theme = $block['attrs']['theme'] ?? '';
		$id    = $theme . '//' . $slug;
		$area  = $block['attrs']['area'] ?? '';

		$switchable_template_parts[ $id ] = [
			'area'                   => $area,
			'limitSwitchableParts'   => $block['attrs']['limitSwitchableParts'] ?? false,
			'limitedSwitchableParts' => $block['attrs']['limitedSwitchableParts'] ?? [],
			'options'                => [],
			'title'                  => '',
			'slug'                   => $slug,
			'theme'                  => $theme,
			'useArea'                => $block['attrs']['useArea'] ?? false,
		];

		// Process each available template part.
		foreach ( $template_parts as $template_part ) {
			// Populate title if this is the current template part.
			if ( $id === $template_part->id ) {
				$switchable_template_parts[ $id ]['title'] = $template_part->title;
			}

			// Validate that template part is published and matches area if needed.
			if ( 'publish' !== $template_part->status ||
				( ! $switchable_template_parts[ $id ]['useArea'] && $template_part->area !== $area ) ||
				( $switchable_template_parts[ $id ]['limitSwitchableParts'] &&
					! in_array( $template_part->id, $switchable_template_parts[ $id ]['limitedSwitchableParts'], true ) &&
					$id !== $template_part->id )
			) {
				continue;
			}

			// Add template part to the options list.
			$switchable_template_parts[ $id ]['options'][] = [
				'area'    => $template_part->area,
				'default' => $id === $template_part->id,
				'id'      => $template_part->id,
				'slug'    => $template_part->slug,
				'title'   => $template_part->title,
				'theme'   => $template_part->theme,
			];
		}
	}

	return $switchable_template_parts;
}
