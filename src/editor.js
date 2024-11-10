/**
 * Editor JS.
 *
 * Scripts applied in the WordPress Editor.
 */

// Import styles
import './editor.scss';

// WordPress dependencies
import { addFilter } from '@wordpress/hooks';
import { registerPlugin } from '@wordpress/plugins';

// Component imports
import inspectorControls from './components/inspectorControls';
import settings from './components/settings';
import { icon, RenderDynamicTemplateParts as render } from './components/sidebar';

const isSiteEditor = 'site-editor-php' === window.adminpage;

if ( isSiteEditor ) {
	// Register block attributes and inspector controls for site editor.
	addFilter( 'blocks.registerBlockType', 'dynamic-template-parts/dynamic-template-parts-attributes', settings );
	addFilter( 'editor.BlockEdit', 'dynamic-template-parts/dynamic-template-parts-controls', inspectorControls );
} else {
	// Register plugin sidebar for the post editor.
	registerPlugin( 'dynamic-template-parts', { icon, render } );
}
