<?php
/**
 * Plugin Name:       Dynamic Template Parts
 * Plugin URI:        https://mattwatson.blog/dynamic-template-parts
 * Description:       Maximize your site's flexibility — swap Template Parts dynamically for each post, saving time and enhancing creativity.
 * Requires at least: 6.6
 * Requires PHP:      7.4
 * Version:           0.0.1
 * Author:            Matt Watson <support@mattwatson.codes>
 * Author URI:        https://mattwatson.blog
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       dynamic-template-parts
 * Domain Path:       /languages
 *
 * @package           dynamic-template-parts
 */

namespace Dynamic_Template_Parts;

defined( 'ABSPATH' ) || exit;

/**
 * Define constants.
 */
const PLUGIN_VERSION      = '0.0.1';
const PLUGIN_PREFIX       = 'dynamic_template_parts_';
const PLUGIN_SLUG         = 'dynamic-template-parts';
const PLUGIN_ROOT_DIR     = __DIR__;
const PLUGIN_ROOT_FILE    = __FILE__;

/**
 * Load the namespaces.
 */
require_once PLUGIN_ROOT_DIR . '/includes/assets.php';
require_once PLUGIN_ROOT_DIR . '/includes/helpers.php';
require_once PLUGIN_ROOT_DIR . '/includes/meta.php';
require_once PLUGIN_ROOT_DIR . '/includes/namespace.php';
require_once PLUGIN_ROOT_DIR . '/includes/switcher.php';

/**
 * Run.
 */
bootstrap();