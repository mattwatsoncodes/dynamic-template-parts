import classNames from 'classnames';
import { BlockPreview, InspectorControls } from '@wordpress/block-editor';
import { createBlock } from '@wordpress/blocks';
import { Disabled, PanelBody, ToggleControl } from '@wordpress/components';
import { useEntityRecords } from '@wordpress/core-data';
import { useMemo } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Generates a label for each CheckboxControl based on the template part's properties.
 *
 * @param {Object} part      The template part data.
 * @param {string} currentId The ID of the current template part.
 * @return {string} The formatted label.
 */
const getCheckboxControlLabel = ( part, currentId ) => {
	const title = part.title.raw;
	const areaLabels = {
		heading: __( 'Heading', 'dynamic-template-parts' ),
		footer: __( 'Footer', 'dynamic-template-parts' ),
	};
	const area = areaLabels[ part.area ] || __( 'Generic', 'dynamic-template-parts' );

	const areaString = `(${__( 'Area:', 'dynamic-template-parts' )} ${area})`;
	const thisString = part.id === currentId ? ` (${__( 'This Template Part', 'dynamic-template-parts' )})` : '';

	return `${title} ${areaString}${thisString}`;
};

/**
 * Updates the list of limited switchable parts based on checkbox selection.
 *
 * @param {boolean}  checked                The new checked state of the checkbox.
 * @param {string}   partId                 The ID of the template part.
 * @param {Array}    limitedSwitchableParts The current list of switchable parts.
 * @param {Function} setAttributes          Callback to update block attributes.
 */
const handleCheckboxChange = ( checked, partId, limitedSwitchableParts, setAttributes ) => {
	const updatedParts = checked
		? [ ...limitedSwitchableParts, partId ]
		: limitedSwitchableParts?.filter( ( id ) => id !== partId );

	setAttributes( { limitedSwitchableParts: updatedParts } );
};

export default function inspectorControls( BlockEdit ) {
	return ( props ) => {
		const {
			attributes: { area, isSwitchable, limitSwitchableParts, limitedSwitchableParts = [], slug, theme, useArea },
			isSelected,
			name,
			setAttributes,
		} = props;

		// Apply Inspector Controls only to the selected 'core/template-part' block.
		if ( name !== 'core/template-part' || ! isSelected ) {
			return <BlockEdit { ...props } />;
		}

		const id = `${theme}//${slug}`;

		// Fetch template parts using useEntityRecords and filter by area if needed.
		const { records: templateParts = [], hasResolved } = useEntityRecords( 'postType', 'wp_template_part' );
		const filteredTemplateParts = useMemo( () => {
			const parts = useArea ? templateParts : templateParts?.filter( ( record ) => record.area === area );
			return parts?.sort( ( a, b ) => a.title.raw.localeCompare( b.title.raw ) );
		}, [ templateParts, area, useArea ] );

		// Memoize BlockPreview components for each template part.
		const memoizedBlockPreviews = useMemo( () => {
			const previews = {};
			filteredTemplateParts?.forEach( ( part ) => {
				const { theme: partTheme, slug: partSlug } = part;
				const templatePartBlock = createBlock( 'core/template-part', { slug: partSlug, theme: partTheme } );
				previews[ part.id ] = (
					<BlockPreview blocks={ [ templatePartBlock ] } viewportWidth={ 300 } />
				);
			} );
			return previews;
		}, [ filteredTemplateParts ] );

		// Display the default BlockEdit if data hasn't resolved.
		if ( ! hasResolved ) {
			return <BlockEdit { ...props } />;
		}

		return (
			<>
				<BlockEdit { ...props } />
				<InspectorControls>
					<PanelBody title={ __( 'Dynamic Template Part', 'dynamic-template-parts' ) }>
						<ToggleControl
							checked={ isSwitchable }
							help={ __( 'Allow this template part to be replaced by others.', 'dynamic-template-parts' ) }
							label={ __( 'Enable', 'dynamic-template-parts' ) }
							onChange={ ( isSwitchable ) => setAttributes( { isSwitchable } ) }
						/>
						{ isSwitchable && (
							<>
								<ToggleControl
									checked={ useArea }
									help={ __( 'Enable to allow replacement with any type.', 'dynamic-template-parts' ) }
									label={ __( 'Allow Replacement with Any Template Part', 'dynamic-template-parts' ) }
									onChange={ ( useArea ) => setAttributes( { useArea } ) }
								/>
								<ToggleControl
									checked={ limitSwitchableParts }
									label={ __( 'Replace with Selected Template Parts', 'dynamic-template-parts' ) }
									help={ __( 'Choose template parts that can replace this one.', 'dynamic-template-parts' ) }
									onChange={ ( limitSwitchableParts ) => setAttributes( { limitSwitchableParts, limitedSwitchableParts: [] } ) }
								/>
								{ limitSwitchableParts && (
									<div className="components-checkbox-control-list">
										{ filteredTemplateParts.map( ( part ) => {
											const blockPreview = memoizedBlockPreviews[ part.id ];
											const isDisabled = part.id === id;
											const isSelected = limitedSwitchableParts.includes( part.id ) || isDisabled;

											const classes = classNames( 'dynamic-template-parts-preview', {
												'dynamic-template-parts-preview--selected': isSelected,
												'dynamic-template-parts-preview--disabled': isDisabled,
											} );

											return (
												<Disabled isDisabled={ isDisabled } key={ part.id }>
													<label className={ classes } htmlFor={ part.id }>
														<input
															type="checkbox"
															checked={ isSelected }
															className="dynamic-template-parts-preview__input"
															id={ part.id }
															name={ part.id }
															onChange={ ( event ) =>
																handleCheckboxChange( event.target.checked, part.id, limitedSwitchableParts, setAttributes ) }
														/>
														<div className="dynamic-template-parts-preview__preview">
															<span className="dynamic-template-parts-preview__title">{ getCheckboxControlLabel( part, id ) }</span>
															{ blockPreview }
														</div>
													</label>
												</Disabled>
											);
										} ) }
									</div>
								) }
							</>
						) }
					</PanelBody>
				</InspectorControls>
			</>
		);
	};
}
