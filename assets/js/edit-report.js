/**
 * ZodiacPress Custom Reports: Edit Report Interface JS functions
 *
 * @package ZodiacPress
 */
var zpEditReport;

(function($) {

	var api;

	api = zpEditReport = {

		options : {
			sortableItems:   '> *'
		},

		reportList : undefined,	// Set in init.
		targetList : undefined, // Set in init.
		lastSearch: '',

		// Functions that run on init.
		init : function() {
			api.reportList = $('#report-to-edit');
			api.targetList = api.reportList;

			this.jQueryExtensions();
			this.attachReportEditListeners();
			this.attachQuickSearchListeners();
			this.attachTabsPanelListeners();

			if ( api.reportList.length )
				this.initSortables();

			this.initAccessibility();
			this.initPreviewing();
		},

		jQueryExtensions : function() {
			// jQuery extensions
			$.fn.extend({

				/**
				 * Adds selected report items to the report.
				 *
				 * @param jQuery metabox The metabox jQuery object.
				 */
				addSelectedToReport : function(processMethod) {
					if ( 0 === $('#report-to-edit').length ) {
						return false;
					}

					// get all ids of added report items
					var addedItems = document.querySelectorAll('#report-to-edit li[id]'),
						addedIds = [];
					Array.prototype.forEach.call( addedItems, function( el ) {
						addedIds.push( el.id.replace('report-item-', '') );
					} );

					return this.each(function() {
						var t = $(this), reportItems = {},
							checkboxes = t.find( '.tabs-panel-active .categorychecklist li input:checked' ),
							re = /report-item\[([^\]]*)/;

						processMethod = processMethod || api.addReportItemToBottom;
						// If no items are checked, bail.
						if ( !checkboxes.length )
							return false;
						// Show the ajax spinner
						t.find( '.button-controls .spinner' ).addClass( 'is-active' );
						// Retrieve report item data
						$(checkboxes).each(function(){
							var t = $(this);
							// if item is already added to report, don't add it again
							if ( -1 == addedIds.indexOf(this.value) ) {
								reportItems[this.value] = t.closest('li').getItemData( 'add-report-item', this.value );
							}
						});

						if ( ! Object.keys(reportItems).length ) {
							// Nothing to add so stop spinner and get out
							t.find( '.button-controls .spinner' ).removeClass( 'is-active' );
							return false;
						}

						// Add the items
						api.addItemToReport(reportItems, processMethod, function(){
							// Deselect the items and hide the ajax spinner
							checkboxes.removeAttr('checked');
							t.find( '.button-controls .spinner' ).removeClass( 'is-active' );
						});
					});
				},
				getItemData : function( itemType, id ) {
					itemType = itemType || 'report-item';

					var itemData = {}, i,
					fields = [
						'report-item-id',
						'report-item-title'
					];
					
					if( !id && itemType == 'report-item' ) {
						id = this.find('.report-item-data-object-id').val();
					}

					if( !id ) return itemData;

					this.find('input').each(function() {
						var field;
						i = fields.length;
						while ( i-- ) {
							if( itemType == 'report-item' )
								field = fields[i] + '[' + id + ']';
							else if( itemType == 'add-report-item' )
								field = 'report-item[' + id + '][' + fields[i] + ']';

							if (
								this.name &&
								field == this.name
							) {
								itemData[fields[i]] = this.value;
							}
						}
					});

					return itemData;
				},
				setItemData : function( itemData ) {
					var itemType = 'report-item',
						id = $('.report-item-data-object-id', this).val();

					if( !id ) return this;
					this.find('input').each(function() {
						var t = $(this), field;
						$.each( itemData, function( attr, val ) {
							field = attr + '[' + id + ']';

							if ( field == t.attr('name') ) {
								t.val( val );
							}
						});
					});
					return this;
				}
			});
		},
		moveReportItem : function( $this, dir ) {
			var newItemPosition,
				reportItems = $( '#report-to-edit li' ),
				reportItemsCount = reportItems.length,
				thisItem = $this.parents( 'li.report-item' ),
				thisItemPosition = parseInt( thisItem.index(), 10 );

			switch ( dir ) {
			case 'up':
				newItemPosition = thisItemPosition - 1;

				// Already at top
				if ( 0 === thisItemPosition )
					break;

				thisItem.detach().insertBefore( reportItems.eq( newItemPosition ) );

				break;
			case 'down':
				// Have we reached the bottom
				if ( reportItemsCount === thisItemPosition + 1 )
					break;

				thisItem.detach().insertAfter( reportItems.eq( thisItemPosition + 1 ) );

				break;
			case 'top':
				// Already at top
				if ( 0 === thisItemPosition )
					break;

				thisItem.detach().insertBefore( reportItems.eq( 0 ) );

				break;
			}
			$this.focus();
			api.refreshKeyboardAccessibility();
			api.refreshAdvancedAccessibility();
		},
		initAccessibility : function() {
			api.refreshKeyboardAccessibility();
			api.refreshAdvancedAccessibility();

			// Refresh the accessibility when the user comes close to the item in any way
			api.reportList.on( 'mouseenter.refreshAccessibility focus.refreshAccessibility touchstart.refreshAccessibility' , '.report-item' , function(){
				api.refreshAdvancedAccessibilityOfItem( $( this ).find( 'a.item-edit' ) );
			} );

			// We have to update on click as well because we might hover first, change the item, and then click.
			api.reportList.on( 'click', 'a.item-edit', function() {
				api.refreshAdvancedAccessibilityOfItem( $( this ) );
			} );

			// Links for moving items
			api.reportList.on( 'click', '.reports-move', function () {
				var $this = $( this ),
					dir = $this.data( 'dir' );

				if ( 'undefined' !== typeof dir ) {
					api.moveReportItem( $( this ).parents( 'li.report-item' ).find( 'a.item-edit' ), dir );
				}
			});
		},
		/**
		 * refreshAdvancedAccessibilityOfItem( [itemToRefresh] )
		 *
		 * Refreshes advanced accessibility buttons for one report item.
		 * Shows or hides buttons based on the location of the report item.
		 *
		 * @param  {object} itemToRefresh The report item that might need its advanced accessibility buttons refreshed
		 */
		refreshAdvancedAccessibilityOfItem : function( itemToRefresh ) {
			// Only refresh accessibility when necessary
			if ( true !== $( itemToRefresh ).data( 'needs_accessibility_refresh' ) ) {
				return;
			}
			var thisLink, title,
				$this = $( itemToRefresh ),
				reportItem = $this.closest( 'li.report-item' ).first(),
				itemName = $this.closest( '.report-item-handle' ).find( '.report-item-title' ).text(),
				position = parseInt( reportItem.index(), 10 ),
				totalReportItems = $('#report-to-edit li').length,
				hasSibling = reportItem.nextAll().length;

			reportItem.find( '.field-move' ).toggle( totalReportItems > 1 );

			// Where can they move this report item?
			if ( 0 !== position ) {
				thisLink = reportItem.find( '.reports-move-up' );
				thisLink.attr( 'aria-label', reports.moveUp ).css( 'display', 'inline' );
			}

			if ( 0 !== position ) {
				thisLink = reportItem.find( '.reports-move-top' );
				thisLink.attr( 'aria-label', reports.moveToTop ).css( 'display', 'inline' );
			}

			if ( position + 1 !== totalReportItems && 0 !== position ) {
				thisLink = reportItem.find( '.reports-move-down' );
				thisLink.attr( 'aria-label', reports.moveDown ).css( 'display', 'inline' );
			}

			if ( 0 === position && 0 !== hasSibling ) {
				thisLink = reportItem.find( '.reports-move-down' );
				thisLink.attr( 'aria-label', reports.moveDown ).css( 'display', 'inline' );
			}

			// String together help text for report items
			title = reports.reportFocus.replace( '%1$s', itemName ).replace( '%2$d', position + 1 ).replace( '%3$d', totalReportItems );
			$this.attr( 'aria-label', title );
			// Mark this item's accessibility as refreshed
			$this.data( 'needs_accessibility_refresh', false );
		},
		/**
		 * refreshAdvancedAccessibility
		 *
		 * Hides all advanced accessibility buttons and marks them for refreshing.
		 */
		refreshAdvancedAccessibility : function() {
			// Hide all the move buttons by default.
			$( '.report-item-settings .field-move .reports-move' ).hide();

			// Mark all report items as unprocessed
			$( 'a.item-edit' ).data( 'needs_accessibility_refresh', true );

			// All open items have to be refreshed or they will show no links
			$( '.report-item-edit-active a.item-edit' ).each( function() {
				api.refreshAdvancedAccessibilityOfItem( this );
			} );
		},
		refreshKeyboardAccessibility : function() {
			$( 'a.item-edit' ).off( 'focus' ).on( 'focus', function(){
				$(this).off( 'keydown' ).on( 'keydown', function(e){
					var arrows,
						$this = $( this ),
						thisItem = $this.parents( 'li.report-item' ),
						thisItemID = thisItem.attr('id').replace('edit-report-item-title-', '');

					// Bail if it's not an up/down arrow key
					if ( 38 != e.which && 40 != e.which )
						return;

					// Avoid multiple keydown events
					$this.off('keydown');

					// Bail if there is only one report item
					if ( 1 === $('#report-to-edit li').length )
						return;

					arrows = { '38': 'up', '40': 'down' };

					switch ( arrows[e.which] ) {
					case 'up':
						api.moveReportItem( $this, 'up' );
						break;
					case 'down':
						api.moveReportItem( $this, 'down' );
						break;
					}

					// Put focus back on same report item
					$( '#edit-' + thisItemID ).focus();
					return false;
				});
			});
		},
		initPreviewing : function() {
			// Update the item handle title when the label is changed.
			$( '#report-to-edit' ).on( 'change input', '.edit-report-item-title', function(e) {
					var input = $( e.currentTarget ), title, titleEl;
					title = input.val();
					titleEl = input.closest( '.report-item' ).find( '.report-item-title' );
					// Don't update to empty title.
					if ( title ) {
						titleEl.text( title );
					}
			} );
		},
		initSortables : function() {
			var prev, next, nextThreshold, helperHeight, transport;
			
			if( 0 !== $( '#report-to-edit li' ).length )
				$( '.drag-instructions' ).show();
			api.reportList.sortable({
				handle: '.report-item-handle',
				placeholder: 'sortable-placeholder',
				items: api.options.sortableItems,
				start: function(e, ui) {
					var height, width, tempHolder;
					// Update the height of the placeholder to match the moving item.
					height = ui.helper.outerHeight();
					helperHeight = height;
					height -= 2; // Subtract 2 for borders
					ui.placeholder.height(height);

					// Update the width of the placeholder to match the moving item.
					width = ui.helper.find('.report-item-handle').outerWidth(); // Get original width
					width -= 2; // Subtract 2 for borders
					ui.placeholder.width(width);

					// Update the list of report items.
					tempHolder = ui.placeholder.next( '.report-item' );
					tempHolder.css( 'margin-top', helperHeight + 'px' ); // Set the margin to absorb the placeholder
					ui.placeholder.detach(); // detach or jQuery UI will think the placeholder is a report item
					$(this).sortable( 'refresh' ); // The children aren't sortable. We should let jQ UI know.
					ui.item.after( ui.placeholder ); // reattach the placeholder.
					tempHolder.css('margin-top', 0); // reset the margin

					// Now that the element is complete, we can update...
					updateSharedVars(ui);
				},
				stop: function(e, ui) {
					// address sortable's incorrectly-calculated top in opera
					ui.item[0].style.top = 0;

					api.refreshKeyboardAccessibility();
					api.refreshAdvancedAccessibility();
				},
				change: function(e, ui) {
					// Make sure the placeholder is inside the element.
					// Otherwise fix it, or we're in trouble.
					if( ! ui.placeholder.parent().hasClass('report') )
						(prev.length) ? prev.after( ui.placeholder ) : api.reportList.prepend( ui.placeholder );

					updateSharedVars(ui);
				},
				sort: function(e, ui) {
					var offset = ui.helper.offset();
					// If we overlap the next element, manually shift downwards
					if( nextThreshold && offset.top + helperHeight > nextThreshold ) {
						next.after( ui.placeholder );
						updateSharedVars( ui );
						$( this ).sortable( 'refreshPositions' );
					}
				}
			});

			function updateSharedVars(ui) {
				next = ui.placeholder.next( '.report-item' );
				// Make sure we don't select the moving item.
				if( next[0] == ui.item[0] ) next = next.next( '.report-item' );
				nextThreshold = (next.length) ? next.offset().top + next.height() / 3 : 0;
			}
		},
		attachReportEditListeners : function() {
			var that = this;
			$('#update-zp-report').bind('click', function(e) {
				if ( e.target && e.target.className ) {
					if ( -1 != e.target.className.indexOf('item-edit') ) {
						return that.eventOnClickEditLink(e.target);
					} else if ( -1 != e.target.className.indexOf('report-save') ) {
						return that.eventOnClickReportSave(e.target);
					} else if ( -1 != e.target.className.indexOf('item-delete') ) {
						return that.eventOnClickReportItemDelete(e.target);
					} else if ( -1 != e.target.className.indexOf('item-cancel') ) {
						return that.eventOnClickCancelLink(e.target);
					}
				}
			});

			$('.zpcustomdiv input[type="text"]').keypress(function(e){
				var box = $(this.closest('.zpcustomdiv'));
				box.removeClass('form-invalid');
				if ( e.keyCode === 13 ) {
					e.preventDefault();
					var addButton = box.find('.submit-add-to-report');
					addButton.click();
				}
			});
		},
		attachQuickSearchListeners : function() {
			var searchTimer;

			// Prevent form submission.
			$( '#zp-report-meta' ).on( 'submit', function( event ) {
				event.preventDefault();
			});

			$( '#zp-report-meta' ).on( 'input', '.quick-search', function() {
				var $this = $( this );
				if ( searchTimer ) {
					clearTimeout( searchTimer );
				}

				searchTimer = setTimeout( function() {
					api.updateQuickSearchResults( $this );
 				}, 500 );
			}).on( 'blur', '.quick-search', function() {
				api.lastSearch = '';
			});
		},
		updateQuickSearchResults : function(input) {
			var panel, params,
				minSearchLength = 2,
				q = input.val();

			/*
			 * Minimum characters for a search. Also avoid a new AJAX search when
			 * the pressed key (e.g. arrows) doesn't change the searched term.
			 */
			if ( q.length < minSearchLength || api.lastSearch == q ) {
				return;
			}

			api.lastSearch = q;
			panel = input.parents('.tabs-panel');
			params = {
				'action': 'zp-aspects-quick-search',
				'zp-edit-report-column-nonce': $('#zp-edit-report-column-nonce').val(),
				'q': q
			};

			$( '.spinner', panel ).addClass( 'is-active' );

			$.post( ajaxurl, params, function(reportMarkup) {
				api.processQuickSearchQueryResponse(reportMarkup, panel);
			});
		},
		addCustomItem : function( processMethod, targetID ) {
			var box = targetID.replace('submit-zpcustom', ''),
				textID = 'custom-' + box  + '-item',
				label = $('#' + textID ).val();
			processMethod = processMethod || api.addReportItemToBottom;
			if ( '' === label ) {
				$('#zpcustom' + box).addClass('form-invalid');
				return false;
			}
			// Show the ajax spinner
			$( '.zpcustomdiv .spinner' ).addClass( 'is-active' );
			this.addTextToReport( box, label, processMethod, function() {
				// Remove the ajax spinner
				$( '.zpcustomdiv .spinner' ).removeClass( 'is-active' );
				// Set custom form back to defaults
				$('#' + textID).val('').blur();
			});
		},
		addTextToReport : function( id, label, processMethod, callback) {
			processMethod = processMethod || api.addReportItemToBottom;
			callback = callback || function(){};

			// Make a unique id for this custom text/heading/subheading

			// get all ids of editable report items
			var ids = document.querySelectorAll('#report-to-edit li[id]'),
				uniqueIds = [],
				ending = '_' + id;

			Array.prototype.forEach.call( ids, function( el ) {
				var item = el.id.replace('report-item-', '');
				
				// collect ids only for this type of custom text item (heading/subheading/text)
				if (item.endsWith(ending)) {
					uniqueIds.push( item.replace(ending, '') );
				}
			} );
			if ( uniqueIds.length > 0 ) {
			  	var number = Math.max(...uniqueIds);
		  		number++;// new unique id
		  	} else {
		  		number = 1;
		  	}
		  	id = number + '_' + id;

			api.addItemToReport({
				'-1': {
					'report-item-id': id,
					'report-item-title': label
				}
			}, processMethod, callback);
		},
		addItemToReport : function(reportItem, processMethod, callback) {
			var params = {
				'action': 'add-report-item',
				'zp-edit-report-column-nonce': $('#zp-edit-report-column-nonce').val(),
				'report-item': reportItem
			}; 				

			processMethod = processMethod || function(){};
			callback = callback || function(){};

			$.post( ajaxurl, params, function(reportMarkup) {
				var ins = $('#report-instructions');
				reportMarkup = $.trim( reportMarkup );
				processMethod(reportMarkup, params);

				// Make it stand out a bit more visually, by adding a fadeIn
				$( 'li.pending' ).hide().fadeIn('slow');
				$( '.drag-instructions' ).show();
				if( ! ins.hasClass( 'report-instructions-inactive' ) && ins.siblings().length )
					ins.addClass( 'report-instructions-inactive' );

				callback();
				$( 'li.pending' ).removeClass('pending');
			});
		},
		/**
		 * Process the add report item request response into report list item. Appends to report.
		 *
		 * @param {string} reportMarkup The text server response of report item markup.
		 */
		addReportItemToBottom : function( reportMarkup ) {
			var $reportMarkup = $( reportMarkup );
			$reportMarkup.appendTo( api.targetList );
			api.refreshKeyboardAccessibility();
			api.refreshAdvancedAccessibility();
		},
		attachTabsPanelListeners : function() {
			$('#report-settings-column').bind('click', function(e) {
				var panelId, wrapper,
					target = $(e.target);

				if ( target.hasClass('aspects-tab-link') ) {

					panelId = target.data( 'type' );
					wrapper = target.parents('.accordion-section-content').first();

					// upon changing tabs, we want to uncheck all checkboxes
					$('input', wrapper).removeAttr('checked');

					$('.tabs-panel-active', wrapper).removeClass('tabs-panel-active').addClass('tabs-panel-inactive');
					$('#' + panelId, wrapper).removeClass('tabs-panel-inactive').addClass('tabs-panel-active');

					$('.tabs', wrapper).removeClass('tabs');
					target.parent().addClass('tabs');

					// select the search bar
					$('.quick-search', wrapper).focus();

					// Hide controls in the search tab if no items found.
					if ( ! wrapper.find( '.tabs-panel-active .report-item-title' ).length ) {
						wrapper.addClass( 'has-no-report-item' );
					} else {
						wrapper.removeClass( 'has-no-report-item' );
					}

					e.preventDefault();

				} else if ( target.hasClass('submit-add-to-report') ) {
					if ( e.target.id && -1 != e.target.id.indexOf('submit-zpcustom') )
						api.addCustomItem( api.addReportItemToBottom, e.target.id );
					else if ( e.target.id && -1 != e.target.id.indexOf('submit-') )
						$('#' + e.target.id.replace(/submit-/, '')).addSelectedToReport( api.addReportItemToBottom );
					return false;
				}
			});
		},
		eventOnClickEditLink : function(clickedEl) {
			var settings, item,
			matchedSection = /#(.*)$/.exec(clickedEl.href);
			if ( matchedSection && matchedSection[1] ) {
				settings = $('#'+matchedSection[1]);
				item = settings.parent();
				if( 0 !== item.length ) {
					if( item.hasClass('report-item-edit-inactive') ) {
						if( ! settings.data('report-item-data') ) {
							settings.data( 'report-item-data', settings.getItemData() );
						}
						settings.slideDown('fast');
						item.removeClass('report-item-edit-inactive')
							.addClass('report-item-edit-active');
					} else {
						settings.slideUp('fast');
						item.removeClass('report-item-edit-active')
							.addClass('report-item-edit-inactive');
					}
					return false;
				}
			}
		},
		eventOnClickCancelLink : function(clickedEl) {
			var settings = $( clickedEl ).closest( '.report-item-settings' ),
				thisReportItem = $( clickedEl ).closest( '.report-item' );
			thisReportItem.removeClass('report-item-edit-active').addClass('report-item-edit-inactive');
			settings.setItemData( settings.data('report-item-data') ).hide();
			return false;
		},
		eventOnClickReportSave : function() {
			var reportName = document.getElementById('report-name'),
			reportNameVal = reportName.value.trim();
			// Cancel and warn if invalid report name
			if( !reportNameVal || reportNameVal.length < 2 ) {
				reportName.parentNode.classList.add('form-invalid');
				return false;
			}
			window.onbeforeunload = null;
		
			// update the report
			var reportData = JSON.stringify( $( '#update-zp-report' ).serializeArray() ),
				params;

			params = {
				'action': 'zp_update_report',
				'report-data': reportData,
				'update-report-nonce': $('#update-zp-report-nonce').val()
			};
			
			$.post( ajaxurl, params, function(response) {
				if ( response.success ) {
					$( '#update-zp-report' ).submit();
				}
			});

			return false;
		},
		eventOnClickReportItemDelete : function(clickedEl) {
			var itemID = clickedEl.id.replace('delete-', '');
			api.removeReportItem( $('#report-item-' + itemID) );
			return false;
		},
		/**
		 * Process the quick search response into a search result
		 *
		 * @param string resp The server response to the query.
		 * @param jQuery panel The tabs panel we're searching in.
		 */
		processQuickSearchQueryResponse : function(resp, panel) {
			var $items = $('<div>').html(resp).find('li'),
				wrapper = panel.closest( '.accordion-section-content' );

			if( ! $items.length ) {
				$('.categorychecklist', panel).html( '<li><p>' + reports.noResultsFound + '</p></li>' );
				$( '.spinner', panel ).removeClass( 'is-active' );
				wrapper.addClass( 'has-no-report-item' );
				return;
			}

			$('.categorychecklist', panel).html( $items );
			$( '.spinner', panel ).removeClass( 'is-active' );
			wrapper.removeClass( 'has-no-report-item' );
		},
		/**
		 * Remove a report item.
		 * @param  {object} el The element to be removed as a jQuery object.
		 */
		removeReportItem : function(el) {
			el.addClass('deleting').animate({
				opacity : 0,
				height: 0
			}, 350, function() {
				var ins = $('#report-instructions');
				el.remove();
				if ( 0 === $( '#report-to-edit li' ).length ) {
					$( '.drag-instructions' ).hide();
					ins.removeClass( 'report-instructions-inactive' );
				}
				api.refreshAdvancedAccessibility();
			});
		},
	};

	$(document).ready(function(){ zpEditReport.init(); });

})(jQuery);
