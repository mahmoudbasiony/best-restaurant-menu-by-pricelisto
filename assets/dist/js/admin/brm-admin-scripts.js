'use strict';

/**
 * Back-end scripts.
 *
 * Scripts to run on the backend-end.
 */
(function ($) {

	/*
  * Nesting sortable groups & items
  */
	var sortingOptions = {
		listType: 'ul',
		handle: 'div',
		items: 'li',
		toleranceElement: '> div',
		forcePlaceholderSize: true,
		helper: 'clone',
		opacity: .6,
		placeholder: 'placeholder',
		revert: 250,
		tabSize: 25,
		tolerance: 'pointer',
		maxLevels: 3,
		isTree: true,
		protectRoot: false,
		expandOnHover: 700,
		startCollapsed: false,
		isAllowed: function isAllowed(placeholder, placeholderParent, currentItem) {
			return true;
		},
		start: function start(e, ui) {
			console.log('start');
		},
		receive: function receive(e, ui) {
			console.log('recieve');
		},
		stop: function stop(e, ui) {
			console.log('stop');
		},
		change: function change() {
			console.log('Changed item');
		},
		relocate: function relocate(e, ui) {
			console.log('Relocated item');
			orderNestingGroupsItems();
		}
	};

	$('.brm-groups-admin:first').nestedSortable(sortingOptions);

	$('.expandEditor').attr('title', 'Click to show/hide group description');
	$('.disclose').attr('title', 'Click to show/hide children');

	$(document).on('click', '.disclose', function () {
		$(this).closest('li').toggleClass('mjs-nestedSortable-collapsed').toggleClass('mjs-nestedSortable-expanded');
		$(this).toggleClass('ui-icon-plusthick').toggleClass('ui-icon-minusthick');
	});

	$(document).on('click', '.expandEditor', function () {
		var id = $(this).attr('data-id');
		$('#group-' + id + '-desc').toggle();
		$(this).toggleClass('ui-icon-triangle-1-n').toggleClass('ui-icon-triangle-1-s');
	});

	/*
  * Add new group.
  */
	$(document).on('click', '.add-new-group-btn', function (e) {
		e.preventDefault();

		openPopupOverlay();

		var formContent = brm_params.render_group_form;

		// Append popup content to the overlay div.
		$('#popup-overlay .popup-overlay-content').html('');
		$('#popup-overlay .popup-overlay-content').append('<div class="popup-close cancel-group"><svg viewbox="0 0 40 40"><path class="close-x" d="M 10,10 L 30,30 M 30,10 L 10,30" /></svg></div>');
		$('#popup-overlay .popup-overlay-content').append(formContent);

		// Close popup.
		$('.popup-close').on('click', function (e) {
			$('#popup-overlay').remove();
		});
	});

	/*
  * Append Lightbox Popup.
  */
	function openPopupOverlay() {
		if ($('#popup-overlay').length <= 0) {
			$('body').append('<div id="popup-overlay"><div class="popup-overlay-content"><div class="popup-close cancel-group"><svg viewbox="0 0 40 40"><path class="close-x" d="M 10,10 L 30,30 M 30,10 L 10,30" /></svg></div></div></div>');
		}
	}

	/*
  * Edit group
  */
	$(document).on('click', '.edit-group', function (e) {
		e.preventDefault();

		var groupId = $(this).closest('.group-raw').data('group-id');
		var order = $(this).closest('.group-raw').data('order');
		var parentID = $(this).closest('.group-raw').data('parent-id');
		var element = $(this);

		var data = {
			group_id: groupId,
			order: order,
			parent_id: parentID,
			action: 'brm_edit_group',
			nonce: brm_params.nonce
		};

		$.ajax({
			url: brm_params.ajax_url,
			data: data,
			type: 'POST',
			success: function success(response) {
				console.log(response);
				if (response && response.data) {

					openPopupOverlay();

					var formContent = response.data.form;

					// Append popup content to the overlay div.
					$('#popup-overlay .popup-overlay-content').html('');
					$('#popup-overlay .popup-overlay-content').append('<div class="popup-close cancel-group"><svg viewbox="0 0 40 40"><path class="close-x" d="M 10,10 L 30,30 M 30,10 L 10,30" /></svg></div>');
					$('#popup-overlay .popup-overlay-content').append(formContent);
					console.log(response.data.form);
				}
			},
			error: function error(message) {
				console.log(message);
			}
		});
	});

	/*
  * Save group
  */
	$(document).on('click', '.save-group', function (e) {
		e.preventDefault();

		var groupId = $(this).closest('form.brm-group-form').data('group-id');
		var groupName = $('.groups-name').val();
		var groupDesc = $('.groups-description').val();
		var parentID = $('.brm-group-raw').data('parent-id');
		var order = $('.brm-group-raw').data('order');

		$('.brm-errors').remove();

		// Validate group name.
		if (groupName === '' || groupName == null || groupName.length <= 0) {
			var error = 'This field is a required field!';
			$('.groups-name').closest('td').prepend('<p class="brm-errors">' + error + '</p>');
			return;
		}

		var data = {
			group_id: groupId,
			group_name: groupName,
			group_desc: groupDesc,
			parent_id: parentID,
			order: order,
			action: 'brm_save_group',
			nonce: brm_params.nonce
		};

		console.log(data);

		$.ajax({
			url: brm_params.ajax_url,
			data: data,
			type: 'POST',
			beforeSend: function beforeSend(e) {
				$('.sp-volume').show();
			},
			success: function success(response) {
				console.log(response);
				if (response && response.data && response.data.status && ('created' == response.data.status || 'updated' == response.data.status)) {
					var groupID = response.data.group_id;
					var groupRaw = response.data.group_raw;

					$('form.brm-group-form').attr('data-group-id', groupID);

					$('#popup-overlay').remove();

					if ($('.group-raw[data-group-id="' + groupID + '"]').length > 0) {
						$('.group-raw[data-group-id="' + groupID + '"]').replaceWith(groupRaw);
					} else {
						if (parentID > 0) {
							if ($('.group-raw[data-group-id="' + parentID + '"]').closest('li').children('ul.brm-groups-admin').length > 0) {
								$('.group-raw[data-group-id="' + parentID + '"]').closest('li').children('ul.brm-groups-admin').append('<li class="brm-group-li" data-group="' + groupId + '">' + groupRaw + '<li>');
							} else {
								$('.group-raw[data-group-id="' + parentID + '"]').closest('li').append('<ul class="brm-groups-admin"><li class="brm-group-li" data-group="' + groupId + '">' + groupRaw + '</li></ul>');
							}
						} else {
							if ($('.brm-menu').children('ul.brm-groups-admin').length > 0) $('.brm-menu').children('ul.brm-groups-admin').append('<li class="brm-group-li" data-group="' + groupId + '">' + groupRaw + '</li>');else $('.brm-menu').append('<ul class="brm-groups-admin"><li class="brm-group-li" data-group="' + groupId + '">' + groupRaw + '</li></ul>');
						}
					}
					orderNestingGroupsItems();
				}

				// Some validations.
				if (!response.success && response.data && 'error' == response.data.status) {
					$('.brm-errors').remove();
					$('.sp-volume').hide();

					var errorMessage = response.data.message;
					$('.groups-name').closest('td').prepend('<p class="brm-errors">' + errorMessage + '</p>');
				}
			},
			error: function error(message) {
				//console.log(message);
				$('.sp-volume').hide();
			}
		});
	});

	/*
  * Delete group.
  */
	$(document).on('click', '.delete-group', function (e) {

		e.preventDefault();

		if (confirm("Are you sure to delete this item ?")) {

			var groupId = $(this).closest('.group-raw').data('group-id');

			var data = {
				group_id: groupId,
				action: 'brm_delete_group',
				nonce: brm_params.nonce
			};

			$.ajax({
				url: brm_params.ajax_url,
				data: data,
				type: 'POST',
				beforeSend: function beforeSend(e) {
					$('.sp-volume').show();
				},
				success: function success(response) {
					console.log(response);
					if (response && response.data && response.data.status) {
						var groupID = response.data.group_id;

						console.log(groupID);
						$('.brm-group-li[data-group="' + groupID + '"]').remove();

						orderNestingGroupsItems();

						// Hide the spinner.
						$('.sp-volume').hide();
					}
				},
				error: function error(message) {
					console.log(message);
					$('.sp-volume').hide();
				}
			});
		}
	});

	/**
  * Cancel editing group.
  */
	$(document).on('click', '.cancel-group', function (e) {
		e.preventDefault();
		$('#popup-overlay').remove();
		$('.sp-volume').hide();
	});

	/**
  * Add sub group
  */
	$(document).on('click', '.add-new-subgroup', function (e) {

		e.preventDefault();

		var parentID = $(this).closest('#add-new-subgroup').data('group-id');

		console.log(parentID);

		console.log(brm_params.render_group_form);

		openPopupOverlay();

		var formContent = brm_params.render_group_form;

		// Append popup content to the overlay div.
		$('#popup-overlay .popup-overlay-content').html('');
		$('#popup-overlay .popup-overlay-content').append('<div class="popup-close cancel-group"><svg viewbox="0 0 40 40"><path class="close-x" d="M 10,10 L 30,30 M 30,10 L 10,30" /></svg></div>');

		// Render group form.
		$('#popup-overlay .popup-overlay-content').append(formContent);
		$('.brm-group-raw').attr('data-parent-id', parentID);

		// Close popup.
		$('.popup-close').on('click', function (e) {
			$('#popup-overlay').remove();
		});
	});

	// Reorder groups
	function orderNestingGroupsItems() {
		var counter = 1;
		var sorting = [];
		$('.group-raw').each(function () {

			$(this).data('order', counter);

			var groupId = $(this).data('group-id');
			var order = $(this).data('order');

			// Reset parent Ids.
			$('.brm-groups-admin:first').children('li').children('.group-raw').data('parent-id', 0);

			if ($(this).siblings('ul').length > 0) {
				// Add parent Ids.
				$(this).siblings('ul').find('div.group-raw').data('parent-id', groupId);
			}

			var parentId = $(this).data('parent-id');

			var obj = {};

			obj.group_id = groupId;
			obj.order = order;
			obj.parent_id = parentId;

			sorting.push(obj);
			counter++;
		});

		var counter = 1;

		$('.item-raw').each(function (e) {
			$(this).data('order', counter);

			var itemId = $(this).data('item-id');
			var order = $(this).data('order');
			var groupId = $(this).closest('li.brm-group-li').data('group');

			var obj = {};
			obj.item_id = itemId;
			obj.item_order = order;
			obj.group_linked = groupId;

			sorting.push(obj);

			counter++;
		});

		if (sorting.length > 0) {
			var sortingData = JSON.stringify(sorting);

			var data = {
				sorting_data: sortingData,
				action: 'brm_order_nesting_groups_items',
				nonce: brm_params.nonce
			};

			console.log(sortingData);
			$.ajax({
				url: brm_params.ajax_url,
				data: data,
				type: 'POST',
				success: function success(response) {
					console.log(response);

					if (response && response.data && response.data.menu) {
						// Initialize groupIds Array.
						var groupIds = [];

						// Push collapsed group Ids to groupIds Array.
						$('li.brm-group-li.mjs-nestedSortable-collapsed').each(function (e) {
							var groupId = $(this).data('group');

							var obj = {};
							obj.id = groupId;
							groupIds.push(obj);
						});

						// Replace menu by the updated menu.
						$('.brm-menu ul.brm-groups-admin:first').replaceWith(response.data.menu);

						// Recall the nesting serialize function.
						$('ul.brm-groups-admin:first').nestedSortable(sortingOptions);

						/**
       * Handles the previously collapsed groups.
       */
						for (var i = 0; i < groupIds.length; i++) {
							var id = groupIds[i].id;
							var element = $('li.brm-group-li[data-group="' + id + '"]');
							element.toggleClass('mjs-nestedSortable-collapsed').toggleClass('mjs-nestedSortable-expanded');
							element.find('span.disclose:first').toggleClass('ui-icon-plusthick').toggleClass('ui-icon-minusthick');
						}

						// Hide the spinner.
						$('.sp-volume').hide();
					}
				},
				error: function error(message) {
					console.log(message);

					// Hide the spinner.
					$('.sp-volume').hide();
				}
			});
		}
	}

	/*
  * Add new item.
  */
	$(document).on('click', '.add-new-item', function (e) {

		e.preventDefault();

		var groupId = $(this).closest('#add-new-item').data('group-id');

		console.log(groupId);

		openPopupOverlay();

		var formContent = brm_params.render_item_form;

		// Append popup content to the overlay div.
		$('#popup-overlay .popup-overlay-content').html('');
		$('#popup-overlay .popup-overlay-content').append('<div class="popup-close cancel-group"><svg viewbox="0 0 40 40"><path class="close-x" d="M 10,10 L 30,30 M 30,10 L 10,30" /></svg></div>');

		// Render item form.
		$('#popup-overlay .popup-overlay-content').append(formContent);

		$('.brm-item-raw').attr('data-group-id', groupId);

		// Close popup.
		$('.popup-close').on('click', function (e) {
			$('#popup-overlay').remove();
		});
	});

	/*
  * Save item.
  */
	$(document).on('click', '.save-item', function (e) {
		e.preventDefault();

		var form = $(this).closest('form.brm-item-form');
		var itemID = form.data('item-id');
		var itemName = form.find('.item-name').val();
		var itemDesc = form.find('.item-description').val();
		var imageID = form.find('#item-image').val();
		var price = form.find('.item-price').val();
		var groupId = form.find('.brm-item-raw').data('group-id');
		var order = form.find('.brm-item-raw').data('order');

		$('.brm-errors').remove();

		/**
   * Validations
   */
		var errorExist = false;

		// Validate item name.
		if (itemName === '' || itemName == null || itemName.length <= 0) {
			var error = 'This field is a required field!';
			form.find('.item-name').closest('td').prepend('<p class="brm-errors">' + error + '</p>');
			errorExist = true;
		}

		// Validate item price.
		if (price.length > 0 && !$.isNumeric(price)) {
			var error = 'This field only accepts a numeric value!';
			form.find('.item-price').closest('td').prepend('<p class="brm-errors">' + error + '</p>');
			errorExist = true;
		}

		if (errorExist) return;

		var data = {
			item_id: itemID,
			item_name: itemName,
			item_desc: itemDesc,
			image_id: imageID,
			price: price,
			order: order,
			group_id: groupId,
			action: 'brm_save_item',
			nonce: brm_params.nonce
		};
		console.log(data);

		$.ajax({
			type: 'POST',
			url: brm_params.ajax_url,
			data: data,
			beforeSend: function beforeSend(e) {
				$('.sp-volume').show();
			},
			success: function success(response) {
				console.log(response);

				if (response && response.data && response.data.status && ('created' === response.data.status || 'updated' === response.data.status)) {
					var itemId = response.data.item_id;
					var itemRaw = response.data.item_raw;

					$('form.brm-item-form').attr('data-item-id', itemId);

					$('#popup-overlay').remove();

					if ($('.item-raw[data-item-id="' + itemId + '"]').length > 0) {
						$('.item-raw[data-item-id="' + itemId + '"]').replaceWith(itemRaw);
					} else {
						if ($('.group-raw[data-group-id="' + groupId + '"]').closest('li').children('ul.brm-items-admin').length > 0) $('.group-raw[data-group-id="' + groupId + '"]').closest('li').children('ul.brm-items-admin').append('<li class="mjs-nestedSortable-no-nesting">' + itemRaw + '</li>');else $('.group-raw[data-group-id="' + groupId + '"]').after('<ul class="brm-items-admin"><li class="mjs-nestedSortable-no-nesting">' + itemRaw + '</li></ul>');
					}

					orderNestingGroupsItems();
				}

				// Some validations.
				if (!response.success && response.data && 'error' == response.data.status) {
					$('.brm-errors').remove();
					$('.sp-volume').hide();

					var fieldClass = response.data.class;
					var errorMessage = response.data.message;
					form.find("." + fieldClass).closest('td').prepend('<p class="brm-errors">' + errorMessage + '</p>');
				}
			},
			error: function error(message) {
				console.log(message);
			}
		});
	});

	/**
  * Edit item
  */
	$(document).on('click', '.edit-item', function (e) {
		e.preventDefault();

		var itemId = $(this).closest('.item-raw').data('item-id');
		var order = $(this).closest('.item-raw').data('order');
		var groupId = $(this).closest('.item-raw').data('group-id');

		var data = {
			item_id: itemId,
			order: order,
			group_id: groupId,
			action: 'brm_edit_item',
			nonce: brm_params.nonce
		};

		console.log(data);
		$.ajax({
			url: brm_params.ajax_url,
			data: data,
			type: 'POST',
			success: function success(response) {
				console.log(response);

				if (response && response.data) {
					openPopupOverlay();

					var formContent = response.data.form;

					// Append popup content to the overlay div.
					$('#popup-overlay .popup-overlay-content').html('');
					$('#popup-overlay .popup-overlay-content').append('<div class="popup-close cancel-group"><svg viewbox="0 0 40 40"><path class="close-x" d="M 10,10 L 30,30 M 30,10 L 10,30" /></svg></div>');
					$('#popup-overlay .popup-overlay-content').append(formContent);
					console.log(response.data.form);
				}
			},
			error: function error(message) {
				console.log(message);
			}
		});
	});

	/**
  * Cancel editing item.
  */
	$(document).on('click', '.cancel-item', function (e) {
		e.preventDefault();

		$('#popup-overlay').remove();
		$('.sp-volume').hide();
	});

	/*
  * Delete item
  */
	$(document).on('click', '.delete-item', function (e) {
		e.preventDefault();

		if (confirm("Are you sure to delete this item ?")) {

			var itemID = $(this).closest('.item-raw').data('item-id');

			var data = {
				item_id: itemID,
				action: 'brm_delete_item',
				nonce: brm_params.nonce
			};

			$.ajax({
				url: brm_params.ajax_url,
				data: data,
				type: 'POST',
				beforeSend: function beforeSend(e) {
					$('.sp-volume').show();
				},
				success: function success(response) {
					console.log(response);

					if (response && response.data && response.data.status) {
						var itemId = response.data.item_id;

						$('.item-raw[data-item-id="' + itemId + '"]').remove();

						orderNestingGroupsItems();
					}
				},
				error: function error(message) {
					console.log(message);
					$('.sp-volume').hide();
				}
			});
		}
	});

	// The "Upload" button
	$(document).on('click', '.upload_image_button', function (e) {
		e.preventDefault();

		var button = $(this),
		    custom_uploader = wp.media({
			title: 'Insert image',
			library: {
				type: 'image'
			},
			button: {
				text: 'Insert into item'
			},
			multiple: false
		}).on('select', function () {
			var attachment = custom_uploader.state().get('selection').first().toJSON();
			$(button).parent().removeClass('no-image');
			$(button).parent().prev().attr('src', attachment.url).show();
			$(button).prev().val(attachment.id);
			$(button).next().show();
		}).open();
	});

	// The "Remove" button (remove the value from input type='hidden')
	$(document).on('click', '.remove_image_button', function () {

		var answer = confirm('Are you sure?');
		if (answer == true) {
			var src = $(this).parent().prev().attr('data-src');
			$(this).parent().addClass('no-image');
			$(this).parent().prev().attr('src', src).hide();
			$(this).prev().prev().val('');
			$(this).hide();
		}
		return false;
	});

	// Enhance some settings dropdown with select2
	$('#business_country, #business_currency, #theme-template').select2();
})(jQuery);