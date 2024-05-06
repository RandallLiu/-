
(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as anonymous module.
        define(['jquery', 'ProjectData'], factory);
    } else if (typeof exports === 'object') {
        // Node / CommonJS
        factory(require('jquery'), require('ProjectData'));
    } else {
        // Browser globals.
        factory(jQuery, ProjectData);
    }
})(function ($, ProjectData) {
    'use strict';



    if (typeof ProjectData === 'undefined') {
        throw new Error('数据未加载...');
    }

    var NAMESPACE = 'projectpicker';
    var EVENT_CHANGE = 'change.' + NAMESPACE;
    var PROVINCE = 'province';
    var CIRY = 'city';

    function Propicker(element, options) {
        this.$element = $(element);
        this.options = $.extend({}, Propicker.DEFAULTS, $.isPlainObject(options) && options);
        this.placeholders = $.extend({}, Propicker.DEFAULTS);
        this.active = false;
        this.init();
    }

    Propicker.prototype = {
        constructor: Propicker,
        // 初始化
        init: function () {
            var options = this.options;
            var $select = this.$element.find('select.pj');
            var length = $select.length;
            var data = {};

            $select.each(function () {
                $.extend(data, $(this).data());
            });

            $.each([PROVINCE, CIRY], $.proxy(function (i, type) {
                if (data[type]) {
                    options[type] = data[type];
                    this['$' + type] = $select.filter('[data-' + type + ']');
                } else {
                    this['$' + type] = length > i ? $select.eq(i) : null;
                }
            }, this));
            // 事件绑定
            this.bind();
            //
            this.reset();

            this.active = true;

        },

        bind: function () {
            if (this.$province) {
                this.$province.on(EVENT_CHANGE, (this._changeProvince = $.proxy(function () {
                    this.output(CIRY);
                }, this)));
            }

            if (this.$city) {
                // this.$city.on(EVENT_CHANGE, (this._changeCity = $.proxy(function () {
                //     this.output(DISTRICT);
                // }, this)));
            }
        },
        // 事件解绑
        unbind: function () {
            if (this.$province) {
                this.$province.off(EVENT_CHANGE, this._changeProvince);
            }

            if (this.$city) {
                this.$city.off(EVENT_CHANGE, this._changeCity);
            }
        },
        // 数据输出
        output: function (type) {
            var options = this.options;
            var placeholders = this.placeholders;
            var $select = this['$' + type];
            var districts = {};
            var data = [];
            var code;
            var matched;
            var value;

            if (!$select || !$select.length) {
                return;
            }

            value = options[type];

            code = (
                type === PROVINCE ? 10000000 : (type === CIRY ? this.$province && this.$province.val() : "")
            );

            // console.log("type",type)
            districts = code ? ProjectData[code] : null;
            if ( (districts) || $.isPlainObject(districts)) {
                $.each(districts, function (code, name) {
                    // console.log(code,name)
                    // console.log(code,name)
                    if (type === 'province') {
                        code = name.no
                        name = name.name
                    }

                    if (type === 'city') {
                        $.each(name, function (k, v) {
                            code = k
                            name = v
                        })
                    }

                    var selected = name === value;
                    if (selected) {
                        matched = true;
                    }

                    data.push({
                        code: code,
                        name: name,
                        selected: selected
                    });

                });
            }

            if (!matched) {
                if (data.length && (options.autoSelect || options.autoselect)) {
                    data[0].selected = true;
                }

                if (!this.active && value) {
                    placeholders[type] = value;
                }
            }

            if (options.placeholder) {
                data.unshift({
                    code: '9999999999',
                    name: placeholders[type],
                    selected: false
                });
            }
            // console.log("data:",data)
            $select.html(this.set_options(data)).val("9999999999").trigger('change');
        },

        set_options: function (data) {
            var list = [];
            // 去除重复对象数组
            const uniqueArr = Array.from(new Set(data.map(JSON.stringify))).map(JSON.parse);
            $.each(uniqueArr, function (i, n) {
                list.push(
                    '<option' +
                    ' value="' + (n.name && n.code ? n.code : '') + '"' +
                    ' data-code="' + (n.code || '') + '"' +
                    (n.selected ? ' selected' : '') +
                    '>' +
                    (n.name || '') +
                    '</option>'
                );
            });

            return list.join('');
        },

        reset: function (deep) {
            if (!deep) {
                this.output(PROVINCE);
                this.output(CIRY);
                // this.output(DISTRICT);
            } else if (this.$province) {
                this.$province.find(':first').prop('selected', true).trigger('change');
            }
        },

        destroy: function () {
            this.unbind();
            this.$element.removeData(NAMESPACE);
        }
    };

    Propicker.DEFAULTS = {
        autoSelect: true,
        placeholder: true,
        province: '--所属客户/项目--',
        city: '--所属地点--',
    };

    Propicker.setDefaults = function (options) {
        $.extend(Propicker.DEFAULTS, options);
    };

    Propicker.other = $.fn.pjpicker;

    $.fn.pjpicker = function (option) {
        var args = [].slice.call(arguments, 1);

        return this.each(function () {
            var $this = $(this);
            var data = $this.data(NAMESPACE);
            var options;
            var fn;

            if (!data) {
                if (/destroy/.test(option)) {
                    return;
                }

                options = $.extend({}, $this.data(), $.isPlainObject(option) && option);
                $this.data(NAMESPACE, (data = new Propicker(this, options)));
            }

            if (typeof option === 'string' && $.isFunction(fn = data[option])) {
                fn.apply(data, args);
            }
        });
    };

    $.fn.pjpicker.Constructor = Propicker;
    $.fn.pjpicker.setDefaults = Propicker.setDefaults;

    $.fn.pjpicker.noConflict = function () {
        $.fn.pjpicker = Propicker.other;
        return this;
    };

    $(function () {
        $('[data-toggle="pjpicker"]').pjpicker();
    });
});
