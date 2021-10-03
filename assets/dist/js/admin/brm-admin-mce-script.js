'use strict';

/**
 * Inserter shortcode mce button script.
 *
 * Scripts to run on the backend-end.
 */

(function ($) {
	tinymce.PluginManager.add('brm_restaurant_menu', function (editor, url) {

		/**
   * onChange show group title option.
   */
		$(document).on('change', 'input[name="show_group_title"]', function (e) {
			if ($(this).is(':checked')) {
				$(this).val('yes');
			} else {
				$(this).val('no');
			}
		});

		/**
   * onChange show group description option.
   */
		$(document).on('change', 'input[name="show_group_desc"]', function (e) {
			if ($(this).is(':checked')) {
				$(this).val('yes');
			} else {
				$(this).val('no');
			}
		});

		/**
   * onChange show items option.
   */
		$(document).on('change', 'input[name="show_items"]', function (e) {
			if ($(this).is(':checked')) {
				$(this).val('1');
			} else {
				$(this).val('0');
			}
		});

		/*
   * Insert shortcode.
   */
		$(document).on('click', '.insert-shortcode', function (e) {
			e.preventDefault();

			var showGroupTitle = $('.show-group-title').val();
			var showGroupDesc = $('.show-group-desc').val();
			var showItems = $('.show-items').val();
			var viewMode = $('select#view-mode option:selected').val();

			var params = {
				attr: {
					'groups': '',
					'show_group_title': showGroupTitle,
					'show_group_desc': showGroupDesc,
					'show_items': showItems,
					'view': viewMode
				},
				name: 'brm_restaurant_menu'
			};

			// Initialize groups array.
			var groupsArray = [];

			$('select#groups-included option:selected').each(function () {
				var group = $(this).val();

				// Push selected group id to groups array. 
				groupsArray.push(group);
			});

			if (groupsArray.length > 0) {
				var groups = groupsArray.join(',');

				// Assign selected groups to shortcode params
				params.attr.groups = groups;
			}

			var shortcode = wp.shortcode.string({
				tag: params.name,
				attrs: params.attr,
				type: 'single'
			});

			editor.insertContent(shortcode);

			$('#popup-overlay').remove();
		});

		/*
   * Cancel shortcode builder form.
   */
		$(document).on('click', '.cancel-shortcode', function (e) {
			e.preventDefault();

			$('#popup-overlay').remove();
		});

		editor.addButton('brm_add_menu', {
			text: 'Menu',
			title: 'Best Restaurant Menu Shortcode',
			image: '',
			icon: false,
			onclick: function onclick() {
				// Open the popup.
				openPopupOverlay();

				var data = {
					action: 'brm_shortcode_builder_form',
					nonce: brm_params.nonce
				};

				$.ajax({
					url: brm_params.ajax_url,
					data: data,
					type: 'POST',
					success: function success(response) {
						if (response && response.data && response.data.status && response.data.status == '200') {
							var formContent = response.data.shortcode_form;

							// Append popup content to the overlay div.
							$('#popup-overlay .popup-overlay-content').html('');
							$('#popup-overlay .popup-overlay-content').append('<div class="popup-close cancel-shortcode"><svg viewbox="0 0 40 40"><path class="close-x" d="M 10,10 L 30,30 M 30,10 L 10,30" /></svg></div>');
							$('#popup-overlay .popup-overlay-content').append(formContent);

							$('form.brm-shortcode-builder #groups-included').select2();

							// Close popup.
							$('.popup-close').on('click', function (e) {
								$('#popup-overlay').remove();
							});
						}
					},
					error: function error(message) {
						console.log(message);
					}
				});
			}
		});
	});

	/*
  * Append Lightbox Popup.
  */
	function openPopupOverlay() {
		if ($('#popup-overlay').length <= 0) {
			$('body').append('<div id="popup-overlay"><div class="popup-overlay-content"><div class="popup-close cancel-shortcode"><svg viewbox="0 0 40 40"><path class="close-x" d="M 10,10 L 30,30 M 30,10 L 10,30" /></svg></div></div></div>');
		}
	}
})(jQuery);