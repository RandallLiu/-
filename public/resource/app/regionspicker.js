
(function (factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as anonymous module.
    define(['jquery', 'RegionsData'], factory);
  } else if (typeof exports === 'object') {
    // Node / CommonJS
    factory(require('jquery'), require('RegionsData'));
  } else {
    // Browser globals.
    factory(jQuery, RegionsData);
  }
})(function ($, RegionsData) {

  'use strict';

  if (typeof RegionsData === 'undefined') {
    throw new Error('数据未加载...');
  }

  var NAMESPACE = 'distpicker';
  var EVENT_CHANGE = 'change.' + NAMESPACE;
  var PROVINCE = 'province';
  var CIRY = 'city';
  var DISTRICT = 'district';

  function Distpicker(element, options) {
    this.$element = $(element);
    this.options = $.extend({}, Distpicker.DEFAULTS, $.isPlainObject(options) && options);
    this.placeholders = $.extend({}, Distpicker.DEFAULTS);
    this.active = false;
    this.init();
  }

  Distpicker.prototype = {
    constructor: Distpicker,
    // 初始化
    init: function () {
      var options = this.options;
      var $select = this.$element.find('select');
      var length = $select.length;
      var data = {};

      $select.each(function () {
        $.extend(data, $(this).data());
      });

      $.each([PROVINCE, CIRY, DISTRICT], $.proxy(function (i, type) {
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
          this.output(DISTRICT);
        }, this)));
      }

      if (this.$city) {
        this.$city.on(EVENT_CHANGE, (this._changeCity = $.proxy(function () {
          this.output(DISTRICT);
        }, this)));
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
        type === PROVINCE ? 86 :
        type === CIRY ? this.$province && this.$province.find(':selected').data('code') :
        type === DISTRICT ? this.$city && this.$city.find(':selected').data('code') : code
      );

      if ( options.region === 'all' ) {
        districts = $.isNumeric(code) ? Regions[code] : null;
      }else {
        districts = $.isNumeric(code) ? RegionsData[code] : null;
      }
      if ($.isPlainObject(districts)) {
        $.each(districts, function (code, address) {
          var selected = address === value;
          if (selected) {
            matched = true;
          }

          data.push({
            code: code,
            address: address,
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
          code: '',
          address: placeholders[type],
          selected: false
        });
      }

      $select.html(this.getList(data)).val('').trigger('change');
    },

    getList: function (data) {
      var list = [];

      $.each(data, function (i, n) {
        list.push(
          '<option' +
          ' value="' + (n.address && n.code ? n.address : '') + '"' +
          ' data-code="' + (n.code || '') + '"' +
          (n.selected ? ' selected' : '') +
          '>' +
            (n.address || '') +
          '</option>'
        );
      });

      return list.join('');
    },

    reset: function (deep) {
      if (!deep) {
        this.output(PROVINCE);
        this.output(CIRY);
        this.output(DISTRICT);
      } else if (this.$province) {
        this.$province.find(':first').prop('selected', true).trigger('change');
      }
    },

    destroy: function () {
      this.unbind();
      this.$element.removeData(NAMESPACE);
    }
  };

  Distpicker.DEFAULTS = {
    autoSelect: true,
    placeholder: true,
    province: '--省--',
    city: '--市--',
    district: '--区/县--'
  };

  Distpicker.setDefaults = function (options) {
    $.extend(Distpicker.DEFAULTS, options);
  };

  Distpicker.other = $.fn.distpicker;

  $.fn.distpicker = function (option) {
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
        $this.data(NAMESPACE, (data = new Distpicker(this, options)));
      }

      if (typeof option === 'string' && $.isFunction(fn = data[option])) {
        fn.apply(data, args);
      }
    });
  };

  $.fn.distpicker.Constructor = Distpicker;
  $.fn.distpicker.setDefaults = Distpicker.setDefaults;

  $.fn.distpicker.noConflict = function () {
    $.fn.distpicker = Distpicker.other;
    return this;
  };

  $(function () {
    $('[data-toggle="distpicker"]').distpicker();
  });
});
