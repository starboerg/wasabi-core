var $ = require('jquery');
window.jQuery = $;
var _ = require('underscore');
var Backbone = require('backbone');
var BaseViewFactory = require('./common/BaseViewFactory');
var bs = require('bootstrap');
//var CoreRouter = require('./router/CoreRouter');

var WS = (function() {

  var eventBus = _.extend({}, Backbone.Events);
  var viewFactory = new BaseViewFactory(eventBus);
  var resizeTimeout = 0;

  return {
    features: {},

    modules: {
      registered: []
    },

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
      //new CoreRouter(options.baseUrl);

      for (var i = 0, len = this.modules.registered.length; i < len; i++) {
        var registered = this.modules.registered[i];
        var name = registered.name;
        var r = registered.require;
        var options = registered.options;
        this.modules[name] = require(r);
        console.log(this.modules);
        this.modules[name].initialize(options);
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
    }
  };

})();

global.window.WS = WS;
