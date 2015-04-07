require=(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
var _ = require('underscore');

var BaseViewFactory = function(eventBus) {
  this.eventBus = eventBus;
  this.registry = {
    factory: this
  };
};

BaseViewFactory.prototype = _.extend({}, {

  register: function(key, value) {
    this.registry[key] = value;
    return this.registry[key];
  },

  create: function(ViewClass, options) {
    var klass, passedOptions;
    options = options || {};
    passedOptions = _.extend(options, this.registry, {
      eventBus: this.eventBus
    });
    klass = ViewClass;
    klass.prototype.eventBus = this.eventBus;
    return new klass(passedOptions);
  }

});

module.exports = BaseViewFactory;

},{"underscore":"underscore"}],2:[function(require,module,exports){
var vMenu = require('./views/Menu');
var vNavigationToggle = require('./views/NavigationToggle');

var Navigation = function() {
  this.menu = WS.viewFactory.create(vMenu, {});
  this.navigationToggle = WS.viewFactory.create(vNavigationToggle, {});
};

module.exports = Navigation;

},{"./views/Menu":3,"./views/NavigationToggle":4}],3:[function(require,module,exports){
(function (global){
var $ = require('jquery');
var BaseView = require('wasabi.BaseView');
var WS = global.window.WS;

/**
 * Default options.
 *
 * @type {{collapsedClass: string, openClass: string, popoutClass: string}}
 */
var defaults = {
  collapsedClass: 'collapsed',
  openClass: 'open',
  popoutClass: 'popout'
};

/**
 * Gets the list element of the current event target.
 *
 * @param {Object} event
 * @param {string=} cls
 * @returns {jQuery}
 */
var _getEventTarget = function(event, cls) {
  var $target = $(event.target);
  if (!$target.is('li')) {
    cls = cls || '';
    return $target.closest('li' + cls);
  }

  return $target;
};

module.exports = BaseView.extend({

  /**
   * The element of this view.
   *
   * @type {string} CSS selector
   */
  el: '#backend-menu > ul',

  /**
   * Registered events of this view.
   *
   * @returns {Object}
   */
  events: function() {
    var events = {
      'click > li:has(ul) > a': 'onMainItemClick'
    };

    if (this.isCollapsed) {
      events = $.extend(events, {
        'mouseenter > li': 'onMainItemMouseenter',
        'mouseleave > li': 'onMainItemMouseleave'
      });
    }

    return events;
  },

  globalEvents: {
    'window.resize': 'collapseMenu'
  },

  /**
   * Determines if the navigation bar is collapsed to the small size (true) or not (false).
   *
   * @type {boolean}
   */
  isCollapsed: false,

  /**
   * Options
   *
   * @param {Object}
   */
  options: {},

  /**
   * Initialization of the view.
   *
   * @param {Object=} options
   */
  initialize: function(options) {
    this.options = $.extend({}, defaults, options);
    this.$('> li').filter('.active').prev().addClass('prev-active');
    this.collapseMenu();
    //if (!WS.features.hasTouch) {
    //  this.$el.parent().perfectScrollbar();
    //}
    //this.listenTo(eventify(window), 'resize', _.bind(this.onResize, this));
  },

  /**
   * Attaches/detaches event handlers if the navigation is collapsed
   * and toggles the .collapsed class.
   *
   * The visuals of the collapsed navigation are done via media queries and not via JS.
   */
  collapseMenu: function() {
    var prevCollapsed = this.isCollapsed;
    this.isCollapsed = (this.$('> li > a > .item-name').first().css('display') === 'none');
    if (this.isCollapsed) {
      this.$el.addClass(this.options.collapsedClass);
      if (this.isCollapsed !== prevCollapsed) {
        this.delegateEvents(this.events());
      }
    } else {
      this.$el.removeClass(this.options.collapsedClass);
      this.delegateEvents(this.events());
    }
  },

  /**
   * onMainItemClick event handler
   * Toggles the .open class on every li that has a child ul.
   *
   * @param {Object} event
   */
  onMainItemClick: function(event) {
    _getEventTarget(event).toggleClass(this.options.openClass);
  },

  /**
   * onMainItemMouseenter event handler
   * Add .popout class to hovered li if the menu is collapsed.
   *
   * @param {Object} event
   */
  onMainItemMouseenter: function(event) {
    if (!this.isCollapsed) return;
    _getEventTarget(event).addClass(this.options.popoutClass);
  },

  /**
   * onMainItemMouseLeave event handler
   * Remove .popout class from the previously hovered li if the menu is collapsed.
   *
   * @param {Object} event
   */
  onMainItemMouseleave: function(event) {
    if (!this.isCollapsed) return;
    _getEventTarget(event, '.' + this.options.popoutClass).removeClass(this.options.popoutClass);
  },

  /**
   * onWindowResize event handler
   * Calls collapseMenu on window resize.
   */
  onResize: function() {
    clearTimeout(this.resizeTimeout);
    this.resizeTimeout = setTimeout(_.bind(this.collapseMenu, this), 100);
  }

});

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"jquery":"jquery","wasabi.BaseView":"wasabi.BaseView"}],4:[function(require,module,exports){
var $ = require('jquery');
var BaseView = require('wasabi.BaseView');

module.exports = (function() {

  /**
   * Holds a reference to the body.
   *
   * @type {jQuery}
   */
  var $body = $('body');

  /**
   * Default options.
   *
   * @type {{navClosedClass: string}}
   */
  var defaults = {
    navClosedClass: 'nav-closed'
  };

  return BaseView.extend({

    /**
     * The element of this view.
     *
     * @type {string} CSS selector
     */
    el: 'body > header .toggle-nav',

    /**
     * Registered events of this view.
     *
     * @type {Object}
     */
    events: {
      'click': 'toggleNav'
    },

    /**
     * Determines if the navigation bar is collapsed to the small size (true) or not (false).
     *
     * @type {boolean}
     */
    isClosed: false,

    /**
     * Options
     *
     * @param {Object}
     */
    options: {},

    /**
     * Initialization of the view.
     *
     * @param {Object=} options
     */
    initialize: function(options) {
      this.options = $.extend({}, defaults, options);
      this.isClosed = $body.hasClass(this.options.navClosedClass);
    },

    /**
     * toggleNav event handler
     * Toggles navClosedClass on the body to show/hide the navigation.
     */
    toggleNav: function() {
      if (!this.closed) {
        $body.addClass(this.options.navClosedClass);
        this.closed = true;
      } else {
        $body.removeClass(this.options.navClosedClass);
        this.closed = false;
      }
    }
  });

})();

},{"jquery":"jquery","wasabi.BaseView":"wasabi.BaseView"}],5:[function(require,module,exports){
(function (global){
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

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"./common/BaseViewFactory":1,"backbone":"backbone","bootstrap":"bootstrap","jquery":"jquery","underscore":"underscore"}],"wasabi.BaseView":[function(require,module,exports){
var $ = require('jquery');
var _ = require('underscore');
var Backbone = require('backbone');
var Spinner = require('spin.js');

Backbone.$ = $;

(function(View){
  'use strict';
  // Additional extension layer for Views
  View.fullExtend = function (protoProps, staticProps) {
    // Call default extend method
    var extended = View.extend.call(this, protoProps, staticProps);
    // Add a usable super method for better inheritance
    extended.prototype._super = this.prototype;
    // Apply new or different events on top of the original
    if (protoProps.events) {
      for (var k in this.prototype.events) {
        if (!extended.prototype.events[k]) {
          extended.prototype.events[k] = this.prototype.events[k];
        }
      }
    }
    return extended;
  };

})(Backbone.View);

module.exports = Backbone.View.extend({

  $blockBackdrop: $('<div class="block-backdrop"></div>'),
  numberOfBlocks: 0,
  spinner: null,

  delegateEvents: function(events) {
    var event, handler, results;

    Backbone.View.prototype.delegateEvents.call(this, events);

    this.globalEvents = this.globalEvents || {};

    if (typeof this.globalEvents === 'function') {
      this.globalEvents = this.globalEvents();
    }

    results = [];

    for (event in this.globalEvents) {
      if (!this.globalEvents.hasOwnProperty(event)) {
        continue;
      }
      handler = this.globalEvents[event];
      results.push(this.eventBus.bind(event, _.bind(this[handler], this)));
    }

    return results;
  },

  /**
   * Block a view by:
   * - overlaying a div with a specified color and opacity
   * - showing a spinner in the center of this div
   * - catching all user generated events on the blocked element
   *
   * @param options
   */
  blockThis: function(options) {
    this.numberOfBlocks++;
    var offset = this.$el.offset();
    var borderLeft = this.$el.css('borderLeftWidth');
    var borderTop = this.$el.css('borderTopWidth');
    var width = this.$el.innerWidth() + (options.deltaWidth || 0);
    var height = this.$el.innerHeight() + (options.deltaHeight || 0);

    if (borderLeft !== '') {
      borderLeft = parseInt(borderLeft.split('px')[0]);
      offset.left += borderLeft;
    }

    if (borderTop !== '') {
      borderTop = parseInt(borderTop.split('px')[0]);
      offset.top += borderTop;
    }

    this.$blockBackdrop.css({
      position: 'absolute',
      top: offset.top,
      left: offset.left,
      width: width,
      height: height,
      backgroundColor: options.backgroundColor || 'transparent',
      opacity: options.opacity || 0.6,
      cursor: options.cursor || 'wait',
      zIndex: options.zIndex || 9997
    }).hide().appendTo($('body'));

    this.$blockBackdrop.fadeIn(100, $.proxy(function() {
      this.$blockBackdrop.on('mousedown mouseup keydown keypress keyup touchstart touchend touchmove', _.bind(this.handleBlockedEvent, this));
      if (options.spinner && options.spinner !== false) {
        this.spinner = this.spinner || new Spinner(options.spinner || false);
      }
      if (this.spinner) {
        this.spinner.spin(this.$blockBackdrop.get(0));
      }
    }, this));
  },

  /**
   * Unblock a view.
   *
   * @param callback
   */
  unblockThis: function(callback) {
    if (this.numberOfBlocks >= 1) {
      this.numberOfBlocks--;
    }
    if (this.numberOfBlocks === 0) {
      callback = callback || function() {};
      if (this.spinner) {
        this.spinner.stop();
      }
      this.$blockBackdrop.remove();
      this.$blockBackdrop.off('mousedown mouseup keydown keypress keyup touchstart touchend touchmove', _.bind(this.handleBlockedEvent, this));
      if (typeof callback === 'function') {
        callback.call(this);
      }
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
  },

  destroy: function() {
    // COMPLETELY UNBIND THE VIEW
    this.undelegateEvents();

    this.$el.removeData().unbind();

    // Remove view from DOM
    this.remove();
    Backbone.View.prototype.remove.call(this);
  }

});

},{"backbone":"backbone","jquery":"jquery","spin.js":"spin.js","underscore":"underscore"}],"wasabi.SpinPresets":[function(require,module,exports){
module.exports = {
  small:  { lines: 12, length: 0, width: 3, radius: 6 },
  medium: { lines: 9 , length: 4, width: 2, radius: 3 },
  large:  { lines: 11, length: 7, width: 2, radius: 5 },
  mega:   { lines: 11, length: 10, width: 3, radius: 8}
};

},{}],"wasabi/core":[function(require,module,exports){
var $ = require('jquery');
var _ = require('underscore');
var cNavigation = require('./components/navigation/navigation');
var win = window;
var doc = document;

/**
 * Setup default ajax options and global ajax event handlers.
 *
 * @private
 */
function _setupAjax() {
  $.ajaxSetup({
    dataType: 'json'
  });
  $(doc)
    .ajaxSuccess(_onAjaxSuccess)
    .ajaxError(_onAjaxError);
}

/**
 * Default ajax success handler.
 * Displays a flash message if the response contains
 * {
     *   'status': '...' # the class of the flash message
     *   'flashMessage': '...' # the text of the flash message
     * }
 *
 * @param event
 * @param xhr
 * @private
 */
function _onAjaxSuccess(event, xhr) {
  if (xhr.status == 200 && xhr.statusText == 'OK') {
    var data = $.parseJSON(xhr.responseText) || {};

    if (typeof data.status !== 'undefined') {
      if (typeof data.flash !== 'undefined') {
        _flashMessage(data.flash);
      }
      if (typeof data.redirect !== 'undefined') {
        win.location = data.redirect;
      }
    }
  }
}

/**
 * Default ajax error handler.
 *
 * @param event
 * @param xhr
 * @private
 */
function _onAjaxError(event, xhr) {
  var data;
  if (xhr.status == 401) {
    data = $.parseJSON(xhr.responseText) || {};
    if (typeof data.name !== 'undefined') {
      if (confirm(data.name)) {
        win.location.reload();
      } else {
        win.location.reload();
      }
    }
  }
  if (xhr.status == 500) {
    data = $.parseJSON(xhr.responseText) || {};
    if (typeof data.name !== 'undefined') {
      _flashMessage({
        before: 'div.title-pad',
        class: 'error',
        message: data.name
      });
    }
  }
}

/**
 * Render a flash message after a specific element.
 *
 * @param {Object} flash The flash object returned by the ajax request.
 * @private
 */
function _flashMessage(flash) {
  var insertBefore = flash.before || false;
  var insertAfter = flash.after || false;
  var message = flash.message;
  var cls = (flash.class) ? 'flash-' + flash.class : 'flash-success';
  var $target = false;
  var $flash = $('<div/>').addClass(cls).html(message);

  if (insertBefore) {
    $target = $(insertBefore);
    $target.prev('.' + cls).remove();
    $flash.insertBefore($target);
  } else if (insertAfter) {
    $target = $(insertAfter);
    $target.next('.' + cls).remove();
    $flash.insertAfter($target);
  }
}

function _initializeBackendViews() {
  this.components.navigation = new cNavigation();
}

/**
 * Wasabi Core module
 * Initializes:
 *   - $.ajax (custom handlers to check login status in ajax responses)
 *   - Backend UI components that are used on every page
 *   - runs feature tests (touch, ...) via modernizr
 *   - sets up a global thresholded window resize event handler that
 *     publishes the event to the backbone views as 'window.resize'
 *
 * @param options
 * @constructor
 */
module.exports = {

  /**
   * Core options
   * that have been supplied through the Wasabi Core default layout.
   *
   * @type {object}
   */
  options: {},

  /**
   * Holds a reference to the window.
   *
   * @type {window}
   */
  w: null,

  /**
   * Holds a reference to the document.
   *
   * @type {HTMLDocument}
   */
  d: null,

  /**
   * Hold a reference to the html tag
   *
   * @type {jQuery}
   */
  $html: null,

  /**
   * Hold a reference to the body tag
   *
   * @type {jQuery}
   */
  $body: null,

  /**
   * @type {Array<BaseView>}
   */
  views: [],

  /**
   * @type {Array<Backbone.Collection>}
   */
  collections: [],

  /**
   * @type {Array<Backbone.Model>}
   */
  models: [],

  /**
   * Resize timeout for window.resize events.
   *
   * @type {number}
   */
  resizeTimeout: 0,

  /**
   * @type {object}
   */
  components: {},

  /**
   * Initialization of the wasabi core module.
   *
   * @param options
   */
  initialize: function(options) {
    this.options = options || {};
    this.w = win;
    this.d = doc;
    this.$html = $('html');
    this.$body = $('body');

    _setupAjax.call(this);
    _initializeBackendViews.call(this);
  }
};

},{"./components/navigation/navigation":2,"jquery":"jquery","underscore":"underscore"}]},{},[5])
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9ncnVudC1icm93c2VyaWZ5LWJvd2VyLXJlc29sdmUvbm9kZV9tb2R1bGVzL2Jyb3dzZXJpZnkvbm9kZV9tb2R1bGVzL2Jyb3dzZXItcGFjay9fcHJlbHVkZS5qcyIsInNyYy9Bc3NldHMvanMvY29tbW9uL0Jhc2VWaWV3RmFjdG9yeS5qcyIsInNyYy9Bc3NldHMvanMvY29yZS9jb21wb25lbnRzL25hdmlnYXRpb24vbmF2aWdhdGlvbi5qcyIsInNyYy9Bc3NldHMvanMvY29yZS9jb21wb25lbnRzL25hdmlnYXRpb24vdmlld3MvTWVudS5qcyIsInNyYy9Bc3NldHMvanMvY29yZS9jb21wb25lbnRzL25hdmlnYXRpb24vdmlld3MvTmF2aWdhdGlvblRvZ2dsZS5qcyIsInNyYy9Bc3NldHMvanMvd2FzYWJpLmpzIiwic3JjL0Fzc2V0cy9qcy9jb21tb24vQmFzZVZpZXcuanMiLCJzcmMvQXNzZXRzL2pzL2NvbW1vbi9TcGluUHJlc2V0cy5qcyIsInNyYy9Bc3NldHMvanMvY29yZS9tYWluLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FDQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDOUJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7QUNUQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7OztBQzVKQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7QUMvRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7QUMzRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUN2SkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FDTkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBIiwiZmlsZSI6ImdlbmVyYXRlZC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzQ29udGVudCI6WyIoZnVuY3Rpb24gZSh0LG4scil7ZnVuY3Rpb24gcyhvLHUpe2lmKCFuW29dKXtpZighdFtvXSl7dmFyIGE9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtpZighdSYmYSlyZXR1cm4gYShvLCEwKTtpZihpKXJldHVybiBpKG8sITApO3ZhciBmPW5ldyBFcnJvcihcIkNhbm5vdCBmaW5kIG1vZHVsZSAnXCIrbytcIidcIik7dGhyb3cgZi5jb2RlPVwiTU9EVUxFX05PVF9GT1VORFwiLGZ9dmFyIGw9bltvXT17ZXhwb3J0czp7fX07dFtvXVswXS5jYWxsKGwuZXhwb3J0cyxmdW5jdGlvbihlKXt2YXIgbj10W29dWzFdW2VdO3JldHVybiBzKG4/bjplKX0sbCxsLmV4cG9ydHMsZSx0LG4scil9cmV0dXJuIG5bb10uZXhwb3J0c312YXIgaT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2Zvcih2YXIgbz0wO288ci5sZW5ndGg7bysrKXMocltvXSk7cmV0dXJuIHN9KSIsInZhciBfID0gcmVxdWlyZSgndW5kZXJzY29yZScpO1xyXG5cclxudmFyIEJhc2VWaWV3RmFjdG9yeSA9IGZ1bmN0aW9uKGV2ZW50QnVzKSB7XHJcbiAgdGhpcy5ldmVudEJ1cyA9IGV2ZW50QnVzO1xyXG4gIHRoaXMucmVnaXN0cnkgPSB7XHJcbiAgICBmYWN0b3J5OiB0aGlzXHJcbiAgfTtcclxufTtcclxuXHJcbkJhc2VWaWV3RmFjdG9yeS5wcm90b3R5cGUgPSBfLmV4dGVuZCh7fSwge1xyXG5cclxuICByZWdpc3RlcjogZnVuY3Rpb24oa2V5LCB2YWx1ZSkge1xyXG4gICAgdGhpcy5yZWdpc3RyeVtrZXldID0gdmFsdWU7XHJcbiAgICByZXR1cm4gdGhpcy5yZWdpc3RyeVtrZXldO1xyXG4gIH0sXHJcblxyXG4gIGNyZWF0ZTogZnVuY3Rpb24oVmlld0NsYXNzLCBvcHRpb25zKSB7XHJcbiAgICB2YXIga2xhc3MsIHBhc3NlZE9wdGlvbnM7XHJcbiAgICBvcHRpb25zID0gb3B0aW9ucyB8fCB7fTtcclxuICAgIHBhc3NlZE9wdGlvbnMgPSBfLmV4dGVuZChvcHRpb25zLCB0aGlzLnJlZ2lzdHJ5LCB7XHJcbiAgICAgIGV2ZW50QnVzOiB0aGlzLmV2ZW50QnVzXHJcbiAgICB9KTtcclxuICAgIGtsYXNzID0gVmlld0NsYXNzO1xyXG4gICAga2xhc3MucHJvdG90eXBlLmV2ZW50QnVzID0gdGhpcy5ldmVudEJ1cztcclxuICAgIHJldHVybiBuZXcga2xhc3MocGFzc2VkT3B0aW9ucyk7XHJcbiAgfVxyXG5cclxufSk7XHJcblxyXG5tb2R1bGUuZXhwb3J0cyA9IEJhc2VWaWV3RmFjdG9yeTtcclxuIiwidmFyIHZNZW51ID0gcmVxdWlyZSgnLi92aWV3cy9NZW51Jyk7XHJcbnZhciB2TmF2aWdhdGlvblRvZ2dsZSA9IHJlcXVpcmUoJy4vdmlld3MvTmF2aWdhdGlvblRvZ2dsZScpO1xyXG5cclxudmFyIE5hdmlnYXRpb24gPSBmdW5jdGlvbigpIHtcclxuICB0aGlzLm1lbnUgPSBXUy52aWV3RmFjdG9yeS5jcmVhdGUodk1lbnUsIHt9KTtcclxuICB0aGlzLm5hdmlnYXRpb25Ub2dnbGUgPSBXUy52aWV3RmFjdG9yeS5jcmVhdGUodk5hdmlnYXRpb25Ub2dnbGUsIHt9KTtcclxufTtcclxuXHJcbm1vZHVsZS5leHBvcnRzID0gTmF2aWdhdGlvbjtcclxuIiwidmFyICQgPSByZXF1aXJlKCdqcXVlcnknKTtcclxudmFyIEJhc2VWaWV3ID0gcmVxdWlyZSgnd2FzYWJpLkJhc2VWaWV3Jyk7XHJcbnZhciBXUyA9IGdsb2JhbC53aW5kb3cuV1M7XHJcblxyXG4vKipcclxuICogRGVmYXVsdCBvcHRpb25zLlxyXG4gKlxyXG4gKiBAdHlwZSB7e2NvbGxhcHNlZENsYXNzOiBzdHJpbmcsIG9wZW5DbGFzczogc3RyaW5nLCBwb3BvdXRDbGFzczogc3RyaW5nfX1cclxuICovXHJcbnZhciBkZWZhdWx0cyA9IHtcclxuICBjb2xsYXBzZWRDbGFzczogJ2NvbGxhcHNlZCcsXHJcbiAgb3BlbkNsYXNzOiAnb3BlbicsXHJcbiAgcG9wb3V0Q2xhc3M6ICdwb3BvdXQnXHJcbn07XHJcblxyXG4vKipcclxuICogR2V0cyB0aGUgbGlzdCBlbGVtZW50IG9mIHRoZSBjdXJyZW50IGV2ZW50IHRhcmdldC5cclxuICpcclxuICogQHBhcmFtIHtPYmplY3R9IGV2ZW50XHJcbiAqIEBwYXJhbSB7c3RyaW5nPX0gY2xzXHJcbiAqIEByZXR1cm5zIHtqUXVlcnl9XHJcbiAqL1xyXG52YXIgX2dldEV2ZW50VGFyZ2V0ID0gZnVuY3Rpb24oZXZlbnQsIGNscykge1xyXG4gIHZhciAkdGFyZ2V0ID0gJChldmVudC50YXJnZXQpO1xyXG4gIGlmICghJHRhcmdldC5pcygnbGknKSkge1xyXG4gICAgY2xzID0gY2xzIHx8ICcnO1xyXG4gICAgcmV0dXJuICR0YXJnZXQuY2xvc2VzdCgnbGknICsgY2xzKTtcclxuICB9XHJcblxyXG4gIHJldHVybiAkdGFyZ2V0O1xyXG59O1xyXG5cclxubW9kdWxlLmV4cG9ydHMgPSBCYXNlVmlldy5leHRlbmQoe1xyXG5cclxuICAvKipcclxuICAgKiBUaGUgZWxlbWVudCBvZiB0aGlzIHZpZXcuXHJcbiAgICpcclxuICAgKiBAdHlwZSB7c3RyaW5nfSBDU1Mgc2VsZWN0b3JcclxuICAgKi9cclxuICBlbDogJyNiYWNrZW5kLW1lbnUgPiB1bCcsXHJcblxyXG4gIC8qKlxyXG4gICAqIFJlZ2lzdGVyZWQgZXZlbnRzIG9mIHRoaXMgdmlldy5cclxuICAgKlxyXG4gICAqIEByZXR1cm5zIHtPYmplY3R9XHJcbiAgICovXHJcbiAgZXZlbnRzOiBmdW5jdGlvbigpIHtcclxuICAgIHZhciBldmVudHMgPSB7XHJcbiAgICAgICdjbGljayA+IGxpOmhhcyh1bCkgPiBhJzogJ29uTWFpbkl0ZW1DbGljaydcclxuICAgIH07XHJcblxyXG4gICAgaWYgKHRoaXMuaXNDb2xsYXBzZWQpIHtcclxuICAgICAgZXZlbnRzID0gJC5leHRlbmQoZXZlbnRzLCB7XHJcbiAgICAgICAgJ21vdXNlZW50ZXIgPiBsaSc6ICdvbk1haW5JdGVtTW91c2VlbnRlcicsXHJcbiAgICAgICAgJ21vdXNlbGVhdmUgPiBsaSc6ICdvbk1haW5JdGVtTW91c2VsZWF2ZSdcclxuICAgICAgfSk7XHJcbiAgICB9XHJcblxyXG4gICAgcmV0dXJuIGV2ZW50cztcclxuICB9LFxyXG5cclxuICBnbG9iYWxFdmVudHM6IHtcclxuICAgICd3aW5kb3cucmVzaXplJzogJ2NvbGxhcHNlTWVudSdcclxuICB9LFxyXG5cclxuICAvKipcclxuICAgKiBEZXRlcm1pbmVzIGlmIHRoZSBuYXZpZ2F0aW9uIGJhciBpcyBjb2xsYXBzZWQgdG8gdGhlIHNtYWxsIHNpemUgKHRydWUpIG9yIG5vdCAoZmFsc2UpLlxyXG4gICAqXHJcbiAgICogQHR5cGUge2Jvb2xlYW59XHJcbiAgICovXHJcbiAgaXNDb2xsYXBzZWQ6IGZhbHNlLFxyXG5cclxuICAvKipcclxuICAgKiBPcHRpb25zXHJcbiAgICpcclxuICAgKiBAcGFyYW0ge09iamVjdH1cclxuICAgKi9cclxuICBvcHRpb25zOiB7fSxcclxuXHJcbiAgLyoqXHJcbiAgICogSW5pdGlhbGl6YXRpb24gb2YgdGhlIHZpZXcuXHJcbiAgICpcclxuICAgKiBAcGFyYW0ge09iamVjdD19IG9wdGlvbnNcclxuICAgKi9cclxuICBpbml0aWFsaXplOiBmdW5jdGlvbihvcHRpb25zKSB7XHJcbiAgICB0aGlzLm9wdGlvbnMgPSAkLmV4dGVuZCh7fSwgZGVmYXVsdHMsIG9wdGlvbnMpO1xyXG4gICAgdGhpcy4kKCc+IGxpJykuZmlsdGVyKCcuYWN0aXZlJykucHJldigpLmFkZENsYXNzKCdwcmV2LWFjdGl2ZScpO1xyXG4gICAgdGhpcy5jb2xsYXBzZU1lbnUoKTtcclxuICAgIC8vaWYgKCFXUy5mZWF0dXJlcy5oYXNUb3VjaCkge1xyXG4gICAgLy8gIHRoaXMuJGVsLnBhcmVudCgpLnBlcmZlY3RTY3JvbGxiYXIoKTtcclxuICAgIC8vfVxyXG4gICAgLy90aGlzLmxpc3RlblRvKGV2ZW50aWZ5KHdpbmRvdyksICdyZXNpemUnLCBfLmJpbmQodGhpcy5vblJlc2l6ZSwgdGhpcykpO1xyXG4gIH0sXHJcblxyXG4gIC8qKlxyXG4gICAqIEF0dGFjaGVzL2RldGFjaGVzIGV2ZW50IGhhbmRsZXJzIGlmIHRoZSBuYXZpZ2F0aW9uIGlzIGNvbGxhcHNlZFxyXG4gICAqIGFuZCB0b2dnbGVzIHRoZSAuY29sbGFwc2VkIGNsYXNzLlxyXG4gICAqXHJcbiAgICogVGhlIHZpc3VhbHMgb2YgdGhlIGNvbGxhcHNlZCBuYXZpZ2F0aW9uIGFyZSBkb25lIHZpYSBtZWRpYSBxdWVyaWVzIGFuZCBub3QgdmlhIEpTLlxyXG4gICAqL1xyXG4gIGNvbGxhcHNlTWVudTogZnVuY3Rpb24oKSB7XHJcbiAgICB2YXIgcHJldkNvbGxhcHNlZCA9IHRoaXMuaXNDb2xsYXBzZWQ7XHJcbiAgICB0aGlzLmlzQ29sbGFwc2VkID0gKHRoaXMuJCgnPiBsaSA+IGEgPiAuaXRlbS1uYW1lJykuZmlyc3QoKS5jc3MoJ2Rpc3BsYXknKSA9PT0gJ25vbmUnKTtcclxuICAgIGlmICh0aGlzLmlzQ29sbGFwc2VkKSB7XHJcbiAgICAgIHRoaXMuJGVsLmFkZENsYXNzKHRoaXMub3B0aW9ucy5jb2xsYXBzZWRDbGFzcyk7XHJcbiAgICAgIGlmICh0aGlzLmlzQ29sbGFwc2VkICE9PSBwcmV2Q29sbGFwc2VkKSB7XHJcbiAgICAgICAgdGhpcy5kZWxlZ2F0ZUV2ZW50cyh0aGlzLmV2ZW50cygpKTtcclxuICAgICAgfVxyXG4gICAgfSBlbHNlIHtcclxuICAgICAgdGhpcy4kZWwucmVtb3ZlQ2xhc3ModGhpcy5vcHRpb25zLmNvbGxhcHNlZENsYXNzKTtcclxuICAgICAgdGhpcy5kZWxlZ2F0ZUV2ZW50cyh0aGlzLmV2ZW50cygpKTtcclxuICAgIH1cclxuICB9LFxyXG5cclxuICAvKipcclxuICAgKiBvbk1haW5JdGVtQ2xpY2sgZXZlbnQgaGFuZGxlclxyXG4gICAqIFRvZ2dsZXMgdGhlIC5vcGVuIGNsYXNzIG9uIGV2ZXJ5IGxpIHRoYXQgaGFzIGEgY2hpbGQgdWwuXHJcbiAgICpcclxuICAgKiBAcGFyYW0ge09iamVjdH0gZXZlbnRcclxuICAgKi9cclxuICBvbk1haW5JdGVtQ2xpY2s6IGZ1bmN0aW9uKGV2ZW50KSB7XHJcbiAgICBfZ2V0RXZlbnRUYXJnZXQoZXZlbnQpLnRvZ2dsZUNsYXNzKHRoaXMub3B0aW9ucy5vcGVuQ2xhc3MpO1xyXG4gIH0sXHJcblxyXG4gIC8qKlxyXG4gICAqIG9uTWFpbkl0ZW1Nb3VzZWVudGVyIGV2ZW50IGhhbmRsZXJcclxuICAgKiBBZGQgLnBvcG91dCBjbGFzcyB0byBob3ZlcmVkIGxpIGlmIHRoZSBtZW51IGlzIGNvbGxhcHNlZC5cclxuICAgKlxyXG4gICAqIEBwYXJhbSB7T2JqZWN0fSBldmVudFxyXG4gICAqL1xyXG4gIG9uTWFpbkl0ZW1Nb3VzZWVudGVyOiBmdW5jdGlvbihldmVudCkge1xyXG4gICAgaWYgKCF0aGlzLmlzQ29sbGFwc2VkKSByZXR1cm47XHJcbiAgICBfZ2V0RXZlbnRUYXJnZXQoZXZlbnQpLmFkZENsYXNzKHRoaXMub3B0aW9ucy5wb3BvdXRDbGFzcyk7XHJcbiAgfSxcclxuXHJcbiAgLyoqXHJcbiAgICogb25NYWluSXRlbU1vdXNlTGVhdmUgZXZlbnQgaGFuZGxlclxyXG4gICAqIFJlbW92ZSAucG9wb3V0IGNsYXNzIGZyb20gdGhlIHByZXZpb3VzbHkgaG92ZXJlZCBsaSBpZiB0aGUgbWVudSBpcyBjb2xsYXBzZWQuXHJcbiAgICpcclxuICAgKiBAcGFyYW0ge09iamVjdH0gZXZlbnRcclxuICAgKi9cclxuICBvbk1haW5JdGVtTW91c2VsZWF2ZTogZnVuY3Rpb24oZXZlbnQpIHtcclxuICAgIGlmICghdGhpcy5pc0NvbGxhcHNlZCkgcmV0dXJuO1xyXG4gICAgX2dldEV2ZW50VGFyZ2V0KGV2ZW50LCAnLicgKyB0aGlzLm9wdGlvbnMucG9wb3V0Q2xhc3MpLnJlbW92ZUNsYXNzKHRoaXMub3B0aW9ucy5wb3BvdXRDbGFzcyk7XHJcbiAgfSxcclxuXHJcbiAgLyoqXHJcbiAgICogb25XaW5kb3dSZXNpemUgZXZlbnQgaGFuZGxlclxyXG4gICAqIENhbGxzIGNvbGxhcHNlTWVudSBvbiB3aW5kb3cgcmVzaXplLlxyXG4gICAqL1xyXG4gIG9uUmVzaXplOiBmdW5jdGlvbigpIHtcclxuICAgIGNsZWFyVGltZW91dCh0aGlzLnJlc2l6ZVRpbWVvdXQpO1xyXG4gICAgdGhpcy5yZXNpemVUaW1lb3V0ID0gc2V0VGltZW91dChfLmJpbmQodGhpcy5jb2xsYXBzZU1lbnUsIHRoaXMpLCAxMDApO1xyXG4gIH1cclxuXHJcbn0pO1xyXG4iLCJ2YXIgJCA9IHJlcXVpcmUoJ2pxdWVyeScpO1xyXG52YXIgQmFzZVZpZXcgPSByZXF1aXJlKCd3YXNhYmkuQmFzZVZpZXcnKTtcclxuXHJcbm1vZHVsZS5leHBvcnRzID0gKGZ1bmN0aW9uKCkge1xyXG5cclxuICAvKipcclxuICAgKiBIb2xkcyBhIHJlZmVyZW5jZSB0byB0aGUgYm9keS5cclxuICAgKlxyXG4gICAqIEB0eXBlIHtqUXVlcnl9XHJcbiAgICovXHJcbiAgdmFyICRib2R5ID0gJCgnYm9keScpO1xyXG5cclxuICAvKipcclxuICAgKiBEZWZhdWx0IG9wdGlvbnMuXHJcbiAgICpcclxuICAgKiBAdHlwZSB7e25hdkNsb3NlZENsYXNzOiBzdHJpbmd9fVxyXG4gICAqL1xyXG4gIHZhciBkZWZhdWx0cyA9IHtcclxuICAgIG5hdkNsb3NlZENsYXNzOiAnbmF2LWNsb3NlZCdcclxuICB9O1xyXG5cclxuICByZXR1cm4gQmFzZVZpZXcuZXh0ZW5kKHtcclxuXHJcbiAgICAvKipcclxuICAgICAqIFRoZSBlbGVtZW50IG9mIHRoaXMgdmlldy5cclxuICAgICAqXHJcbiAgICAgKiBAdHlwZSB7c3RyaW5nfSBDU1Mgc2VsZWN0b3JcclxuICAgICAqL1xyXG4gICAgZWw6ICdib2R5ID4gaGVhZGVyIC50b2dnbGUtbmF2JyxcclxuXHJcbiAgICAvKipcclxuICAgICAqIFJlZ2lzdGVyZWQgZXZlbnRzIG9mIHRoaXMgdmlldy5cclxuICAgICAqXHJcbiAgICAgKiBAdHlwZSB7T2JqZWN0fVxyXG4gICAgICovXHJcbiAgICBldmVudHM6IHtcclxuICAgICAgJ2NsaWNrJzogJ3RvZ2dsZU5hdidcclxuICAgIH0sXHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBEZXRlcm1pbmVzIGlmIHRoZSBuYXZpZ2F0aW9uIGJhciBpcyBjb2xsYXBzZWQgdG8gdGhlIHNtYWxsIHNpemUgKHRydWUpIG9yIG5vdCAoZmFsc2UpLlxyXG4gICAgICpcclxuICAgICAqIEB0eXBlIHtib29sZWFufVxyXG4gICAgICovXHJcbiAgICBpc0Nsb3NlZDogZmFsc2UsXHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBPcHRpb25zXHJcbiAgICAgKlxyXG4gICAgICogQHBhcmFtIHtPYmplY3R9XHJcbiAgICAgKi9cclxuICAgIG9wdGlvbnM6IHt9LFxyXG5cclxuICAgIC8qKlxyXG4gICAgICogSW5pdGlhbGl6YXRpb24gb2YgdGhlIHZpZXcuXHJcbiAgICAgKlxyXG4gICAgICogQHBhcmFtIHtPYmplY3Q9fSBvcHRpb25zXHJcbiAgICAgKi9cclxuICAgIGluaXRpYWxpemU6IGZ1bmN0aW9uKG9wdGlvbnMpIHtcclxuICAgICAgdGhpcy5vcHRpb25zID0gJC5leHRlbmQoe30sIGRlZmF1bHRzLCBvcHRpb25zKTtcclxuICAgICAgdGhpcy5pc0Nsb3NlZCA9ICRib2R5Lmhhc0NsYXNzKHRoaXMub3B0aW9ucy5uYXZDbG9zZWRDbGFzcyk7XHJcbiAgICB9LFxyXG5cclxuICAgIC8qKlxyXG4gICAgICogdG9nZ2xlTmF2IGV2ZW50IGhhbmRsZXJcclxuICAgICAqIFRvZ2dsZXMgbmF2Q2xvc2VkQ2xhc3Mgb24gdGhlIGJvZHkgdG8gc2hvdy9oaWRlIHRoZSBuYXZpZ2F0aW9uLlxyXG4gICAgICovXHJcbiAgICB0b2dnbGVOYXY6IGZ1bmN0aW9uKCkge1xyXG4gICAgICBpZiAoIXRoaXMuY2xvc2VkKSB7XHJcbiAgICAgICAgJGJvZHkuYWRkQ2xhc3ModGhpcy5vcHRpb25zLm5hdkNsb3NlZENsYXNzKTtcclxuICAgICAgICB0aGlzLmNsb3NlZCA9IHRydWU7XHJcbiAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgJGJvZHkucmVtb3ZlQ2xhc3ModGhpcy5vcHRpb25zLm5hdkNsb3NlZENsYXNzKTtcclxuICAgICAgICB0aGlzLmNsb3NlZCA9IGZhbHNlO1xyXG4gICAgICB9XHJcbiAgICB9XHJcbiAgfSk7XHJcblxyXG59KSgpO1xyXG4iLCJ2YXIgJCA9IHJlcXVpcmUoJ2pxdWVyeScpO1xyXG53aW5kb3cualF1ZXJ5ID0gJDtcclxudmFyIF8gPSByZXF1aXJlKCd1bmRlcnNjb3JlJyk7XHJcbnZhciBCYWNrYm9uZSA9IHJlcXVpcmUoJ2JhY2tib25lJyk7XHJcbnZhciBCYXNlVmlld0ZhY3RvcnkgPSByZXF1aXJlKCcuL2NvbW1vbi9CYXNlVmlld0ZhY3RvcnknKTtcclxudmFyIGJzID0gcmVxdWlyZSgnYm9vdHN0cmFwJyk7XHJcbi8vdmFyIENvcmVSb3V0ZXIgPSByZXF1aXJlKCcuL3JvdXRlci9Db3JlUm91dGVyJyk7XHJcblxyXG52YXIgV1MgPSAoZnVuY3Rpb24oKSB7XHJcblxyXG4gIHZhciBldmVudEJ1cyA9IF8uZXh0ZW5kKHt9LCBCYWNrYm9uZS5FdmVudHMpO1xyXG4gIHZhciB2aWV3RmFjdG9yeSA9IG5ldyBCYXNlVmlld0ZhY3RvcnkoZXZlbnRCdXMpO1xyXG4gIHZhciByZXNpemVUaW1lb3V0ID0gMDtcclxuXHJcbiAgcmV0dXJuIHtcclxuICAgIGZlYXR1cmVzOiB7fSxcclxuXHJcbiAgICBtb2R1bGVzOiB7XHJcbiAgICAgIHJlZ2lzdGVyZWQ6IFtdXHJcbiAgICB9LFxyXG5cclxuICAgIC8qKlxyXG4gICAgICogR2xvYmFsIGV2ZW50IGJ1cy5cclxuICAgICAqXHJcbiAgICAgKiBAdHlwZSB7QmFja2JvbmUuRXZlbnRzfVxyXG4gICAgICovXHJcbiAgICBldmVudEJ1czogZXZlbnRCdXMsXHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBHbG9iYWwgdmlldyBmYWN0b3J5IHRvIGluc3RhbnRpYXRlIG5ldyB2aWV3cy5cclxuICAgICAqXHJcbiAgICAgKiBAdHlwZSB7QmFzZVZpZXdGYWN0b3J5fVxyXG4gICAgICovXHJcbiAgICB2aWV3RmFjdG9yeTogdmlld0ZhY3RvcnksXHJcblxyXG4gICAgYm9vdDogZnVuY3Rpb24gKCkge1xyXG4gICAgICAvL25ldyBDb3JlUm91dGVyKG9wdGlvbnMuYmFzZVVybCk7XHJcblxyXG4gICAgICBmb3IgKHZhciBpID0gMCwgbGVuID0gdGhpcy5tb2R1bGVzLnJlZ2lzdGVyZWQubGVuZ3RoOyBpIDwgbGVuOyBpKyspIHtcclxuICAgICAgICB2YXIgcmVnaXN0ZXJlZCA9IHRoaXMubW9kdWxlcy5yZWdpc3RlcmVkW2ldO1xyXG4gICAgICAgIHZhciBuYW1lID0gcmVnaXN0ZXJlZC5uYW1lO1xyXG4gICAgICAgIHZhciByID0gcmVnaXN0ZXJlZC5yZXF1aXJlO1xyXG4gICAgICAgIHZhciBvcHRpb25zID0gcmVnaXN0ZXJlZC5vcHRpb25zO1xyXG4gICAgICAgIHRoaXMubW9kdWxlc1tuYW1lXSA9IHJlcXVpcmUocik7XHJcbiAgICAgICAgY29uc29sZS5sb2codGhpcy5tb2R1bGVzKTtcclxuICAgICAgICB0aGlzLm1vZHVsZXNbbmFtZV0uaW5pdGlhbGl6ZShvcHRpb25zKTtcclxuICAgICAgfVxyXG5cclxuICAgICAgJCh3aW5kb3cpLm9uKCdyZXNpemUnLCBmdW5jdGlvbigpIHtcclxuICAgICAgICBjbGVhclRpbWVvdXQocmVzaXplVGltZW91dCk7XHJcbiAgICAgICAgcmVzaXplVGltZW91dCA9IHNldFRpbWVvdXQoXy5iaW5kKGZ1bmN0aW9uKCkge1xyXG4gICAgICAgICAgdGhpcy5ldmVudEJ1cy50cmlnZ2VyKCd3aW5kb3cucmVzaXplJyk7XHJcbiAgICAgICAgfSwgdGhpcyksIDEwMCk7XHJcbiAgICAgIH0pO1xyXG4gICAgfSxcclxuXHJcbiAgICByZWdpc3Rlck1vZHVsZTogZnVuY3Rpb24gKG5hbWUsIG9wdGlvbnMpIHtcclxuICAgICAgdGhpcy5tb2R1bGVzLnJlZ2lzdGVyZWQucHVzaCh7XHJcbiAgICAgICAgbmFtZTogbmFtZSxcclxuICAgICAgICByZXF1aXJlOiBuYW1lLFxyXG4gICAgICAgIG9wdGlvbnM6IG9wdGlvbnMgfHwge31cclxuICAgICAgfSk7XHJcbiAgICB9LFxyXG5cclxuICAgIGdldDogZnVuY3Rpb24gKG5hbWUpIHtcclxuICAgICAgaWYgKHRoaXMubW9kdWxlc1tuYW1lXSkge1xyXG4gICAgICAgIHJldHVybiB0aGlzLm1vZHVsZXNbbmFtZV07XHJcbiAgICAgIH1cclxuICAgICAgY29uc29sZS5kZWJ1ZygnbW9kdWxlIFwiJyArIG5hbWUgKyBcIiBpcyBub3QgcmVnaXN0ZXJlZC5cIik7XHJcbiAgICB9XHJcbiAgfTtcclxuXHJcbn0pKCk7XHJcblxyXG5nbG9iYWwud2luZG93LldTID0gV1M7XHJcbiIsInZhciAkID0gcmVxdWlyZSgnanF1ZXJ5Jyk7XHJcbnZhciBfID0gcmVxdWlyZSgndW5kZXJzY29yZScpO1xyXG52YXIgQmFja2JvbmUgPSByZXF1aXJlKCdiYWNrYm9uZScpO1xyXG52YXIgU3Bpbm5lciA9IHJlcXVpcmUoJ3NwaW4uanMnKTtcclxuXHJcbkJhY2tib25lLiQgPSAkO1xyXG5cclxuKGZ1bmN0aW9uKFZpZXcpe1xyXG4gICd1c2Ugc3RyaWN0JztcclxuICAvLyBBZGRpdGlvbmFsIGV4dGVuc2lvbiBsYXllciBmb3IgVmlld3NcclxuICBWaWV3LmZ1bGxFeHRlbmQgPSBmdW5jdGlvbiAocHJvdG9Qcm9wcywgc3RhdGljUHJvcHMpIHtcclxuICAgIC8vIENhbGwgZGVmYXVsdCBleHRlbmQgbWV0aG9kXHJcbiAgICB2YXIgZXh0ZW5kZWQgPSBWaWV3LmV4dGVuZC5jYWxsKHRoaXMsIHByb3RvUHJvcHMsIHN0YXRpY1Byb3BzKTtcclxuICAgIC8vIEFkZCBhIHVzYWJsZSBzdXBlciBtZXRob2QgZm9yIGJldHRlciBpbmhlcml0YW5jZVxyXG4gICAgZXh0ZW5kZWQucHJvdG90eXBlLl9zdXBlciA9IHRoaXMucHJvdG90eXBlO1xyXG4gICAgLy8gQXBwbHkgbmV3IG9yIGRpZmZlcmVudCBldmVudHMgb24gdG9wIG9mIHRoZSBvcmlnaW5hbFxyXG4gICAgaWYgKHByb3RvUHJvcHMuZXZlbnRzKSB7XHJcbiAgICAgIGZvciAodmFyIGsgaW4gdGhpcy5wcm90b3R5cGUuZXZlbnRzKSB7XHJcbiAgICAgICAgaWYgKCFleHRlbmRlZC5wcm90b3R5cGUuZXZlbnRzW2tdKSB7XHJcbiAgICAgICAgICBleHRlbmRlZC5wcm90b3R5cGUuZXZlbnRzW2tdID0gdGhpcy5wcm90b3R5cGUuZXZlbnRzW2tdO1xyXG4gICAgICAgIH1cclxuICAgICAgfVxyXG4gICAgfVxyXG4gICAgcmV0dXJuIGV4dGVuZGVkO1xyXG4gIH07XHJcblxyXG59KShCYWNrYm9uZS5WaWV3KTtcclxuXHJcbm1vZHVsZS5leHBvcnRzID0gQmFja2JvbmUuVmlldy5leHRlbmQoe1xyXG5cclxuICAkYmxvY2tCYWNrZHJvcDogJCgnPGRpdiBjbGFzcz1cImJsb2NrLWJhY2tkcm9wXCI+PC9kaXY+JyksXHJcbiAgbnVtYmVyT2ZCbG9ja3M6IDAsXHJcbiAgc3Bpbm5lcjogbnVsbCxcclxuXHJcbiAgZGVsZWdhdGVFdmVudHM6IGZ1bmN0aW9uKGV2ZW50cykge1xyXG4gICAgdmFyIGV2ZW50LCBoYW5kbGVyLCByZXN1bHRzO1xyXG5cclxuICAgIEJhY2tib25lLlZpZXcucHJvdG90eXBlLmRlbGVnYXRlRXZlbnRzLmNhbGwodGhpcywgZXZlbnRzKTtcclxuXHJcbiAgICB0aGlzLmdsb2JhbEV2ZW50cyA9IHRoaXMuZ2xvYmFsRXZlbnRzIHx8IHt9O1xyXG5cclxuICAgIGlmICh0eXBlb2YgdGhpcy5nbG9iYWxFdmVudHMgPT09ICdmdW5jdGlvbicpIHtcclxuICAgICAgdGhpcy5nbG9iYWxFdmVudHMgPSB0aGlzLmdsb2JhbEV2ZW50cygpO1xyXG4gICAgfVxyXG5cclxuICAgIHJlc3VsdHMgPSBbXTtcclxuXHJcbiAgICBmb3IgKGV2ZW50IGluIHRoaXMuZ2xvYmFsRXZlbnRzKSB7XHJcbiAgICAgIGlmICghdGhpcy5nbG9iYWxFdmVudHMuaGFzT3duUHJvcGVydHkoZXZlbnQpKSB7XHJcbiAgICAgICAgY29udGludWU7XHJcbiAgICAgIH1cclxuICAgICAgaGFuZGxlciA9IHRoaXMuZ2xvYmFsRXZlbnRzW2V2ZW50XTtcclxuICAgICAgcmVzdWx0cy5wdXNoKHRoaXMuZXZlbnRCdXMuYmluZChldmVudCwgXy5iaW5kKHRoaXNbaGFuZGxlcl0sIHRoaXMpKSk7XHJcbiAgICB9XHJcblxyXG4gICAgcmV0dXJuIHJlc3VsdHM7XHJcbiAgfSxcclxuXHJcbiAgLyoqXHJcbiAgICogQmxvY2sgYSB2aWV3IGJ5OlxyXG4gICAqIC0gb3ZlcmxheWluZyBhIGRpdiB3aXRoIGEgc3BlY2lmaWVkIGNvbG9yIGFuZCBvcGFjaXR5XHJcbiAgICogLSBzaG93aW5nIGEgc3Bpbm5lciBpbiB0aGUgY2VudGVyIG9mIHRoaXMgZGl2XHJcbiAgICogLSBjYXRjaGluZyBhbGwgdXNlciBnZW5lcmF0ZWQgZXZlbnRzIG9uIHRoZSBibG9ja2VkIGVsZW1lbnRcclxuICAgKlxyXG4gICAqIEBwYXJhbSBvcHRpb25zXHJcbiAgICovXHJcbiAgYmxvY2tUaGlzOiBmdW5jdGlvbihvcHRpb25zKSB7XHJcbiAgICB0aGlzLm51bWJlck9mQmxvY2tzKys7XHJcbiAgICB2YXIgb2Zmc2V0ID0gdGhpcy4kZWwub2Zmc2V0KCk7XHJcbiAgICB2YXIgYm9yZGVyTGVmdCA9IHRoaXMuJGVsLmNzcygnYm9yZGVyTGVmdFdpZHRoJyk7XHJcbiAgICB2YXIgYm9yZGVyVG9wID0gdGhpcy4kZWwuY3NzKCdib3JkZXJUb3BXaWR0aCcpO1xyXG4gICAgdmFyIHdpZHRoID0gdGhpcy4kZWwuaW5uZXJXaWR0aCgpICsgKG9wdGlvbnMuZGVsdGFXaWR0aCB8fCAwKTtcclxuICAgIHZhciBoZWlnaHQgPSB0aGlzLiRlbC5pbm5lckhlaWdodCgpICsgKG9wdGlvbnMuZGVsdGFIZWlnaHQgfHwgMCk7XHJcblxyXG4gICAgaWYgKGJvcmRlckxlZnQgIT09ICcnKSB7XHJcbiAgICAgIGJvcmRlckxlZnQgPSBwYXJzZUludChib3JkZXJMZWZ0LnNwbGl0KCdweCcpWzBdKTtcclxuICAgICAgb2Zmc2V0LmxlZnQgKz0gYm9yZGVyTGVmdDtcclxuICAgIH1cclxuXHJcbiAgICBpZiAoYm9yZGVyVG9wICE9PSAnJykge1xyXG4gICAgICBib3JkZXJUb3AgPSBwYXJzZUludChib3JkZXJUb3Auc3BsaXQoJ3B4JylbMF0pO1xyXG4gICAgICBvZmZzZXQudG9wICs9IGJvcmRlclRvcDtcclxuICAgIH1cclxuXHJcbiAgICB0aGlzLiRibG9ja0JhY2tkcm9wLmNzcyh7XHJcbiAgICAgIHBvc2l0aW9uOiAnYWJzb2x1dGUnLFxyXG4gICAgICB0b3A6IG9mZnNldC50b3AsXHJcbiAgICAgIGxlZnQ6IG9mZnNldC5sZWZ0LFxyXG4gICAgICB3aWR0aDogd2lkdGgsXHJcbiAgICAgIGhlaWdodDogaGVpZ2h0LFxyXG4gICAgICBiYWNrZ3JvdW5kQ29sb3I6IG9wdGlvbnMuYmFja2dyb3VuZENvbG9yIHx8ICd0cmFuc3BhcmVudCcsXHJcbiAgICAgIG9wYWNpdHk6IG9wdGlvbnMub3BhY2l0eSB8fCAwLjYsXHJcbiAgICAgIGN1cnNvcjogb3B0aW9ucy5jdXJzb3IgfHwgJ3dhaXQnLFxyXG4gICAgICB6SW5kZXg6IG9wdGlvbnMuekluZGV4IHx8IDk5OTdcclxuICAgIH0pLmhpZGUoKS5hcHBlbmRUbygkKCdib2R5JykpO1xyXG5cclxuICAgIHRoaXMuJGJsb2NrQmFja2Ryb3AuZmFkZUluKDEwMCwgJC5wcm94eShmdW5jdGlvbigpIHtcclxuICAgICAgdGhpcy4kYmxvY2tCYWNrZHJvcC5vbignbW91c2Vkb3duIG1vdXNldXAga2V5ZG93biBrZXlwcmVzcyBrZXl1cCB0b3VjaHN0YXJ0IHRvdWNoZW5kIHRvdWNobW92ZScsIF8uYmluZCh0aGlzLmhhbmRsZUJsb2NrZWRFdmVudCwgdGhpcykpO1xyXG4gICAgICBpZiAob3B0aW9ucy5zcGlubmVyICYmIG9wdGlvbnMuc3Bpbm5lciAhPT0gZmFsc2UpIHtcclxuICAgICAgICB0aGlzLnNwaW5uZXIgPSB0aGlzLnNwaW5uZXIgfHwgbmV3IFNwaW5uZXIob3B0aW9ucy5zcGlubmVyIHx8IGZhbHNlKTtcclxuICAgICAgfVxyXG4gICAgICBpZiAodGhpcy5zcGlubmVyKSB7XHJcbiAgICAgICAgdGhpcy5zcGlubmVyLnNwaW4odGhpcy4kYmxvY2tCYWNrZHJvcC5nZXQoMCkpO1xyXG4gICAgICB9XHJcbiAgICB9LCB0aGlzKSk7XHJcbiAgfSxcclxuXHJcbiAgLyoqXHJcbiAgICogVW5ibG9jayBhIHZpZXcuXHJcbiAgICpcclxuICAgKiBAcGFyYW0gY2FsbGJhY2tcclxuICAgKi9cclxuICB1bmJsb2NrVGhpczogZnVuY3Rpb24oY2FsbGJhY2spIHtcclxuICAgIGlmICh0aGlzLm51bWJlck9mQmxvY2tzID49IDEpIHtcclxuICAgICAgdGhpcy5udW1iZXJPZkJsb2Nrcy0tO1xyXG4gICAgfVxyXG4gICAgaWYgKHRoaXMubnVtYmVyT2ZCbG9ja3MgPT09IDApIHtcclxuICAgICAgY2FsbGJhY2sgPSBjYWxsYmFjayB8fCBmdW5jdGlvbigpIHt9O1xyXG4gICAgICBpZiAodGhpcy5zcGlubmVyKSB7XHJcbiAgICAgICAgdGhpcy5zcGlubmVyLnN0b3AoKTtcclxuICAgICAgfVxyXG4gICAgICB0aGlzLiRibG9ja0JhY2tkcm9wLnJlbW92ZSgpO1xyXG4gICAgICB0aGlzLiRibG9ja0JhY2tkcm9wLm9mZignbW91c2Vkb3duIG1vdXNldXAga2V5ZG93biBrZXlwcmVzcyBrZXl1cCB0b3VjaHN0YXJ0IHRvdWNoZW5kIHRvdWNobW92ZScsIF8uYmluZCh0aGlzLmhhbmRsZUJsb2NrZWRFdmVudCwgdGhpcykpO1xyXG4gICAgICBpZiAodHlwZW9mIGNhbGxiYWNrID09PSAnZnVuY3Rpb24nKSB7XHJcbiAgICAgICAgY2FsbGJhY2suY2FsbCh0aGlzKTtcclxuICAgICAgfVxyXG4gICAgfVxyXG4gIH0sXHJcblxyXG4gIC8qKlxyXG4gICAqIENhdGNoIGFsbCBldmVudCBoYW5kbGVyIGZvciBibG9ja2VkIHZpZXdzLlxyXG4gICAqXHJcbiAgICogQHBhcmFtIGV2ZW50XHJcbiAgICovXHJcbiAgaGFuZGxlQmxvY2tlZEV2ZW50OiBmdW5jdGlvbihldmVudCkge1xyXG4gICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcclxuICAgIGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xyXG4gIH0sXHJcblxyXG4gIGRlc3Ryb3k6IGZ1bmN0aW9uKCkge1xyXG4gICAgLy8gQ09NUExFVEVMWSBVTkJJTkQgVEhFIFZJRVdcclxuICAgIHRoaXMudW5kZWxlZ2F0ZUV2ZW50cygpO1xyXG5cclxuICAgIHRoaXMuJGVsLnJlbW92ZURhdGEoKS51bmJpbmQoKTtcclxuXHJcbiAgICAvLyBSZW1vdmUgdmlldyBmcm9tIERPTVxyXG4gICAgdGhpcy5yZW1vdmUoKTtcclxuICAgIEJhY2tib25lLlZpZXcucHJvdG90eXBlLnJlbW92ZS5jYWxsKHRoaXMpO1xyXG4gIH1cclxuXHJcbn0pO1xyXG4iLCJtb2R1bGUuZXhwb3J0cyA9IHtcclxuICBzbWFsbDogIHsgbGluZXM6IDEyLCBsZW5ndGg6IDAsIHdpZHRoOiAzLCByYWRpdXM6IDYgfSxcclxuICBtZWRpdW06IHsgbGluZXM6IDkgLCBsZW5ndGg6IDQsIHdpZHRoOiAyLCByYWRpdXM6IDMgfSxcclxuICBsYXJnZTogIHsgbGluZXM6IDExLCBsZW5ndGg6IDcsIHdpZHRoOiAyLCByYWRpdXM6IDUgfSxcclxuICBtZWdhOiAgIHsgbGluZXM6IDExLCBsZW5ndGg6IDEwLCB3aWR0aDogMywgcmFkaXVzOiA4fVxyXG59O1xyXG4iLCJ2YXIgJCA9IHJlcXVpcmUoJ2pxdWVyeScpO1xyXG52YXIgXyA9IHJlcXVpcmUoJ3VuZGVyc2NvcmUnKTtcclxudmFyIGNOYXZpZ2F0aW9uID0gcmVxdWlyZSgnLi9jb21wb25lbnRzL25hdmlnYXRpb24vbmF2aWdhdGlvbicpO1xyXG52YXIgd2luID0gd2luZG93O1xyXG52YXIgZG9jID0gZG9jdW1lbnQ7XHJcblxyXG4vKipcclxuICogU2V0dXAgZGVmYXVsdCBhamF4IG9wdGlvbnMgYW5kIGdsb2JhbCBhamF4IGV2ZW50IGhhbmRsZXJzLlxyXG4gKlxyXG4gKiBAcHJpdmF0ZVxyXG4gKi9cclxuZnVuY3Rpb24gX3NldHVwQWpheCgpIHtcclxuICAkLmFqYXhTZXR1cCh7XHJcbiAgICBkYXRhVHlwZTogJ2pzb24nXHJcbiAgfSk7XHJcbiAgJChkb2MpXHJcbiAgICAuYWpheFN1Y2Nlc3MoX29uQWpheFN1Y2Nlc3MpXHJcbiAgICAuYWpheEVycm9yKF9vbkFqYXhFcnJvcik7XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBEZWZhdWx0IGFqYXggc3VjY2VzcyBoYW5kbGVyLlxyXG4gKiBEaXNwbGF5cyBhIGZsYXNoIG1lc3NhZ2UgaWYgdGhlIHJlc3BvbnNlIGNvbnRhaW5zXHJcbiAqIHtcclxuICAgICAqICAgJ3N0YXR1cyc6ICcuLi4nICMgdGhlIGNsYXNzIG9mIHRoZSBmbGFzaCBtZXNzYWdlXHJcbiAgICAgKiAgICdmbGFzaE1lc3NhZ2UnOiAnLi4uJyAjIHRoZSB0ZXh0IG9mIHRoZSBmbGFzaCBtZXNzYWdlXHJcbiAgICAgKiB9XHJcbiAqXHJcbiAqIEBwYXJhbSBldmVudFxyXG4gKiBAcGFyYW0geGhyXHJcbiAqIEBwcml2YXRlXHJcbiAqL1xyXG5mdW5jdGlvbiBfb25BamF4U3VjY2VzcyhldmVudCwgeGhyKSB7XHJcbiAgaWYgKHhoci5zdGF0dXMgPT0gMjAwICYmIHhoci5zdGF0dXNUZXh0ID09ICdPSycpIHtcclxuICAgIHZhciBkYXRhID0gJC5wYXJzZUpTT04oeGhyLnJlc3BvbnNlVGV4dCkgfHwge307XHJcblxyXG4gICAgaWYgKHR5cGVvZiBkYXRhLnN0YXR1cyAhPT0gJ3VuZGVmaW5lZCcpIHtcclxuICAgICAgaWYgKHR5cGVvZiBkYXRhLmZsYXNoICE9PSAndW5kZWZpbmVkJykge1xyXG4gICAgICAgIF9mbGFzaE1lc3NhZ2UoZGF0YS5mbGFzaCk7XHJcbiAgICAgIH1cclxuICAgICAgaWYgKHR5cGVvZiBkYXRhLnJlZGlyZWN0ICE9PSAndW5kZWZpbmVkJykge1xyXG4gICAgICAgIHdpbi5sb2NhdGlvbiA9IGRhdGEucmVkaXJlY3Q7XHJcbiAgICAgIH1cclxuICAgIH1cclxuICB9XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBEZWZhdWx0IGFqYXggZXJyb3IgaGFuZGxlci5cclxuICpcclxuICogQHBhcmFtIGV2ZW50XHJcbiAqIEBwYXJhbSB4aHJcclxuICogQHByaXZhdGVcclxuICovXHJcbmZ1bmN0aW9uIF9vbkFqYXhFcnJvcihldmVudCwgeGhyKSB7XHJcbiAgdmFyIGRhdGE7XHJcbiAgaWYgKHhoci5zdGF0dXMgPT0gNDAxKSB7XHJcbiAgICBkYXRhID0gJC5wYXJzZUpTT04oeGhyLnJlc3BvbnNlVGV4dCkgfHwge307XHJcbiAgICBpZiAodHlwZW9mIGRhdGEubmFtZSAhPT0gJ3VuZGVmaW5lZCcpIHtcclxuICAgICAgaWYgKGNvbmZpcm0oZGF0YS5uYW1lKSkge1xyXG4gICAgICAgIHdpbi5sb2NhdGlvbi5yZWxvYWQoKTtcclxuICAgICAgfSBlbHNlIHtcclxuICAgICAgICB3aW4ubG9jYXRpb24ucmVsb2FkKCk7XHJcbiAgICAgIH1cclxuICAgIH1cclxuICB9XHJcbiAgaWYgKHhoci5zdGF0dXMgPT0gNTAwKSB7XHJcbiAgICBkYXRhID0gJC5wYXJzZUpTT04oeGhyLnJlc3BvbnNlVGV4dCkgfHwge307XHJcbiAgICBpZiAodHlwZW9mIGRhdGEubmFtZSAhPT0gJ3VuZGVmaW5lZCcpIHtcclxuICAgICAgX2ZsYXNoTWVzc2FnZSh7XHJcbiAgICAgICAgYmVmb3JlOiAnZGl2LnRpdGxlLXBhZCcsXHJcbiAgICAgICAgY2xhc3M6ICdlcnJvcicsXHJcbiAgICAgICAgbWVzc2FnZTogZGF0YS5uYW1lXHJcbiAgICAgIH0pO1xyXG4gICAgfVxyXG4gIH1cclxufVxyXG5cclxuLyoqXHJcbiAqIFJlbmRlciBhIGZsYXNoIG1lc3NhZ2UgYWZ0ZXIgYSBzcGVjaWZpYyBlbGVtZW50LlxyXG4gKlxyXG4gKiBAcGFyYW0ge09iamVjdH0gZmxhc2ggVGhlIGZsYXNoIG9iamVjdCByZXR1cm5lZCBieSB0aGUgYWpheCByZXF1ZXN0LlxyXG4gKiBAcHJpdmF0ZVxyXG4gKi9cclxuZnVuY3Rpb24gX2ZsYXNoTWVzc2FnZShmbGFzaCkge1xyXG4gIHZhciBpbnNlcnRCZWZvcmUgPSBmbGFzaC5iZWZvcmUgfHwgZmFsc2U7XHJcbiAgdmFyIGluc2VydEFmdGVyID0gZmxhc2guYWZ0ZXIgfHwgZmFsc2U7XHJcbiAgdmFyIG1lc3NhZ2UgPSBmbGFzaC5tZXNzYWdlO1xyXG4gIHZhciBjbHMgPSAoZmxhc2guY2xhc3MpID8gJ2ZsYXNoLScgKyBmbGFzaC5jbGFzcyA6ICdmbGFzaC1zdWNjZXNzJztcclxuICB2YXIgJHRhcmdldCA9IGZhbHNlO1xyXG4gIHZhciAkZmxhc2ggPSAkKCc8ZGl2Lz4nKS5hZGRDbGFzcyhjbHMpLmh0bWwobWVzc2FnZSk7XHJcblxyXG4gIGlmIChpbnNlcnRCZWZvcmUpIHtcclxuICAgICR0YXJnZXQgPSAkKGluc2VydEJlZm9yZSk7XHJcbiAgICAkdGFyZ2V0LnByZXYoJy4nICsgY2xzKS5yZW1vdmUoKTtcclxuICAgICRmbGFzaC5pbnNlcnRCZWZvcmUoJHRhcmdldCk7XHJcbiAgfSBlbHNlIGlmIChpbnNlcnRBZnRlcikge1xyXG4gICAgJHRhcmdldCA9ICQoaW5zZXJ0QWZ0ZXIpO1xyXG4gICAgJHRhcmdldC5uZXh0KCcuJyArIGNscykucmVtb3ZlKCk7XHJcbiAgICAkZmxhc2guaW5zZXJ0QWZ0ZXIoJHRhcmdldCk7XHJcbiAgfVxyXG59XHJcblxyXG5mdW5jdGlvbiBfaW5pdGlhbGl6ZUJhY2tlbmRWaWV3cygpIHtcclxuICB0aGlzLmNvbXBvbmVudHMubmF2aWdhdGlvbiA9IG5ldyBjTmF2aWdhdGlvbigpO1xyXG59XHJcblxyXG4vKipcclxuICogV2FzYWJpIENvcmUgbW9kdWxlXHJcbiAqIEluaXRpYWxpemVzOlxyXG4gKiAgIC0gJC5hamF4IChjdXN0b20gaGFuZGxlcnMgdG8gY2hlY2sgbG9naW4gc3RhdHVzIGluIGFqYXggcmVzcG9uc2VzKVxyXG4gKiAgIC0gQmFja2VuZCBVSSBjb21wb25lbnRzIHRoYXQgYXJlIHVzZWQgb24gZXZlcnkgcGFnZVxyXG4gKiAgIC0gcnVucyBmZWF0dXJlIHRlc3RzICh0b3VjaCwgLi4uKSB2aWEgbW9kZXJuaXpyXHJcbiAqICAgLSBzZXRzIHVwIGEgZ2xvYmFsIHRocmVzaG9sZGVkIHdpbmRvdyByZXNpemUgZXZlbnQgaGFuZGxlciB0aGF0XHJcbiAqICAgICBwdWJsaXNoZXMgdGhlIGV2ZW50IHRvIHRoZSBiYWNrYm9uZSB2aWV3cyBhcyAnd2luZG93LnJlc2l6ZSdcclxuICpcclxuICogQHBhcmFtIG9wdGlvbnNcclxuICogQGNvbnN0cnVjdG9yXHJcbiAqL1xyXG5tb2R1bGUuZXhwb3J0cyA9IHtcclxuXHJcbiAgLyoqXHJcbiAgICogQ29yZSBvcHRpb25zXHJcbiAgICogdGhhdCBoYXZlIGJlZW4gc3VwcGxpZWQgdGhyb3VnaCB0aGUgV2FzYWJpIENvcmUgZGVmYXVsdCBsYXlvdXQuXHJcbiAgICpcclxuICAgKiBAdHlwZSB7b2JqZWN0fVxyXG4gICAqL1xyXG4gIG9wdGlvbnM6IHt9LFxyXG5cclxuICAvKipcclxuICAgKiBIb2xkcyBhIHJlZmVyZW5jZSB0byB0aGUgd2luZG93LlxyXG4gICAqXHJcbiAgICogQHR5cGUge3dpbmRvd31cclxuICAgKi9cclxuICB3OiBudWxsLFxyXG5cclxuICAvKipcclxuICAgKiBIb2xkcyBhIHJlZmVyZW5jZSB0byB0aGUgZG9jdW1lbnQuXHJcbiAgICpcclxuICAgKiBAdHlwZSB7SFRNTERvY3VtZW50fVxyXG4gICAqL1xyXG4gIGQ6IG51bGwsXHJcblxyXG4gIC8qKlxyXG4gICAqIEhvbGQgYSByZWZlcmVuY2UgdG8gdGhlIGh0bWwgdGFnXHJcbiAgICpcclxuICAgKiBAdHlwZSB7alF1ZXJ5fVxyXG4gICAqL1xyXG4gICRodG1sOiBudWxsLFxyXG5cclxuICAvKipcclxuICAgKiBIb2xkIGEgcmVmZXJlbmNlIHRvIHRoZSBib2R5IHRhZ1xyXG4gICAqXHJcbiAgICogQHR5cGUge2pRdWVyeX1cclxuICAgKi9cclxuICAkYm9keTogbnVsbCxcclxuXHJcbiAgLyoqXHJcbiAgICogQHR5cGUge0FycmF5PEJhc2VWaWV3Pn1cclxuICAgKi9cclxuICB2aWV3czogW10sXHJcblxyXG4gIC8qKlxyXG4gICAqIEB0eXBlIHtBcnJheTxCYWNrYm9uZS5Db2xsZWN0aW9uPn1cclxuICAgKi9cclxuICBjb2xsZWN0aW9uczogW10sXHJcblxyXG4gIC8qKlxyXG4gICAqIEB0eXBlIHtBcnJheTxCYWNrYm9uZS5Nb2RlbD59XHJcbiAgICovXHJcbiAgbW9kZWxzOiBbXSxcclxuXHJcbiAgLyoqXHJcbiAgICogUmVzaXplIHRpbWVvdXQgZm9yIHdpbmRvdy5yZXNpemUgZXZlbnRzLlxyXG4gICAqXHJcbiAgICogQHR5cGUge251bWJlcn1cclxuICAgKi9cclxuICByZXNpemVUaW1lb3V0OiAwLFxyXG5cclxuICAvKipcclxuICAgKiBAdHlwZSB7b2JqZWN0fVxyXG4gICAqL1xyXG4gIGNvbXBvbmVudHM6IHt9LFxyXG5cclxuICAvKipcclxuICAgKiBJbml0aWFsaXphdGlvbiBvZiB0aGUgd2FzYWJpIGNvcmUgbW9kdWxlLlxyXG4gICAqXHJcbiAgICogQHBhcmFtIG9wdGlvbnNcclxuICAgKi9cclxuICBpbml0aWFsaXplOiBmdW5jdGlvbihvcHRpb25zKSB7XHJcbiAgICB0aGlzLm9wdGlvbnMgPSBvcHRpb25zIHx8IHt9O1xyXG4gICAgdGhpcy53ID0gd2luO1xyXG4gICAgdGhpcy5kID0gZG9jO1xyXG4gICAgdGhpcy4kaHRtbCA9ICQoJ2h0bWwnKTtcclxuICAgIHRoaXMuJGJvZHkgPSAkKCdib2R5Jyk7XHJcblxyXG4gICAgX3NldHVwQWpheC5jYWxsKHRoaXMpO1xyXG4gICAgX2luaXRpYWxpemVCYWNrZW5kVmlld3MuY2FsbCh0aGlzKTtcclxuICB9XHJcbn07XHJcbiJdfQ==
