define(function(require) {
  var $ = require('jquery');
  var _ = require('underscore');
  var Backbone = require('backbone');
  var BaseViewFactory = require('./common/BaseViewFactory');
  require('bootstrap.dropdown');

  var eventBus = _.extend({}, Backbone.Events);
  var viewFactory = new BaseViewFactory(eventBus);
  var resizeTimeout = 0;
  var strundefined = 'undefined';

  if (typeof window.WS !== strundefined) {
    return window.WS;
  }

  var WS = {
    features: {},

    modules: {
      registered: []
    },

    views: [],

    /**
     * Global event bus.
     *
     * @type {Backbone.Events}
     */
    eventBus: eventBus,

    /**
     * Global view factory to instantiate new views.
     *
     * @type {BaseViewFactory}
     */
    viewFactory: viewFactory,

    boot: function () {
      var that = this;

      function loadModule(module) {
        var name = module.name;
        var r = module.require;
        var options = module.options;
        require([r], function(module) {
          that.modules[name] = module;
          that.modules[name].initialize(options);
        });
      }

      for (var i = 0, len = this.modules.registered.length; i < len; i++) {
        loadModule(this.modules.registered[i]);
      }

      $(window).on('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
          WS.eventBus.trigger('window.resize');
        }, 100);
      });
    },

    registerModule: function (name, options) {
      this.modules.registered.push({
        name: name,
        require: name,
        options: options || {}
      });
    },

    get: function (name) {
      if (this.modules[name]) {
        return this.modules[name];
      }
      console.debug('module "' + name + " is not registered.");
    },

    createView: function(viewClass, options) {
      return this.viewFactory.create(viewClass, options || {});
    },

    createViews: function($elements, ViewClass, options) {
      var views = [];
      if ($elements.length > 0) {
        $elements.each(function(index, el) {
          views.push(WS.createView(ViewClass, _.extend({el: el}, options || {})));
        });
      }
      return views;
    },

    blockElement: function($el, options) {
      options = $.extend({}, {
        deltaWidth: 0,
        deltaHeight: 0,
        backgroundColor: '#fff',
        opacity: 0.6,
        cursor: 'wait',
        zIndex: 9997,
        whiteSpinner: false
      }, options);
      var $blockBackdrop = $('<div class="block-backdrop"><div class="spinner"></div></div>');
      var offset = $el.offset();
      var borderLeft = $el.css('borderLeftWidth');
      var borderTop = $el.css('borderTopWidth');
      var width = $el.innerWidth() + options.deltaWidth;
      var height = $el.innerHeight() + options.deltaHeight;

      if (borderLeft !== '') {
        borderLeft = parseInt(borderLeft.split('px')[0]);
        offset.left += borderLeft;
      }

      if (borderTop !== '') {
        borderTop = parseInt(borderTop.split('px')[0]);
        offset.top += borderTop;
      }

      $blockBackdrop
        .css({
          position: 'absolute',
          top: 0,
          left: 0,
          width: width,
          height: height,
          backgroundColor: options.backgroundColor,
          opacity: options.opacity,
          cursor: options.cursor,
          zIndex: options.zIndex
        })
        .hide()
        .appendTo($el);

      if (options.whiteSpinner) {
        $blockBackdrop.find('.spinner').addClass('spinner-white');
      }

      $blockBackdrop.fadeIn(100, function() {
        $blockBackdrop.on('mousedown mouseup keydown keypress keyup touchstart touchend touchmove', this.handleBlockedEvent);
      });
    },

    /**
     * Unblock a view.
     *
     * @param {jQuery} $el
     * @param callback
     */
    unblockElement: function($el, callback) {
      callback = callback || function() {};
      if (this.spinner) {
        this.spinner.stop();
      }
      $el.find('.block-backdrop')
        .off('mousedown mouseup keydown keypress keyup touchstart touchend touchmove', this.handleBlockedEvent)
        .remove();
      if (typeof callback === 'function') {
        callback.call(this);
      }
    },

    /**
     * Catch all event handler for blocked views.
     *
     * @param event
     */
    handleBlockedEvent: function(event) {
      event.preventDefault();
      event.stopPropagation();
    }
  };

  window.WS = WS;

  return WS;
});
