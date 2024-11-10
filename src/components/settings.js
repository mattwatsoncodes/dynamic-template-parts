export default ( settings ) => {
	const { attributes, name } = settings;

	// Only apply changes to the 'core/template-part' block.
	if ( name !== 'core/template-part' ) {
		return settings;
	}

	// Define additional attributes for 'core/template-part'.
	const additionalAttributes = {
		isSwitchable: {
			type: 'boolean',
			default: false,
		},
		limitSwitchableParts: {
			type: 'boolean',
			default: false,
		},
		limitedSwitchableParts: {
			type: 'array',
			default: [],
		},
		useArea: {
			type: 'boolean',
			default: false,
		},
	};

	// Return updated settings with merged attributes.
	return {
		...settings,
		attributes: {
			...attributes,
			...additionalAttributes,
		},
	};
};
