(function ($) {
    'use strict';
    var nextId = 0;
    var defaults = {
        ajax_url: null,
        ajax_param: {},
        language_id: null,
        min_chars: 3,
        debounce: 250,
        timeout: 8000,
        cache_ttl: 0, // Opt in; cached public results can become stale after unpublishing.
        cache_size: 30,
        max_height: 360,
        z_index: 100000,
        callback_selected: $.noop,
        callback_search: $.noop,
        strings: {
            loading: 'Searching…',
            empty: 'No results found',
            error: 'Search failed. Try again.',
            results: '{count} suggestions available',
            label: 'Search suggestions'
        }
    };

    function Suggestion(input, options) {
        var self = this;
        self.$input = $(input);
        self.options = $.extend(true, {}, defaults, options);
        self.id = 'wdk-suggestion-' + (++nextId);
        self.ns = '.' + self.id;
        self.cache = new Map();
        self.items = [];
        self.active = -1;
        self.version = 0;
        self.open = false;
        self.composing = false;
        self.destroyed = false;
        self.original = {};
        ['role', 'autocomplete', 'aria-autocomplete', 'aria-controls', 'aria-expanded',
            'aria-activedescendant', 'aria-busy', 'aria-haspopup'].forEach(function (name) {
            self.original[name] = self.$input.attr(name);
        });
        self.$input.attr({
            role: 'combobox', autocomplete: 'off', 'aria-autocomplete': 'list',
            'aria-haspopup': 'listbox', 'aria-controls': self.id + '-list', 'aria-expanded': 'false'
        });
        // A body portal avoids clipping by form and widget overflow containers.
        self.$root = $('<div>', {class: 'wdk_suggestion'}).css('z-index', self.options.z_index).appendTo(document.body);
        self.$panel = $('<div>', {class: 'list_container'}).hide().appendTo(self.$root);
        self.$scroll = $('<div>', {class: 'list_scroll'}).appendTo(self.$panel);
        self.$list = $('<ul>', {
            class: 'list_items', id: self.id + '-list', role: 'listbox',
            'aria-label': self.options.strings.label
        }).appendTo(self.$scroll);
        self.$message = $('<div>', {class: 'wdk-suggestion-message', 'aria-hidden': 'true'}).appendTo(self.$scroll);
        self.$status = $('<div>', {class: 'wdk-suggestion-sr', role: 'status', 'aria-live': 'polite', 'aria-atomic': 'true'}).appendTo(self.$root);
        self.$input.on('input' + self.ns, function () {
            if (!self.composing && !self.selecting) self.schedule();
        }).on('compositionstart' + self.ns, function () {
            self.composing = true;
            self.cancel();
            self.hide();
        }).on('compositionend' + self.ns, function () {
            self.composing = false;
            self.schedule();
        }).on('focus' + self.ns, function () {
            if (!self.selecting) self.schedule();
        }).on('keydown' + self.ns, function (event) {
            self.keydown(event);
        }).on('blur' + self.ns, function () {
            self.cancel();
            self.hide();
        });
        self.$panel.on('mousedown' + self.ns, '[role="option"]', function (event) {
            event.preventDefault(); // Keep keyboard focus on the input.
        }).on('click' + self.ns, '[role="option"]', function () {
            self.select(Number($(this).attr('data-index')));
        });
        $(document).on('pointerdown' + self.ns, function (event) {
            if (event.target !== input && !self.$root[0].contains(event.target)) {
                self.cancel();
                self.hide();
            }
        });
        self.onViewport = function (event) {
            if (event && self.$root[0].contains(event.target)) return;
            if (!self.open || self.frame) return;
            self.frame = window.requestAnimationFrame(function () {
                self.frame = null;
                if (self.open) self.position();
            });
        };
        window.addEventListener('resize', self.onViewport);
        document.addEventListener('scroll', self.onViewport, true);
        if (window.visualViewport) {
            window.visualViewport.addEventListener('resize', self.onViewport);
            window.visualViewport.addEventListener('scroll', self.onViewport);
        }
    }

    Suggestion.prototype.query = function () { return String(this.$input.val() || '').trim(); };
    Suggestion.prototype.cancel = function () {
        this.version++;
        clearTimeout(this.timer);
        var xhr = this.xhr;
        this.xhr = null;
        if (xhr) xhr.abort();
        this.$input.attr('aria-busy', 'false');
    };
    Suggestion.prototype.hide = function () {
        this.open = false;
        this.active = -1;
        this.$panel.hide().removeClass('win_visible');
        this.$root.removeClass('win_open');
        this.$input.attr('aria-expanded', 'false').removeAttr('aria-activedescendant');
        this.$status.text('');
    };
    Suggestion.prototype.position = function () {
        var rect = this.$input[0].getBoundingClientRect();
        var viewport = window.visualViewport;
        var top = viewport ? viewport.offsetTop : 0;
        var left = viewport ? viewport.offsetLeft : 0;
        var width = viewport ? viewport.width : document.documentElement.clientWidth;
        var height = viewport ? viewport.height : window.innerHeight;
        var gap = 6;
        if (rect.bottom < top || rect.top > top + height || !rect.width) {
            this.hide();
            return;
        }
        var below = Math.max(0, top + height - rect.bottom - gap - 8);
        var above = Math.max(0, rect.top - top - gap - 8);
        var up = below < Math.min(220, this.options.max_height) && above > below;
        var available = Math.min(this.options.max_height, up ? above : below);
        this.$scroll.css('max-height', available + 'px');
        this.$root.css({
            width: Math.min(rect.width, Math.max(0, width - 16)) + 'px',
            left: Math.max(left + 8, Math.min(rect.left, left + width - rect.width - 8)) + 'px',
            top: (up ? rect.top - gap : rect.bottom + gap) + 'px',
            direction: this.$input.css('direction'),
            'font-family': this.$input.css('font-family')
        }).toggleClass('suggestion_above', up);
    };
    Suggestion.prototype.show = function () {
        if (this.destroyed || document.activeElement !== this.$input[0]) return;
        this.open = true;
        this.$panel.show().addClass('win_visible');
        this.$root.addClass('win_open');
        this.$input.attr('aria-expanded', 'true');
        this.position();
    };
    Suggestion.prototype.message = function (type) {
        this.items = [];
        this.active = -1;
        this.$input.removeAttr('aria-activedescendant');
        this.$list.empty();
        this.$message.attr('class', 'wdk-suggestion-message is-' + type).text(this.options.strings[type]).show();
        this.$status.text(this.options.strings[type]);
        this.show();
    };
    Suggestion.prototype.schedule = function () {
        var self = this;
        self.cancel();
        self.hide();
        var query = self.query();
        if (Array.from(query).length < self.options.min_chars || Array.from(query).length > 120) return;
        self.timer = setTimeout(function () { self.request(query); }, self.options.debounce);
    };
    Suggestion.prototype.request = function (query) {
        var self = this;
        if (self.destroyed || document.activeElement !== self.$input[0] || self.query() !== query) return;
        var params = typeof self.options.ajax_param === 'function'
            ? self.options.ajax_param.call(self.$input[0]) : self.options.ajax_param;
        var data = $.extend({}, params, {search: query, language_id: self.options.language_id});
        var key = JSON.stringify(data);
        var cached = self.cache.get(key);
        if (cached && Date.now() - cached.time < self.options.cache_ttl) {
            self.cache.delete(key);
            self.cache.set(key, cached);
            self.render(cached.items, query);
            return;
        }
        self.cache.delete(key);
        if (!self.options.ajax_url) { self.message('error'); return; }
        var version = self.version;
        self.message('loading');
        self.$input.attr('aria-busy', 'true');
        self.options.callback_search.call(self.$input[0], query);
        self.xhr = $.ajax({
            url: self.options.ajax_url, type: 'POST', dataType: 'json', data: data,
            timeout: self.options.timeout
        }).done(function (response) {
            if (version !== self.version || self.destroyed) return;
            // Accept both the existing WDK output and wp_send_json_success().
            var payload = response && response.data && Array.isArray(response.data.results) ? response.data : response;
            if (!response || response.success === false || !payload || !Array.isArray(payload.results)) {
                self.message('error');
                return;
            }
            if (self.options.cache_ttl > 0 && self.options.cache_size > 0) {
                self.cache.set(key, {time: Date.now(), items: payload.results});
                while (self.cache.size > self.options.cache_size) self.cache.delete(self.cache.keys().next().value);
            }
            self.render(payload.results, query);
        }).fail(function (_xhr, status) {
            if (version === self.version && !self.destroyed && status !== 'abort') self.message('error');
        }).always(function () {
            if (version === self.version && !self.destroyed) {
                self.xhr = null;
                self.$input.attr('aria-busy', 'false');
            }
        });
    };
    function appendHighlighted($target, value, query) {
        var text = value == null ? '' : String(value);
        var needle = query.toLocaleLowerCase();
        var index = text.toLocaleLowerCase().indexOf(needle);
        if (index < 0 || !needle) { $target.text(text); return; }
        $target.append(document.createTextNode(text.slice(0, index)));
        $('<mark>').text(text.slice(index, index + query.length)).appendTo($target);
        $target.append(document.createTextNode(text.slice(index + query.length)));
    }
    function safeUrl(value) {
        try {
            var url = new URL(String(value), window.location.href);
            return /^https?:$/.test(url.protocol) ? url.href : null;
        } catch (_error) { return null; }
    }
    Suggestion.prototype.render = function (results, query) {
        var self = this;
        self.items = [];
        self.active = -1;
        self.$input.removeAttr('aria-activedescendant');
        self.$list.empty();
        self.$message.hide();
        var fragment = document.createDocumentFragment();
        results.slice(0, 80).forEach(function (item) {
            var content = item && item.print && item.print.parsed_content;
            // HTML branches intentionally require migration to structured text.
            if (!content || content.title == null || item.value == null || String(item.value) === '') return;
            if (item.field_key === 'link' && !safeUrl(item.value)) return;
            var index = self.items.push(item) - 1;
            var $row = $('<li>', {
                class: 'wdk-suggestion-item', role: 'option', id: self.id + '-option-' + index,
                'data-index': index, 'aria-selected': 'false'
            });
            var $icon = $('<span>', {class: 'column left', 'aria-hidden': 'true'}).appendTo($row);
            var iconClasses = String(content.icon_class || 'fa fa-search').split(/\s+/).filter(function (token) {
                return /^[a-zA-Z][a-zA-Z0-9_-]*$/.test(token);
            }).join(' ');
            $('<i>').addClass(iconClasses).appendTo($icon);
            var $middle = $('<span>', {class: 'column middle'}).appendTo($row);
            appendHighlighted($('<span>', {class: 'title'}).appendTo($middle), content.title, query);
            if (content.sub_title) $('<span>', {class: 'sub-title'}).text(content.sub_title).appendTo($middle);
            if (content.right_text) $('<span>', {class: 'column right'}).text(content.right_text).appendTo($row);
            fragment.appendChild($row[0]);
        });
        if (!self.items.length) { self.message('empty'); return; }
        self.$list[0].appendChild(fragment);
        self.$scroll.scrollTop(0);
        self.$status.text(self.options.strings.results.replace('{count}', self.items.length));
        self.show();
    };
    Suggestion.prototype.activate = function (index) {
        this.active = index;
        var $rows = this.$list.children('[role="option"]');
        $rows.removeClass('is-active').attr('aria-selected', 'false');
        var $row = $rows.eq(index).addClass('is-active').attr('aria-selected', 'true');
        this.$input.attr('aria-activedescendant', $row.attr('id'));
        var row = $row[0];
        var scroll = this.$scroll[0];
        var rowRect = row.getBoundingClientRect();
        var scrollRect = scroll.getBoundingClientRect();
        if (rowRect.top < scrollRect.top) scroll.scrollTop -= scrollRect.top - rowRect.top;
        else if (rowRect.bottom > scrollRect.bottom) scroll.scrollTop += rowRect.bottom - scrollRect.bottom;
    };
    Suggestion.prototype.keydown = function (event) {
        if (this.composing || event.isComposing || event.keyCode === 229) return;
        if (event.key === 'Escape') {
            if (this.open) event.preventDefault();
            this.cancel(); this.hide(); return;
        }
        if (event.key === 'Tab') { this.cancel(); this.hide(); return; }
        if (event.key === 'Enter' && this.open && this.active >= 0) {
            event.preventDefault(); this.select(this.active); return;
        }
        if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
            event.preventDefault();
            if (!this.open) { this.schedule(); return; }
            if (!this.items.length) return;
            var index = this.active < 0 ? (event.key === 'ArrowDown' ? 0 : this.items.length - 1)
                : (this.active + (event.key === 'ArrowDown' ? 1 : -1) + this.items.length) % this.items.length;
            this.activate(index);
        }
    };
    Suggestion.prototype.select = function (index) {
        var item = this.items[index];
        if (!item) return;
        this.cancel();
        this.hide();
        if (item.field_key === 'link') {
            var url = safeUrl(item.value);
            if (url && this.options.callback_selected.call(this.$input[0], item.value, item) !== false) window.location.assign(url);
            return;
        }
        this.selecting = true;
        try {
            this.$input.val(item.value).trigger('input').trigger('change');
            this.options.callback_selected.call(this.$input[0], item.value, item);
        } finally { this.selecting = false; }
    };
    Suggestion.prototype.destroy = function () {
        this.destroyed = true;
        this.cancel();
        window.cancelAnimationFrame(this.frame);
        this.$input.off(this.ns).removeData('wdkSuggestion');
        $(document).off(this.ns);
        window.removeEventListener('resize', this.onViewport);
        document.removeEventListener('scroll', this.onViewport, true);
        if (window.visualViewport) {
            window.visualViewport.removeEventListener('resize', this.onViewport);
            window.visualViewport.removeEventListener('scroll', this.onViewport);
        }
        this.$root.remove();
        this.cache.clear();
        var $input = this.$input;
        $.each(this.original, function (name, value) {
            if (value === undefined) $input.removeAttr(name); else $input.attr(name, value);
        });
    };
    $.fn.wdkSuggestion = function (options) {
        return this.each(function () {
            var instance = $(this).data('wdkSuggestion');
            if (options === 'destroy') { if (instance) instance.destroy(); return; }
            if (options === 'clearCache') { if (instance) instance.cache.clear(); return; }
            if (typeof options === 'string') return;
            if (instance) instance.destroy();
            $(this).data('wdkSuggestion', new Suggestion(this, options));
        });
    };
})(jQuery); 
