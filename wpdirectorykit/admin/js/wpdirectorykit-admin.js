(function ($) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	jQuery(document).ready(function ($) {
		$('.event-ajax-indicator').on('click submit', function () {
			if ($(this).find('.wdk-ajax-indicator').length) {
				$(this).find('.wdk-ajax-indicator').removeClass('hidden')
					.removeClass('wdk-hidden');
				$(this).find('.hidden-onloading').addClass('wdk-hidden');
			}
			else {
				$(this).parent().find('.wdk-ajax-indicator').removeClass('hidden');
			}
		})


		$('.wdk-submit-loading').on('click', function () {
			var form;
			form = $(this).closest('form');

			if ($(this).hasClass('wdk_btn_load_indicator')) {
				console.log('disabled')
				return false;
			}

			$(this).addClass('wdk_btn_load_indicator disabled');
		});

		$('.wdk-click-loading').on('click', function (e) {
			$(this).addClass('wdk_btn_load_indicator').attr('disabled', 'disabled');
		});

		/* date time fields init */
		if ($('.wdk-fielddate').length && typeof $.datepicker != 'undefined') {

			$('.wdk-fielddate').each(function () {
				let dateFormat = script_parameters.format_date_js;
				var self = $(this);

				if (self.attr('date-format'))
					dateFormat = self.attr('date-format');

				self.datepicker({
					dateFormat: dateFormat, onSelect: function () {
						self.parent().find('.db-date').val(wdk_date_sql_normalize(self.val(), self)).trigger('input');
					}
				}).on("change", function () {
					if (self.val() == '') {
						self.parent().find('.db-date').val('');
					} else {
						self.parent().find('.db-date').val(wdk_date_sql_normalize(self.val(), self));
					}
				});

				if (self.parent().find('.db-date').val() == '' && false) {
					self.parent().find('.db-date').val(wdk_date_sql_normalize());
				}
			})
		};

		if ($('.wdk-fielddatetime').length && typeof $.datepicker != 'undefined') {

			$('.wdk-fielddatetime').each(function () {
				let dateFormat = script_parameters.format_datetime_js;

				var self = $(this);
				if (self.attr('date-format'))
					dateFormat = self.attr('date-format');

				self.datepicker({
					dateFormat: dateFormat, onSelect: function () {


						var datetime = wdk_date_notime_sql_normalize(self.val(), self);
						if (self.parent().find('[name="hours_mask"]').val() != '') {
							datetime += ' ' + wdk_pad(self.parent().find('[name="hours_mask"]').val());
						} else {
							datetime += ' 00';
						}
						if (self.parent().find('[name="minutes_mask"]').val() != '') {
							datetime += ':' + wdk_pad(self.parent().find('[name="minutes_mask"]').val());
						} else {
							datetime += ':00';
						}
						datetime += ':00';

						self.parent().find('.db-date').val(datetime).trigger('input');
					}
				}).on("change", function () {

					var datetime = wdk_date_notime_sql_normalize(self.val(), self);
					if (self.parent().find('[name="hours_mask"]').val() != '') {
						datetime += ' ' + wdk_pad(self.parent().find('[name="hours_mask"]').val());
					} else {
						datetime += ' 00';
					}
					if (self.parent().find('[name="minutes_mask"]').val() != '') {
						datetime += ':' + wdk_pad(self.parent().find('[name="minutes_mask"]').val());
					} else {
						datetime += ':00';
					}
					datetime += ':00';
					self.parent().find('.db-date').val(datetime).trigger('input');

				});

				if (self.parent().find('.db-date').val() == '') {
					self.parent().find('.db-date').val(wdk_date_sql_normalize());
				}

				self.parent().find('[name="hours_mask"],[name="minutes_mask"]').on('input', function () {
					var datetime = wdk_date_notime_sql_normalize(self.val(), self);
					if (self.parent().find('[name="hours_mask"]').val() != '') {
						datetime += ' ' + wdk_pad(self.parent().find('[name="hours_mask"]').val());
					} else {
						datetime += ' 00';
					}
					if (self.parent().find('[name="minutes_mask"]').val() != '') {
						datetime += ':' + wdk_pad(self.parent().find('[name="minutes_mask"]').val());
					} else {
						datetime += ':00';
					}
					datetime += ':00';
					self.parent().find('.db-date').val(datetime);
				});
			})
		};

		if ($('.wdk-fielddate_from').length && $('.wdk-fielddate_to').length && typeof $.datepicker != 'undefined') {
			var dateFormat, from, to;
			const getDate = (element) => {
				var date;
				try {
					date = $.datepicker.parseDate(dateFormat, element.value);
				} catch (error) {
					date = null;
				}
				return date;
			}

			dateFormat = script_parameters.format_date_js;
			if ($('.wdk-fielddate_from').attr('date-format'))
				dateFormat = $('.wdk-fielddate_from').attr('date-format');

			from = $('.wdk-fielddate_from')
				.datepicker({
					dateFormat: dateFormat,
					onSelect: function (selectedDate) {
						to.datepicker("option", "minDate", selectedDate);
						setTimeout(function () {
							to.datepicker('show');
						}, 16);

						from.parent().find('.db-date').val(wdk_date_sql_normalize(from.val(), from)).trigger('input');
						wdk_date_add_hours(from);

					}
				}).on("change", function () {

				});

			to = $('.wdk-fielddate_to').datepicker({
				dateFormat: dateFormat
			})
				.on("change", function () {
					from.datepicker("option", "maxDate", getDate(this));

					to.parent().find('.db-date').val(wdk_date_sql_normalize(to.val(), to)).trigger('input');
					wdk_date_add_hours(to);
				});

			if (to.parent().find('.db-date').val() == '') {
				to.parent().find('.db-date').val(wdk_date_sql_normalize());
			}

			if (from.parent().find('.db-date').val() == '') {
				from.parent().find('.db-date').val(wdk_date_sql_normalize());
			}

			from.parent().find('[name="hours_mask"],[name="minutes_mask"]').on('input', function () {
				wdk_date_add_hours(from);
			});

			to.parent().find('[name="hours_mask"],[name="minutes_mask"]').on('input', function () {
				wdk_date_add_hours(to);
			});

		}

		jQuery(document).ready(function ($) {
			$("ul#adminmenu a[href*='wordpress.org/support/plugin/wpdirectorykit']").attr('target', '_blank');
			$("ul#adminmenu a[href*='wpdirectorykit.com/contact.html']").attr('target', '_blank');
			$("ul#adminmenu a[href*='wpdirectorykit.com/documentation']").attr('target', '_blank');
		});


		/* wdk pro feature ask */
		if (typeof $.fn.confirm == 'function') {
			$('.wdk-pro, .wdk-pro a, .wdk-pro button, .wdk-pro input').off().on('click', function (e) {
				e.preventDefault();
				var self = jQuery(this)
				jQuery.confirm({
					boxWidth: '400px',
					useBootstrap: false,
					title: self.data('title'),
					content: self.data('content'),
					buttons: {
						cancel: function () {
							return true;
						},
						somethingElse: {
							text: self.data('button-succuss'),
							btnClass: 'btn-blue activate-now',
							keys: ['enter', 'shift'],
							action: function () {
								window.location = self.data('action');
								return false;
							}
						}
					}
				});
				return false;
			});
			$('select.wdk-pro-select').off().on('input', function (e) {

				var self = jQuery(this);
				var selected = self.find('option:selected');

				if (selected.hasClass('pro-value')) {
					jQuery.confirm({
						boxWidth: '400px',
						useBootstrap: false,
						title: self.data('title'),
						content: self.data('content'),
						buttons: {
							cancel: function () {
								return true;
							},
							somethingElse: {
								text: self.data('button-success'),
								btnClass: 'btn-blue activate-now',
								keys: ['enter', 'shift'],
								action: function () {
									window.location = self.data('action');
									return false;
								}
							}
						}
					});
					self.val("").change();
				}
			});
			$('.wdk-pro,.wdk-pro a, .wdk-pro button, .wdk-pro input').on('focus', function () {
				$(this).trigger('blur');
				return false;
			});
		}

		if (jQuery('.wdk-tabs-navs').length) {
			jQuery('.wdk-tabs-navs').find('label').on('click', function () {
				jQuery(this).parent().find('label').removeClass('active');
				jQuery(this).addClass('active');
			});
		}
	})
})(jQuery);

