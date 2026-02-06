(function () {

	var $ = jQuery;

	var pinterestToken = null;

	elementor.channels.editor.on('change', function (view) {
		var changed = view.elementSettingsModel.changed;

		if (changed.access_token) {
			if (changed.access_token.startsWith('pina_'))
				pinterestToken = changed.access_token;
		}
	});

	function onSectionActivate(sectionName) {

		if ('access_credentials_section' === sectionName) {

			setTimeout(function () {

				var accessToken = jQuery('.elementor-control-access_token textarea').val();

				pinterestToken = accessToken;

			}, 100);
		}
	}

	elementor.channels.editor.on('section:activated', onSectionActivate);

	var selectOptions = elementor.modules.controls.Select2.extend({

		isUpdated: false,

		onReady: function () {

			var options = (0 === this.model.get('options').length);

			if (this.container && "widget" === this.container.type && 'board_id' === this.model.get('name')) {
				if (options) {

					var _this = this;

					if (pinterestToken) {

						jQuery.ajax({
							type: "GET",
							url: PremiumSettings.ajaxurl,
							dataType: "JSON",
							data: {
								action: "get_pinterest_boards",
								nonce: PremiumSettings.nonce,
								token: pinterestToken
							},
							success: function (res) {

								if (res.data) {

									var options = JSON.parse(res.data);

									_this.model.set("options", options);

									_this.isUpdated = false;

									_this.render();

								}
							},
							error: function (err) {
								console.log(err);
							}
						});
					}

					elementor.channels.editor.on('change', function (view) {
						var changed = view.elementSettingsModel.changed;

						if (undefined !== changed.board_id && !_this.isUpdated) {
							_this.isUpdated = true;
						}
					});

				}
			}
		},

		onBeforeRender: function () {

			if (this.container && ("section" === this.container.type || "container" === this.container.type)) {
				var widgetObj = elementor.widgetsCache || elementor.config.widgets,
					optionsToUpdate = {};

				var _this = this;
				this.container.children.forEach(function (child) {

					if ("container" === _this.container.type) {

						if (child.view.$childViewContainer) {
							getInnerWidgets(child);
						} else {
							//Get Flex Container widgets when no columns are added.
							var name = child.view.$el.data("widget_type").split('.')[0];

							if ('undefined' !== typeof widgetObj[name]) {
								optionsToUpdate[".elementor-widget-" + widgetObj[name].widget_type + " .elementor-widget-container"] = widgetObj[name].title;
							}
						}

					} else if ("section" === _this.container.type) {
						getInnerWidgets(child);
					}

				});

				function getInnerWidgets(child) {
					child.view.$childViewContainer.children("[data-widget_type]").each(function (index, widget) {
						var name = $(widget).data("widget_type").split('.')[0];

						if ('undefined' !== typeof widgetObj[name]) {
							optionsToUpdate[".elementor-widget-" + widgetObj[name].widget_type + " .elementor-widget-container"] = widgetObj[name].title;
						}
					});

				}

				this.model.set("options", optionsToUpdate);
			}
		},
	});

	elementor.addControlView("wdk-select", selectOptions);

	var filterOptions = elementor.modules.controls.Select2.extend({

		isUpdated: false,

		onReady: function () {
			var self = this,
				type = self.model.get('source') || self.options.elementSettingsModel.attributes.post_type_filter;

			// if ('post' !== type) {
			var options = (0 === this.model.get('options').length);

			if (options) {
				self.fetchData(type);
			}
			// }

			elementor.channels.editor.on('change', function (view) {
				var changed = view.elementSettingsModel.changed;

				if (undefined !== changed.post_type_filter && 'post' !== changed.post_type_filter && !self.isUpdated) {
					self.isUpdated = true;
					self.fetchData(changed.post_type_filter);
				}
			});
		},

		fetchData: function (type) {

			var self = this;
			$.ajax({
				url: PremiumSettings.ajaxurl,
				dataType: 'json',
				type: 'POST',
				data: {
					nonce: PremiumSettings.nonce,
					action: 'wdk_update_filter',
					post_type: 'object' === typeof type ? type : [type]
				},
				success: function (res) {

					self.updateFilterOptions(JSON.parse(res.data));
					self.isUpdated = false;

					self.render();
				},
				error: function (err) {
					console.log(err);
				},
			});
		},

		updateFilterOptions: function (options) {
			this.model.set("options", options);
		},

		onBeforeDestroy: function () {
			if (this.ui.select.data('select2')) {
				// this.ui.select.select2('destroy');
			}

			this.$el.remove();
		}
	});

	elementor.addControlView("wdk-post-filter", filterOptions);


	jQuery(window).on('elementor:init', function() {
		elementor.hooks.addAction('panel/open_editor/widget', function(panel, model, view) {
			jQuery('.el-wdk-selector-field').each(function() {
				const $select = jQuery(this);
				const isMultiple = $select.prop('multiple');
				let val = $select.val();
				
				if (isMultiple) {
					const selectedOptions = $select.find('option[selected]');
					if ((!val || !val.length) && selectedOptions.length) {
						val = selectedOptions.map(function() { return jQuery(this).val(); }).get();
						$select.val(val).trigger('change');
					}
				} else {
					if (!val) {
						const selectedOption = $select.find('option[selected]');
						if (selectedOption.length) {
							val = selectedOption.first().val();
							$select.val(val).trigger('change');
						}
					}
				}
			});
		}); 
	});


	jQuery(window).on('elementor:init', () => {
		elementor.addControlView('wdk-fields-selector', elementor.modules.controls.BaseData.extend({
	
			onReady() {
				const $select = this.$el.find('.el-wdk-selector-field');
	
				if (!$select.hasClass('select2-hidden-accessible')) {
					$select.select2({
						width: '100%',
						placeholder: $select.data('placeholder') || '',
						allowClear: true
					});
				}
	
				let val = this.getControlValue();
	
				if (!val) {
					const selectedOption = $select.find('option[selected]');
					if (selectedOption.length) {
						val = selectedOption.val();
						this.setValue(val);
						$select.val(val).trigger('change');
					}
				} else {
					$select.val(val).trigger('change');
				}
	
				$select.on('change', (e) => {
					this.setValue($select.val());
				});
			},
	
			onBeforeDestroy() { 
				const $select = this.$el.find('.el-wdk-selector-field');
				if ($select.data('select2')) {
					$select.select2('destroy');
				}
			},
		}));
	});
	
})(jQuery);
