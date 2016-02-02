/*!
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2015 - 2016
 * @version 1.0.0
 *
 * Krajee IP Information fetcher plugin using PHP API from freegeoip.net. The plugin is built to work with
 * `kartik-v/yii2-ipinfo` extension. The plugin refreshes IP information via AJAX on document load.
 *
 * @see http://api.freegeoip.net
 *
 * Author: Kartik Visweswaran
 * Copyright: 2015, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
(function ($) {
    "use strict";

    var KvIpInfo = function (element, options) {
        var self = this;
        self.$element = $(element);
        self.init(options);
    };

    KvIpInfo.prototype = {
        constructor: KvIpInfo,
        init: function (options) {
            var self = this, $el = self.$element;
            $.each(options, function (key, value) {
                self[key] = value;
            });
            $.ajax({
                url: self.url,
                type: 'GET',
                dataType: 'json',
                data: self.params,
                beforeSend: function (jqXHR) {
                    $el.trigger('beforesend.kvipinfo', [jqXHR]);
                },
                success: function (data, textStatus, jqXHR) {
                    //noinspection JSUnresolvedVariable
                    var out = data, $flag, opts, css, content = '', country = out.country_code;
                    $el.trigger('success.kvipinfo', [data, textStatus, jqXHR]);
                    if (!out || !country) {
                        $el.html(self.noData);
                    } else {
                        if (self.flagWrapper) {
                            opts = $.isEmptyObject(self.flagOptions) ? {} : self.flagOptions;
                            css = 'flag-icon-' + country.toLowerCase();
                            $flag = $(document.createElement('span')).attr(opts).removeClass(css).addClass(css);
                            $('#' + self.flagWrapper).html('').append($flag);
                        }
                        $.each(self.fields, function (key, value) {
                            if (out[value] !== undefined) {
                                content += "<tr><th>" + self.defaultFields[value] + "</th>" +
                                    "<td>" + out[value] + "</td></tr>\n";
                                self.setContent('p', value, out[value]);
                                self.setContent('i', value, out[value]);
                            }
                        });
                        if (content) {
                            self.setContent('p', 'table', content);
                            self.setContent('i', 'table', content);
                        }
                        $el.html($el.find('.kv-hide').html());
                        if (!$el.text().length) {
                            $el.html(self.noData);
                        }
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $el.trigger('error.kvipinfo', [jqXHR, textStatus, errorThrown]).html(self.errorData);
                }
            });
        },
        setContent: function(type, tag, content) {
            var self = this, id = self.$element.attr('id'), sel = tag === 'table' ? 'table.' : '#',
                $fld = $(sel + id + '-' + tag + '-' + type);
            if (!$fld.length) {
                return;
            }
            if (content.length) {
                $fld.html(content);
            } else {
                $fld.remove();
            }

        }
    };

    $.fn.kvIpInfo = function (option) {
        var args = Array.apply(null, arguments);
        args.shift();
        return this.each(function () {
            var $this = $(this), defaults, data = $this.data('kvIpInfo'), opts = typeof option === 'object' && option;
            if (!data) {
                defaults = $.extend({}, $.fn.kvIpInfo.defaults);
                data = new KvIpInfo(this, $.extend(defaults, opts, $this.data()));
                $this.data('kvIpInfo', data);
            }
            if (typeof option === 'string') {
                data[option].apply(data, args);
            }
        });
    };

    $.fn.kvIpInfo.defaults = {
        flagWrapper: '',
        flagOptions: {},
        fields: [],
        defaultFields: {},
        url: '',
        params: {},
        noData: '',
        errorData: ''
    };

    $.fn.kvIpInfo.Constructor = KvIpInfo;
})(window.jQuery);