const wdk_pad = (num = '') => {
	return ('00' + num).slice(-2);
};

const wdk_date_add_hours = (selector = '') => {
	if (typeof selector != 'undefined' && selector.parent().find('[name="hours_mask"]').length) {
		var datetime = wdk_date_notime_sql_normalize(selector.val(), selector);
		if (selector.parent().find('[name="hours_mask"]').val() != '') {
			datetime += ' ' + wdk_pad(selector.parent().find('[name="hours_mask"]').val());
		} else {
			datetime += ' 00';
		}
		if (selector.parent().find('[name="minutes_mask"]').val() != '') {
			datetime += ':' + wdk_pad(selector.parent().find('[name="minutes_mask"]').val());
		} else {
			datetime += ':00';
		}
		datetime += ':00';
		selector.parent().find('.db-date').val(datetime);
	}
};

/*
	Convert date to sql format

	@param string $date, string of date

	@return string normalize date or Current date/time
*/
const wdk_date_sql_normalize = (date = '', datepicker_el = null) => {
	if (date == '') {
		var d = new Date();
	} else {
		var d = new Date(date);
	}

	if (d == 'Invalid Date' && datepicker_el) {
		var d = new Date(+(jQuery.datepicker.formatDate("@", datepicker_el.datepicker("getDate"))));
	}

	d = d.getUTCFullYear() + '-' +
		wdk_pad(d.getMonth() + 1) + '-' +
		wdk_pad(d.getDate()) + ' ' +
		wdk_pad(d.getHours()) + ':' +
		wdk_pad(d.getMinutes()) + ':' +
		wdk_pad(d.getSeconds());
	return d;
};

