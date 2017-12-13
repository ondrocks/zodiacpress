/**
 * Block editor script for the birth report form
 */

/**
 * @todo @test 
 */
function getZPForm() {

	var formMarkup = '';

	jQuery.ajax({
		type: 'POST',
		url: ajaxurl,
		data: {
			action: "zp_get_block_form",
		},		
		dataType: 'json',
		async: false,
		success: function (response) {

			// if neither null, blank, nor false 
			if ($.trim(response.form) && 'false' != $.trim(response.form)) {
				formMarkup = response.form;
			}

		}
	});


	return formMarkup;
}

( function( blocks, i18n, element ) {
	var el = element.createElement;// @todo need...
	var __ = i18n.__;

	const iconEl = el('svg', { width: 20, height: 20 },
	  el('path', { d: "M8.744 15.011l-1.182 4.686c0.796 0.201 1.615 0.302 2.434 0.302 0.706 0 1.416-0.076 2.109-0.226l-1.021-4.724c-0.774 0.167-1.577 0.154-2.341-0.039zM12.746 19.613v0zM13.465 13.832c-0.587 0.532-1.275 0.914-2.046 1.134l-0.013 0.004 1.34 4.644-0.013 0.004 0.015-0.004c1.489-0.425 2.822-1.165 3.963-2.199l-3.245-3.581zM6.404 13.709l-3.36 3.474c1.095 1.058 2.44 1.865 3.89 2.334l1.485-4.599c-0.752-0.243-1.449-0.661-2.015-1.209zM14.917 11.583c-0.243 0.752-0.661 1.448-1.21 2.015l3.473 3.361c1.059-1.094 1.866-2.439 2.335-3.889l-4.599-1.487zM5.031 11.411l-1.169 0.339-3.477 0.993 0.003 0.009c0.426 1.488 1.166 2.821 2.201 3.962l3.58-3.247c-0.533-0.588-0.914-1.276-1.135-2.047l-0.002-0.009zM19.775 7.898l-4.724 1.019c0.077 0.358 0.116 0.723 0.116 1.087 0 0.422-0.052 0.844-0.156 1.254l4.686 1.183c0.201-0.797 0.303-1.617 0.303-2.438 0-0.705-0.076-1.414-0.225-2.106zM0.302 7.565c-0.2 0.795-0.302 1.613-0.302 2.431 0 0.708 0.076 1.418 0.227 2.113l4.724-1.023c-0.078-0.359-0.117-0.726-0.117-1.090 0-0.421 0.052-0.841 0.155-1.251l-4.687-1.18zM17.407 3.282l-3.578 3.249c0.534 0.588 0.916 1.277 1.137 2.049l0.003 0.009 4.647-1.327-0.003-0.010c-0.427-1.492-1.169-2.828-2.206-3.97zM2.815 3.046c-1.058 1.095-1.865 2.44-2.332 3.89l4.6 1.484c0.242-0.752 0.66-1.449 1.208-2.016l-3.475-3.359zM13.060 0.481l-1.482 4.6c0.752 0.242 1.449 0.66 2.016 1.208l3.357-3.477c-1.095-1.058-2.441-1.864-3.891-2.331zM7.253 0.387c-1.491 0.426-2.826 1.168-3.968 2.204l3.248 3.579c0.588-0.533 1.277-0.916 2.048-1.136l0.020-0.006-0.688-2.341-0.66-2.3zM10.004 0c-0.709 0-1.421 0.077-2.116 0.227l1.025 4.723c0.773-0.168 1.577-0.155 2.341 0.038l1.179-4.687c-0.794-0.2-1.611-0.301-2.428-0.301z" } )
	);


	blocks.registerBlockType( 'zodiacpress/birthreport', {
		title: __( 'Birth Report Form', 'zodiacpress' ),
		icon: iconEl,
		category: 'common',
		keywords: [ __( 'natal' ), __( 'zodiacpress' ) ],


		supportHTML: false,// @test

		/****************************************************
		* @todo default attributes for sidereal and house system
		****************************************************/


		// attributes: {
		// 	content: {
		// 		source: 'text',
		// 		selector: 'h2',
		// 	},
		// },



		edit: function() {

			var zpBlockContent = getZPForm();// @todo 

			// var zpDisplay = blocks.parse( zpBlockContent );// @todo @test
			

    	    return zpBlockContent;// @todo 

	    },

		save() {
			return null;
		},
		
	} );
} )(
	window.wp.blocks,
	window.wp.i18n,
	window.wp.element
);
