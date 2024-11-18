import classNames from 'classnames';
import { BlockPreview } from '@wordpress/block-editor';
import { createBlock } from '@wordpress/blocks';
import { BaseControl, Panel, PanelBody } from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
import { PluginSidebar, PluginSidebarMoreMenuItem } from '@wordpress/editor';
import { useMemo } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { layout } from '@wordpress/icons';

export const icon = layout;
const title = __( 'Dynamic Template Parts', 'dynamic-template-parts' );

/**
 * Helper function to determine if an option is selected.
 *
 * @param {string} key                   The key of the template part.
 * @param {string} optionId              The ID of the current option.
 * @param {Object} switchedTemplateParts The switched template parts data.
 * @param {string} defaultOptionId       The ID of the default option.
 * @param {Object} templateParts         The template parts data.
 * @return {boolean} Whether the option is selected.
 */
const isOptionSelected = ( key, optionId, switchedTemplateParts, defaultOptionId, templateParts ) => {
	const isSwitchedSelected = switchedTemplateParts?.[ key ]?.id === optionId;

	// If the key does not exist in templateParts and it's the default option, select it.
	const isDefaultWhenKeyMissing =
		! templateParts?.[ key ] && optionId === defaultOptionId;

	// Default selection when no switched parts for the key.
	const isDefaultSelected =
		! switchedTemplateParts?.[ key ] && optionId === defaultOptionId;

	return isSwitchedSelected || isDefaultWhenKeyMissing || isDefaultSelected;
};

/**
 * Helper function to get updated switched parts on option change.
 */
const getUpdatedSwitchedParts = ( selected, key, partOptions, switchedTemplateParts ) => {
	const updatedParts = { ...switchedTemplateParts };
	const selectedOption = partOptions.find( ( option ) => option.id === selected );

	// Update or remove from switched parts based on selection.
	if ( selectedOption.id === key ) {
		delete updatedParts[ key ];
	} else {
		updatedParts[ key ] = { ...selectedOption };
	}
	return updatedParts;
};

/**
 * Renders Dynamic Template Parts Singular Sidebar.
 *
 * @return {JSX.Element} The sidebar component.
 */
export const RenderDynamicTemplateParts = () => {
	// Retrieve localized key and options from the global object.
	// eslint-disable-next-line no-undef
	const { DynamicTemplatePartsKey, DynamicTemplatePartsOptions } = dynamicTemplateParts;

	// Use editPost to update post meta and useSelect to get post meta data.
	const { editPost } = useDispatch( 'core/editor' );
	const meta = useSelect( ( select ) => select( 'core/editor' ).getEditedPostAttribute( 'meta' ) );
	const switchedTemplateParts = meta?.[ DynamicTemplatePartsKey ] || [];

	// Generate block previews and organize template parts with memoization.
	const { blockPreviews, templateParts } = useMemo( () => {
		const previews = {};
		const parts = Object.entries( DynamicTemplatePartsOptions ).reduce( ( acc, [ key, part ] ) => {
			const options = part.options.map( ( option ) => {
				const [ theme, slug ] = option.id.split( '//' );
				const templatePartBlock = createBlock( 'core/template-part', { slug, theme } );
				previews[ option.id ] = (
					<BlockPreview blocks={ [ templatePartBlock ] } viewportWidth={ 300 } />
				);
				return option;
			} );
			acc[ key ] = { ...part, options };
			return acc;
		}, {} );

		return { blockPreviews: previews, templateParts: parts };
	}, [ DynamicTemplatePartsOptions ] );

	// Exit early if there are no template parts.
	if ( ! templateParts || ! Object.keys( templateParts ).length ) {
		return null;
	}

	// Exit early if there are no valid template parts.
	const hasValidTemplateParts = Object.values( templateParts ).some(
		( part ) => part.options.length > 1
	);

	if ( ! hasValidTemplateParts ) {
		return null;
	}

	return (
		<>
			{ /* Sidebar Menu Item */ }
			<PluginSidebarMoreMenuItem target="dynamic-template-parts">
				{ title }
			</PluginSidebarMoreMenuItem>

			{ /* Main Plugin Sidebar */ }
			<PluginSidebar name="dynamic-template-parts" title={ title }>
				{ Object.entries( templateParts ).map( ( [ key, part ] ) => {
					// Only render if we can select more than one part.
					if ( part.options.length < 2 ) {
						return null;
					}

					return (
						<Panel key={ key }>
							<PanelBody title={ part.title }>
								<BaseControl
									// translators: %s: Template Part Title.
									help={ __( "Select a template part to display instead of '%s'.", 'dynamic-template-parts' ).replace( '%s', part.title ) }
								/>
								{ part.options.map( ( option ) => {
									const isSelected = isOptionSelected(
										key,
										option.id,
										switchedTemplateParts,
										part.options.find( ( opt ) => opt.default )?.id
									);

									const classes = classNames( 'dynamic-template-parts-preview', {
										'dynamic-template-parts-preview--selected': isSelected,
									} );

									return (
										<label className={ classes } htmlFor={ `${key}//${option.id}` } key={ `${key}//${option.id}` }>
											<input
												checked={ isSelected }
												className="dynamic-template-parts-preview__input"
												id={ `${key}//${option.id}` }
												name={ key }
												type="radio"
												value={ option.id }
												onChange={ ( event ) => {
													const updatedParts = getUpdatedSwitchedParts(
														event.target.value,
														key,
														part.options,
														switchedTemplateParts
													);
													editPost( {
														meta: {
															[ DynamicTemplatePartsKey ]: updatedParts,
														},
													} );
												} }
											/>
											<div className="dynamic-template-parts-preview__preview">
												<span className="dynamic-template-parts-preview__title">{ option.title }</span>
												{ blockPreviews[ option.id ] }
											</div>
										</label>
									);
								} ) }
							</PanelBody>
						</Panel>
					);
				} ) }
			</PluginSidebar>
		</>
	);
};