const wdk_date_notime_sql_normalize = (date = '', datepicker_el = null) => {
	if (date == '') {
		var d = new Date();
	} else {
		var d = new Date(date);
	}

	if (d == 'Invalid Date' && datepicker_el) {
		var d = new Date(+(jQuery.datepicker.formatDate("@", datepicker_el.datepicker("getDate"))));
	}

	d = d.getUTCFullYear() + '-' +
		wdk_pad(d.getMonth() + 1) + '-' +
		wdk_pad(d.getDate());
	return d;
};



document.addEventListener('DOMContentLoaded', () => {

	class WDKListingSearch {
		constructor(inputSelector, resultsSelector, executedId = null) {
			this.executedId = executedId;
			this.input = document.querySelector(inputSelector);
			this.results = document.querySelector(resultsSelector);
			this.timer = null;
			this.controller = null;
			this.init();
		}

		init() {
			if (!this.input || !this.results) return;

			this.input.addEventListener('input', () => this.onInput());
			document.addEventListener('click', (e) => this.onDocumentClick(e));
			this.input.addEventListener('focus', () => this.onFocus());
			this.input.addEventListener('keydown', (e) => this.onKeyDown(e));
		}

		onInput() {
			clearTimeout(this.timer);

			const keyword = this.input.value.trim();

			if (this.controller) {
				this.controller.abort();
			}

			if (keyword.length < 2) {
				this.results.replaceChildren();
				this.results.hidden = true;
				return;
			}

			this.timer = setTimeout(() => this.fetchResults(keyword), 250);
		}

		async fetchResults(keyword) {
			this.controller = new AbortController();

			// Show dropdown + loading
			this.results.hidden = false;
			this.results.innerHTML = `
				<div class="wdk-search-loading">
					<span class="spinner is-active"></span>
					Searching...
				</div>
			`;

			try {
				const response = await fetch(
					wdk_script_parameters.wpApiSettings.root + 'wdk/v1/listing/autosuggestion',
					{
						method: 'POST',
						credentials: 'same-origin',
						headers: {
							'Content-Type': 'application/json',
							'X-WP-Nonce': wdk_script_parameters.wpApiSettings.nonce
						},
						body: JSON.stringify({
							keyword: keyword,
							executedId: this.executedId
						}),
						signal: this.controller.signal
					}
				);

				const json = await response.json();

				this.results.replaceChildren();

				if (!json.success || !json.data.length) {
					this.results.innerHTML = `
						<div class="wdk-search-empty">
							No results found
						</div>
					`;
					return;
				}

				json.data.forEach(item => {
					const a = document.createElement('a');
					a.href = item.edit ? decodeURIComponent(item.edit) : '#';
					a.className = 'wdk-search-item';
					a.textContent = item.title;

					this.results.appendChild(a);
				});

			} catch (e) {
				if (e.name === 'AbortError') {
					return;
				}
				this.results.innerHTML = `
					<div class="wdk-search-empty">
						${e.message ? 'Error: ' + e.message : 'An unexpected error occurred'}
					</div>
				`;
				this.results.innerHTML = `
					<div class="wdk-search-empty">
						Search failed
					</div>
				`;
			}
		}

		onDocumentClick(e) {
			if (!e.target.closest('.wdk-toolbar-search')) {
				this.results.hidden = true;
			}
		}

		onFocus() {
			if (this.results.children.length) {
				this.results.hidden = false;
			}
		}

		onKeyDown(e) {
			if (e.key === 'Escape') {
				this.results.hidden = true;
			}
		}
	}

	// Initialize on DOMContentLoaded
	new WDKListingSearch('#wdk-listing-search', '.wdk-search-results', document.getElementById('wdk-listing-search').getAttribute('idexecuted'));
});



