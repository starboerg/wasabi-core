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
        resizeTimeout = setTimeout(_.bind(function() {
          this.eventBus.trigger('window.resize');
        }, this), 100);
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
    }
  };

  window.WS = WS;

  return WS;
});
