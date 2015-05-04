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
(function (global){
var Menu = require('./views/Menu');
var NavigationToggle = require('./views/NavigationToggle');
var LangSwitch = require('./views/LangSwitch');
var WS = global.window.WS;

module.exports = function() {
  this.menu = WS.createView(Menu);
  this.navigationToggle = WS.createView(NavigationToggle);
  this.langSwitch = WS.createView(LangSwitch);
};

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"./views/LangSwitch":3,"./views/Menu":4,"./views/NavigationToggle":5}],3:[function(require,module,exports){
(function (global){
var $ = require('jquery');
var BaseView = require('wasabi.BaseView');
var WS = global.window.WS;
var _ = require('underscore');

module.exports = BaseView.extend({

  /**
   * The element of this view.
   *
   * @type {string} CSS selector
   */
  el: '.lang-switch',

  /**
   * global event listeners
   */
  globalEvents: {
    'wasabi-core--languages-sort': 'updateLanguageLinkPositions'
  },

  updateLanguageLinkPositions: function(event, data) {
    var frontendLanguages = data.frontendLanguages;
    var $sortedLi = [];
    var $li = this.$('li');
    _.each(frontendLanguages, function(el) {
      $sortedLi.push($li.filter('[data-language-id="' + el.id + '"]'));
    });
    this.$el.html($sortedLi);
  }

});

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"jquery":"jquery","underscore":"underscore","wasabi.BaseView":"wasabi.BaseView"}],4:[function(require,module,exports){
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

},{"jquery":"jquery","wasabi.BaseView":"wasabi.BaseView"}],5:[function(require,module,exports){
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

},{"jquery":"jquery","wasabi.BaseView":"wasabi.BaseView"}],6:[function(require,module,exports){
(function (global){
var $ = require('jquery');
var BaseView = require('wasabi.BaseView');
var purl = require('purl');

module.exports = BaseView.extend({
  el: '.pagination',

  events: {
    'change .limit': 'onChangeLimit'
  },

  onChangeLimit: function(event) {
    var $target = $(event.target);
    var limit = $target.val();
    var targetUrl = $target.attr('data-url');
    var currentUrl = global.purl(window.location);
    var limitParamName = $target.attr('name').split('data[').join('').split(']').join('');

    var params = currentUrl.data.param.query;
    params[limitParamName] = limit.toString();

    var queryString = '';
    var parts = [];
    for (var param in params) {
      if (!params.hasOwnProperty(param)) {
        continue;
      }
      parts.push(param + '=' + params[param]);
    }

    queryString = parts.join('&');

    if (queryString !== '') {
      queryString = '?' + queryString;
    }

    window.location = targetUrl + queryString;
  }

});

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"jquery":"jquery","purl":"purl","wasabi.BaseView":"wasabi.BaseView"}],7:[function(require,module,exports){
(function (global){
var $ = require('jquery');
var BaseView = require('wasabi.BaseView');
var WS = global.window.WS;
var LanguagesTable = require('../views/LanguagesTable');

module.exports = BaseView.extend({

  el: '.wasabi-core--languages-index',

  initialize: function(options) {
    WS.createView(LanguagesTable, {
      el: this.$el.find('table.languages')
    });
  }

});
}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"../views/LanguagesTable":8,"jquery":"jquery","wasabi.BaseView":"wasabi.BaseView"}],8:[function(require,module,exports){
(function (global){
var $ = require('jquery');
var BaseView = require('wasabi.BaseView');
var WS = global.window.WS;
require('jquery.tSortable');

module.exports = BaseView.extend({

  events: {
    'tSortable-change': 'onSort'
  },

  initialize: function(options) {
    this.$form = this.$el.closest('form');
    this.$el.tSortable({
      handle: 'a.action-sort',
      placeholder: 'sortable-placeholder',
      opacity: 0.8
    });
  },

  onSort: function(event) {
    var $table = $(event.target);
    $table.find('tbody > tr').each(function(index) {
      $(this).find('input.position').first().val(index);
    });
    $.ajax({
      url: this.$form.attr('action'),
      data: this.$form.serialize(),
      type: 'post',
      success: function(data) {
        WS.eventBus.trigger('wasabi-core--languages-sort', event, data);
      }
    });
  }

});
}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"jquery":"jquery","jquery.tSortable":"jquery.tSortable","wasabi.BaseView":"wasabi.BaseView"}],9:[function(require,module,exports){
(function (global){
var $ = require('jquery');
window.jQuery = $;
var _ = require('underscore');
var Backbone = require('backbone');
var BaseViewFactory = require('./common/BaseViewFactory');
var bs = require('bootstrap');

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
      for (var i = 0, len = this.modules.registered.length; i < len; i++) {
        var registered = this.modules.registered[i];
        var name = registered.name;
        var r = registered.require;
        var options = registered.options;
        this.modules[name] = require(r);
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
(function (global){
var $ = require('jquery');
var _ = require('underscore');
var cNavigation = require('./components/navigation/navigation');
var win = window;
var doc = document;
var WS = global.window.WS;

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

function _toggleEmptySelects() {
  require('jquery.livequery');
  $('select').livequery(function() {
    $(this).toggleClass('empty', $(this).val() === '');
    $(this).change(function() {
      $(this).toggleClass('empty', $(this).val() === '');
    });
  });
}

function _initPaginations() {
  var Pagination = require('./components/pagination/pagination');
  WS.createViews($('.pagination'), Pagination);
}

function _initSections() {
  if (this.$body.hasClass('wasabi-core--languages-index')) {
    WS.createView(require('./sections/LanguagesIndex'));
  }
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
    _toggleEmptySelects.call(this);
    _initPaginations.call(this);
    _initSections.call(this);
  }
};

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"./components/navigation/navigation":2,"./components/pagination/pagination":6,"./sections/LanguagesIndex":7,"jquery":"jquery","jquery.livequery":"jquery.livequery","underscore":"underscore"}]},{},[9])
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyaWZ5L25vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJzcmMvQXNzZXRzL2pzL2NvbW1vbi9CYXNlVmlld0ZhY3RvcnkuanMiLCJzcmMvQXNzZXRzL2pzL2NvcmUvY29tcG9uZW50cy9uYXZpZ2F0aW9uL25hdmlnYXRpb24uanMiLCJzcmMvQXNzZXRzL2pzL2NvcmUvY29tcG9uZW50cy9uYXZpZ2F0aW9uL3ZpZXdzL0xhbmdTd2l0Y2guanMiLCJzcmMvQXNzZXRzL2pzL2NvcmUvY29tcG9uZW50cy9uYXZpZ2F0aW9uL3ZpZXdzL01lbnUuanMiLCJzcmMvQXNzZXRzL2pzL2NvcmUvY29tcG9uZW50cy9uYXZpZ2F0aW9uL3ZpZXdzL05hdmlnYXRpb25Ub2dnbGUuanMiLCJzcmMvQXNzZXRzL2pzL2NvcmUvY29tcG9uZW50cy9wYWdpbmF0aW9uL3BhZ2luYXRpb24uanMiLCJzcmMvQXNzZXRzL2pzL2NvcmUvc2VjdGlvbnMvTGFuZ3VhZ2VzSW5kZXguanMiLCJzcmMvQXNzZXRzL2pzL2NvcmUvdmlld3MvTGFuZ3VhZ2VzVGFibGUuanMiLCJzcmMvQXNzZXRzL2pzL3dhc2FiaS5qcyIsInNyYy9Bc3NldHMvanMvY29tbW9uL0Jhc2VWaWV3LmpzIiwic3JjL0Fzc2V0cy9qcy9jb21tb24vU3BpblByZXNldHMuanMiLCJzcmMvQXNzZXRzL2pzL2NvcmUvbWFpbi5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtBQ0FBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7QUM5QkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7Ozs7QUNWQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDaENBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7O0FDNUpBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7OztBQy9FQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7OztBQ3hDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7Ozs7QUNmQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDbkNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7QUNyRkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUN2SkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7OztBQ05BO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBIiwiZmlsZSI6ImdlbmVyYXRlZC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzQ29udGVudCI6WyIoZnVuY3Rpb24gZSh0LG4scil7ZnVuY3Rpb24gcyhvLHUpe2lmKCFuW29dKXtpZighdFtvXSl7dmFyIGE9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtpZighdSYmYSlyZXR1cm4gYShvLCEwKTtpZihpKXJldHVybiBpKG8sITApO3ZhciBmPW5ldyBFcnJvcihcIkNhbm5vdCBmaW5kIG1vZHVsZSAnXCIrbytcIidcIik7dGhyb3cgZi5jb2RlPVwiTU9EVUxFX05PVF9GT1VORFwiLGZ9dmFyIGw9bltvXT17ZXhwb3J0czp7fX07dFtvXVswXS5jYWxsKGwuZXhwb3J0cyxmdW5jdGlvbihlKXt2YXIgbj10W29dWzFdW2VdO3JldHVybiBzKG4/bjplKX0sbCxsLmV4cG9ydHMsZSx0LG4scil9cmV0dXJuIG5bb10uZXhwb3J0c312YXIgaT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2Zvcih2YXIgbz0wO288ci5sZW5ndGg7bysrKXMocltvXSk7cmV0dXJuIHN9KSIsInZhciBfID0gcmVxdWlyZSgndW5kZXJzY29yZScpO1xyXG5cclxudmFyIEJhc2VWaWV3RmFjdG9yeSA9IGZ1bmN0aW9uKGV2ZW50QnVzKSB7XHJcbiAgdGhpcy5ldmVudEJ1cyA9IGV2ZW50QnVzO1xyXG4gIHRoaXMucmVnaXN0cnkgPSB7XHJcbiAgICBmYWN0b3J5OiB0aGlzXHJcbiAgfTtcclxufTtcclxuXHJcbkJhc2VWaWV3RmFjdG9yeS5wcm90b3R5cGUgPSBfLmV4dGVuZCh7fSwge1xyXG5cclxuICByZWdpc3RlcjogZnVuY3Rpb24oa2V5LCB2YWx1ZSkge1xyXG4gICAgdGhpcy5yZWdpc3RyeVtrZXldID0gdmFsdWU7XHJcbiAgICByZXR1cm4gdGhpcy5yZWdpc3RyeVtrZXldO1xyXG4gIH0sXHJcblxyXG4gIGNyZWF0ZTogZnVuY3Rpb24oVmlld0NsYXNzLCBvcHRpb25zKSB7XHJcbiAgICB2YXIga2xhc3MsIHBhc3NlZE9wdGlvbnM7XHJcbiAgICBvcHRpb25zID0gb3B0aW9ucyB8fCB7fTtcclxuICAgIHBhc3NlZE9wdGlvbnMgPSBfLmV4dGVuZChvcHRpb25zLCB0aGlzLnJlZ2lzdHJ5LCB7XHJcbiAgICAgIGV2ZW50QnVzOiB0aGlzLmV2ZW50QnVzXHJcbiAgICB9KTtcclxuICAgIGtsYXNzID0gVmlld0NsYXNzO1xyXG4gICAga2xhc3MucHJvdG90eXBlLmV2ZW50QnVzID0gdGhpcy5ldmVudEJ1cztcclxuICAgIHJldHVybiBuZXcga2xhc3MocGFzc2VkT3B0aW9ucyk7XHJcbiAgfVxyXG5cclxufSk7XHJcblxyXG5tb2R1bGUuZXhwb3J0cyA9IEJhc2VWaWV3RmFjdG9yeTtcclxuIiwidmFyIE1lbnUgPSByZXF1aXJlKCcuL3ZpZXdzL01lbnUnKTtcclxudmFyIE5hdmlnYXRpb25Ub2dnbGUgPSByZXF1aXJlKCcuL3ZpZXdzL05hdmlnYXRpb25Ub2dnbGUnKTtcclxudmFyIExhbmdTd2l0Y2ggPSByZXF1aXJlKCcuL3ZpZXdzL0xhbmdTd2l0Y2gnKTtcclxudmFyIFdTID0gZ2xvYmFsLndpbmRvdy5XUztcclxuXHJcbm1vZHVsZS5leHBvcnRzID0gZnVuY3Rpb24oKSB7XHJcbiAgdGhpcy5tZW51ID0gV1MuY3JlYXRlVmlldyhNZW51KTtcclxuICB0aGlzLm5hdmlnYXRpb25Ub2dnbGUgPSBXUy5jcmVhdGVWaWV3KE5hdmlnYXRpb25Ub2dnbGUpO1xyXG4gIHRoaXMubGFuZ1N3aXRjaCA9IFdTLmNyZWF0ZVZpZXcoTGFuZ1N3aXRjaCk7XHJcbn07XHJcbiIsInZhciAkID0gcmVxdWlyZSgnanF1ZXJ5Jyk7XHJcbnZhciBCYXNlVmlldyA9IHJlcXVpcmUoJ3dhc2FiaS5CYXNlVmlldycpO1xyXG52YXIgV1MgPSBnbG9iYWwud2luZG93LldTO1xyXG52YXIgXyA9IHJlcXVpcmUoJ3VuZGVyc2NvcmUnKTtcclxuXHJcbm1vZHVsZS5leHBvcnRzID0gQmFzZVZpZXcuZXh0ZW5kKHtcclxuXHJcbiAgLyoqXHJcbiAgICogVGhlIGVsZW1lbnQgb2YgdGhpcyB2aWV3LlxyXG4gICAqXHJcbiAgICogQHR5cGUge3N0cmluZ30gQ1NTIHNlbGVjdG9yXHJcbiAgICovXHJcbiAgZWw6ICcubGFuZy1zd2l0Y2gnLFxyXG5cclxuICAvKipcclxuICAgKiBnbG9iYWwgZXZlbnQgbGlzdGVuZXJzXHJcbiAgICovXHJcbiAgZ2xvYmFsRXZlbnRzOiB7XHJcbiAgICAnd2FzYWJpLWNvcmUtLWxhbmd1YWdlcy1zb3J0JzogJ3VwZGF0ZUxhbmd1YWdlTGlua1Bvc2l0aW9ucydcclxuICB9LFxyXG5cclxuICB1cGRhdGVMYW5ndWFnZUxpbmtQb3NpdGlvbnM6IGZ1bmN0aW9uKGV2ZW50LCBkYXRhKSB7XHJcbiAgICB2YXIgZnJvbnRlbmRMYW5ndWFnZXMgPSBkYXRhLmZyb250ZW5kTGFuZ3VhZ2VzO1xyXG4gICAgdmFyICRzb3J0ZWRMaSA9IFtdO1xyXG4gICAgdmFyICRsaSA9IHRoaXMuJCgnbGknKTtcclxuICAgIF8uZWFjaChmcm9udGVuZExhbmd1YWdlcywgZnVuY3Rpb24oZWwpIHtcclxuICAgICAgJHNvcnRlZExpLnB1c2goJGxpLmZpbHRlcignW2RhdGEtbGFuZ3VhZ2UtaWQ9XCInICsgZWwuaWQgKyAnXCJdJykpO1xyXG4gICAgfSk7XHJcbiAgICB0aGlzLiRlbC5odG1sKCRzb3J0ZWRMaSk7XHJcbiAgfVxyXG5cclxufSk7XHJcbiIsInZhciAkID0gcmVxdWlyZSgnanF1ZXJ5Jyk7XHJcbnZhciBCYXNlVmlldyA9IHJlcXVpcmUoJ3dhc2FiaS5CYXNlVmlldycpO1xyXG52YXIgV1MgPSBnbG9iYWwud2luZG93LldTO1xyXG5cclxuLyoqXHJcbiAqIERlZmF1bHQgb3B0aW9ucy5cclxuICpcclxuICogQHR5cGUge3tjb2xsYXBzZWRDbGFzczogc3RyaW5nLCBvcGVuQ2xhc3M6IHN0cmluZywgcG9wb3V0Q2xhc3M6IHN0cmluZ319XHJcbiAqL1xyXG52YXIgZGVmYXVsdHMgPSB7XHJcbiAgY29sbGFwc2VkQ2xhc3M6ICdjb2xsYXBzZWQnLFxyXG4gIG9wZW5DbGFzczogJ29wZW4nLFxyXG4gIHBvcG91dENsYXNzOiAncG9wb3V0J1xyXG59O1xyXG5cclxuLyoqXHJcbiAqIEdldHMgdGhlIGxpc3QgZWxlbWVudCBvZiB0aGUgY3VycmVudCBldmVudCB0YXJnZXQuXHJcbiAqXHJcbiAqIEBwYXJhbSB7T2JqZWN0fSBldmVudFxyXG4gKiBAcGFyYW0ge3N0cmluZz19IGNsc1xyXG4gKiBAcmV0dXJucyB7alF1ZXJ5fVxyXG4gKi9cclxudmFyIF9nZXRFdmVudFRhcmdldCA9IGZ1bmN0aW9uKGV2ZW50LCBjbHMpIHtcclxuICB2YXIgJHRhcmdldCA9ICQoZXZlbnQudGFyZ2V0KTtcclxuICBpZiAoISR0YXJnZXQuaXMoJ2xpJykpIHtcclxuICAgIGNscyA9IGNscyB8fCAnJztcclxuICAgIHJldHVybiAkdGFyZ2V0LmNsb3Nlc3QoJ2xpJyArIGNscyk7XHJcbiAgfVxyXG5cclxuICByZXR1cm4gJHRhcmdldDtcclxufTtcclxuXHJcbm1vZHVsZS5leHBvcnRzID0gQmFzZVZpZXcuZXh0ZW5kKHtcclxuXHJcbiAgLyoqXHJcbiAgICogVGhlIGVsZW1lbnQgb2YgdGhpcyB2aWV3LlxyXG4gICAqXHJcbiAgICogQHR5cGUge3N0cmluZ30gQ1NTIHNlbGVjdG9yXHJcbiAgICovXHJcbiAgZWw6ICcjYmFja2VuZC1tZW51ID4gdWwnLFxyXG5cclxuICAvKipcclxuICAgKiBSZWdpc3RlcmVkIGV2ZW50cyBvZiB0aGlzIHZpZXcuXHJcbiAgICpcclxuICAgKiBAcmV0dXJucyB7T2JqZWN0fVxyXG4gICAqL1xyXG4gIGV2ZW50czogZnVuY3Rpb24oKSB7XHJcbiAgICB2YXIgZXZlbnRzID0ge1xyXG4gICAgICAnY2xpY2sgPiBsaTpoYXModWwpID4gYSc6ICdvbk1haW5JdGVtQ2xpY2snXHJcbiAgICB9O1xyXG5cclxuICAgIGlmICh0aGlzLmlzQ29sbGFwc2VkKSB7XHJcbiAgICAgIGV2ZW50cyA9ICQuZXh0ZW5kKGV2ZW50cywge1xyXG4gICAgICAgICdtb3VzZWVudGVyID4gbGknOiAnb25NYWluSXRlbU1vdXNlZW50ZXInLFxyXG4gICAgICAgICdtb3VzZWxlYXZlID4gbGknOiAnb25NYWluSXRlbU1vdXNlbGVhdmUnXHJcbiAgICAgIH0pO1xyXG4gICAgfVxyXG5cclxuICAgIHJldHVybiBldmVudHM7XHJcbiAgfSxcclxuXHJcbiAgZ2xvYmFsRXZlbnRzOiB7XHJcbiAgICAnd2luZG93LnJlc2l6ZSc6ICdjb2xsYXBzZU1lbnUnXHJcbiAgfSxcclxuXHJcbiAgLyoqXHJcbiAgICogRGV0ZXJtaW5lcyBpZiB0aGUgbmF2aWdhdGlvbiBiYXIgaXMgY29sbGFwc2VkIHRvIHRoZSBzbWFsbCBzaXplICh0cnVlKSBvciBub3QgKGZhbHNlKS5cclxuICAgKlxyXG4gICAqIEB0eXBlIHtib29sZWFufVxyXG4gICAqL1xyXG4gIGlzQ29sbGFwc2VkOiBmYWxzZSxcclxuXHJcbiAgLyoqXHJcbiAgICogT3B0aW9uc1xyXG4gICAqXHJcbiAgICogQHBhcmFtIHtPYmplY3R9XHJcbiAgICovXHJcbiAgb3B0aW9uczoge30sXHJcblxyXG4gIC8qKlxyXG4gICAqIEluaXRpYWxpemF0aW9uIG9mIHRoZSB2aWV3LlxyXG4gICAqXHJcbiAgICogQHBhcmFtIHtPYmplY3Q9fSBvcHRpb25zXHJcbiAgICovXHJcbiAgaW5pdGlhbGl6ZTogZnVuY3Rpb24ob3B0aW9ucykge1xyXG4gICAgdGhpcy5vcHRpb25zID0gJC5leHRlbmQoe30sIGRlZmF1bHRzLCBvcHRpb25zKTtcclxuICAgIHRoaXMuJCgnPiBsaScpLmZpbHRlcignLmFjdGl2ZScpLnByZXYoKS5hZGRDbGFzcygncHJldi1hY3RpdmUnKTtcclxuICAgIHRoaXMuY29sbGFwc2VNZW51KCk7XHJcbiAgICAvL2lmICghV1MuZmVhdHVyZXMuaGFzVG91Y2gpIHtcclxuICAgIC8vICB0aGlzLiRlbC5wYXJlbnQoKS5wZXJmZWN0U2Nyb2xsYmFyKCk7XHJcbiAgICAvL31cclxuICAgIC8vdGhpcy5saXN0ZW5UbyhldmVudGlmeSh3aW5kb3cpLCAncmVzaXplJywgXy5iaW5kKHRoaXMub25SZXNpemUsIHRoaXMpKTtcclxuICB9LFxyXG5cclxuICAvKipcclxuICAgKiBBdHRhY2hlcy9kZXRhY2hlcyBldmVudCBoYW5kbGVycyBpZiB0aGUgbmF2aWdhdGlvbiBpcyBjb2xsYXBzZWRcclxuICAgKiBhbmQgdG9nZ2xlcyB0aGUgLmNvbGxhcHNlZCBjbGFzcy5cclxuICAgKlxyXG4gICAqIFRoZSB2aXN1YWxzIG9mIHRoZSBjb2xsYXBzZWQgbmF2aWdhdGlvbiBhcmUgZG9uZSB2aWEgbWVkaWEgcXVlcmllcyBhbmQgbm90IHZpYSBKUy5cclxuICAgKi9cclxuICBjb2xsYXBzZU1lbnU6IGZ1bmN0aW9uKCkge1xyXG4gICAgdmFyIHByZXZDb2xsYXBzZWQgPSB0aGlzLmlzQ29sbGFwc2VkO1xyXG4gICAgdGhpcy5pc0NvbGxhcHNlZCA9ICh0aGlzLiQoJz4gbGkgPiBhID4gLml0ZW0tbmFtZScpLmZpcnN0KCkuY3NzKCdkaXNwbGF5JykgPT09ICdub25lJyk7XHJcbiAgICBpZiAodGhpcy5pc0NvbGxhcHNlZCkge1xyXG4gICAgICB0aGlzLiRlbC5hZGRDbGFzcyh0aGlzLm9wdGlvbnMuY29sbGFwc2VkQ2xhc3MpO1xyXG4gICAgICBpZiAodGhpcy5pc0NvbGxhcHNlZCAhPT0gcHJldkNvbGxhcHNlZCkge1xyXG4gICAgICAgIHRoaXMuZGVsZWdhdGVFdmVudHModGhpcy5ldmVudHMoKSk7XHJcbiAgICAgIH1cclxuICAgIH0gZWxzZSB7XHJcbiAgICAgIHRoaXMuJGVsLnJlbW92ZUNsYXNzKHRoaXMub3B0aW9ucy5jb2xsYXBzZWRDbGFzcyk7XHJcbiAgICAgIHRoaXMuZGVsZWdhdGVFdmVudHModGhpcy5ldmVudHMoKSk7XHJcbiAgICB9XHJcbiAgfSxcclxuXHJcbiAgLyoqXHJcbiAgICogb25NYWluSXRlbUNsaWNrIGV2ZW50IGhhbmRsZXJcclxuICAgKiBUb2dnbGVzIHRoZSAub3BlbiBjbGFzcyBvbiBldmVyeSBsaSB0aGF0IGhhcyBhIGNoaWxkIHVsLlxyXG4gICAqXHJcbiAgICogQHBhcmFtIHtPYmplY3R9IGV2ZW50XHJcbiAgICovXHJcbiAgb25NYWluSXRlbUNsaWNrOiBmdW5jdGlvbihldmVudCkge1xyXG4gICAgX2dldEV2ZW50VGFyZ2V0KGV2ZW50KS50b2dnbGVDbGFzcyh0aGlzLm9wdGlvbnMub3BlbkNsYXNzKTtcclxuICB9LFxyXG5cclxuICAvKipcclxuICAgKiBvbk1haW5JdGVtTW91c2VlbnRlciBldmVudCBoYW5kbGVyXHJcbiAgICogQWRkIC5wb3BvdXQgY2xhc3MgdG8gaG92ZXJlZCBsaSBpZiB0aGUgbWVudSBpcyBjb2xsYXBzZWQuXHJcbiAgICpcclxuICAgKiBAcGFyYW0ge09iamVjdH0gZXZlbnRcclxuICAgKi9cclxuICBvbk1haW5JdGVtTW91c2VlbnRlcjogZnVuY3Rpb24oZXZlbnQpIHtcclxuICAgIGlmICghdGhpcy5pc0NvbGxhcHNlZCkgcmV0dXJuO1xyXG4gICAgX2dldEV2ZW50VGFyZ2V0KGV2ZW50KS5hZGRDbGFzcyh0aGlzLm9wdGlvbnMucG9wb3V0Q2xhc3MpO1xyXG4gIH0sXHJcblxyXG4gIC8qKlxyXG4gICAqIG9uTWFpbkl0ZW1Nb3VzZUxlYXZlIGV2ZW50IGhhbmRsZXJcclxuICAgKiBSZW1vdmUgLnBvcG91dCBjbGFzcyBmcm9tIHRoZSBwcmV2aW91c2x5IGhvdmVyZWQgbGkgaWYgdGhlIG1lbnUgaXMgY29sbGFwc2VkLlxyXG4gICAqXHJcbiAgICogQHBhcmFtIHtPYmplY3R9IGV2ZW50XHJcbiAgICovXHJcbiAgb25NYWluSXRlbU1vdXNlbGVhdmU6IGZ1bmN0aW9uKGV2ZW50KSB7XHJcbiAgICBpZiAoIXRoaXMuaXNDb2xsYXBzZWQpIHJldHVybjtcclxuICAgIF9nZXRFdmVudFRhcmdldChldmVudCwgJy4nICsgdGhpcy5vcHRpb25zLnBvcG91dENsYXNzKS5yZW1vdmVDbGFzcyh0aGlzLm9wdGlvbnMucG9wb3V0Q2xhc3MpO1xyXG4gIH0sXHJcblxyXG4gIC8qKlxyXG4gICAqIG9uV2luZG93UmVzaXplIGV2ZW50IGhhbmRsZXJcclxuICAgKiBDYWxscyBjb2xsYXBzZU1lbnUgb24gd2luZG93IHJlc2l6ZS5cclxuICAgKi9cclxuICBvblJlc2l6ZTogZnVuY3Rpb24oKSB7XHJcbiAgICBjbGVhclRpbWVvdXQodGhpcy5yZXNpemVUaW1lb3V0KTtcclxuICAgIHRoaXMucmVzaXplVGltZW91dCA9IHNldFRpbWVvdXQoXy5iaW5kKHRoaXMuY29sbGFwc2VNZW51LCB0aGlzKSwgMTAwKTtcclxuICB9XHJcblxyXG59KTtcclxuIiwidmFyICQgPSByZXF1aXJlKCdqcXVlcnknKTtcclxudmFyIEJhc2VWaWV3ID0gcmVxdWlyZSgnd2FzYWJpLkJhc2VWaWV3Jyk7XHJcblxyXG5tb2R1bGUuZXhwb3J0cyA9IChmdW5jdGlvbigpIHtcclxuXHJcbiAgLyoqXHJcbiAgICogSG9sZHMgYSByZWZlcmVuY2UgdG8gdGhlIGJvZHkuXHJcbiAgICpcclxuICAgKiBAdHlwZSB7alF1ZXJ5fVxyXG4gICAqL1xyXG4gIHZhciAkYm9keSA9ICQoJ2JvZHknKTtcclxuXHJcbiAgLyoqXHJcbiAgICogRGVmYXVsdCBvcHRpb25zLlxyXG4gICAqXHJcbiAgICogQHR5cGUge3tuYXZDbG9zZWRDbGFzczogc3RyaW5nfX1cclxuICAgKi9cclxuICB2YXIgZGVmYXVsdHMgPSB7XHJcbiAgICBuYXZDbG9zZWRDbGFzczogJ25hdi1jbG9zZWQnXHJcbiAgfTtcclxuXHJcbiAgcmV0dXJuIEJhc2VWaWV3LmV4dGVuZCh7XHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBUaGUgZWxlbWVudCBvZiB0aGlzIHZpZXcuXHJcbiAgICAgKlxyXG4gICAgICogQHR5cGUge3N0cmluZ30gQ1NTIHNlbGVjdG9yXHJcbiAgICAgKi9cclxuICAgIGVsOiAnYm9keSA+IGhlYWRlciAudG9nZ2xlLW5hdicsXHJcblxyXG4gICAgLyoqXHJcbiAgICAgKiBSZWdpc3RlcmVkIGV2ZW50cyBvZiB0aGlzIHZpZXcuXHJcbiAgICAgKlxyXG4gICAgICogQHR5cGUge09iamVjdH1cclxuICAgICAqL1xyXG4gICAgZXZlbnRzOiB7XHJcbiAgICAgICdjbGljayc6ICd0b2dnbGVOYXYnXHJcbiAgICB9LFxyXG5cclxuICAgIC8qKlxyXG4gICAgICogRGV0ZXJtaW5lcyBpZiB0aGUgbmF2aWdhdGlvbiBiYXIgaXMgY29sbGFwc2VkIHRvIHRoZSBzbWFsbCBzaXplICh0cnVlKSBvciBub3QgKGZhbHNlKS5cclxuICAgICAqXHJcbiAgICAgKiBAdHlwZSB7Ym9vbGVhbn1cclxuICAgICAqL1xyXG4gICAgaXNDbG9zZWQ6IGZhbHNlLFxyXG5cclxuICAgIC8qKlxyXG4gICAgICogT3B0aW9uc1xyXG4gICAgICpcclxuICAgICAqIEBwYXJhbSB7T2JqZWN0fVxyXG4gICAgICovXHJcbiAgICBvcHRpb25zOiB7fSxcclxuXHJcbiAgICAvKipcclxuICAgICAqIEluaXRpYWxpemF0aW9uIG9mIHRoZSB2aWV3LlxyXG4gICAgICpcclxuICAgICAqIEBwYXJhbSB7T2JqZWN0PX0gb3B0aW9uc1xyXG4gICAgICovXHJcbiAgICBpbml0aWFsaXplOiBmdW5jdGlvbihvcHRpb25zKSB7XHJcbiAgICAgIHRoaXMub3B0aW9ucyA9ICQuZXh0ZW5kKHt9LCBkZWZhdWx0cywgb3B0aW9ucyk7XHJcbiAgICAgIHRoaXMuaXNDbG9zZWQgPSAkYm9keS5oYXNDbGFzcyh0aGlzLm9wdGlvbnMubmF2Q2xvc2VkQ2xhc3MpO1xyXG4gICAgfSxcclxuXHJcbiAgICAvKipcclxuICAgICAqIHRvZ2dsZU5hdiBldmVudCBoYW5kbGVyXHJcbiAgICAgKiBUb2dnbGVzIG5hdkNsb3NlZENsYXNzIG9uIHRoZSBib2R5IHRvIHNob3cvaGlkZSB0aGUgbmF2aWdhdGlvbi5cclxuICAgICAqL1xyXG4gICAgdG9nZ2xlTmF2OiBmdW5jdGlvbigpIHtcclxuICAgICAgaWYgKCF0aGlzLmNsb3NlZCkge1xyXG4gICAgICAgICRib2R5LmFkZENsYXNzKHRoaXMub3B0aW9ucy5uYXZDbG9zZWRDbGFzcyk7XHJcbiAgICAgICAgdGhpcy5jbG9zZWQgPSB0cnVlO1xyXG4gICAgICB9IGVsc2Uge1xyXG4gICAgICAgICRib2R5LnJlbW92ZUNsYXNzKHRoaXMub3B0aW9ucy5uYXZDbG9zZWRDbGFzcyk7XHJcbiAgICAgICAgdGhpcy5jbG9zZWQgPSBmYWxzZTtcclxuICAgICAgfVxyXG4gICAgfVxyXG4gIH0pO1xyXG5cclxufSkoKTtcclxuIiwidmFyICQgPSByZXF1aXJlKCdqcXVlcnknKTtcclxudmFyIEJhc2VWaWV3ID0gcmVxdWlyZSgnd2FzYWJpLkJhc2VWaWV3Jyk7XHJcbnZhciBwdXJsID0gcmVxdWlyZSgncHVybCcpO1xyXG5cclxubW9kdWxlLmV4cG9ydHMgPSBCYXNlVmlldy5leHRlbmQoe1xyXG4gIGVsOiAnLnBhZ2luYXRpb24nLFxyXG5cclxuICBldmVudHM6IHtcclxuICAgICdjaGFuZ2UgLmxpbWl0JzogJ29uQ2hhbmdlTGltaXQnXHJcbiAgfSxcclxuXHJcbiAgb25DaGFuZ2VMaW1pdDogZnVuY3Rpb24oZXZlbnQpIHtcclxuICAgIHZhciAkdGFyZ2V0ID0gJChldmVudC50YXJnZXQpO1xyXG4gICAgdmFyIGxpbWl0ID0gJHRhcmdldC52YWwoKTtcclxuICAgIHZhciB0YXJnZXRVcmwgPSAkdGFyZ2V0LmF0dHIoJ2RhdGEtdXJsJyk7XHJcbiAgICB2YXIgY3VycmVudFVybCA9IGdsb2JhbC5wdXJsKHdpbmRvdy5sb2NhdGlvbik7XHJcbiAgICB2YXIgbGltaXRQYXJhbU5hbWUgPSAkdGFyZ2V0LmF0dHIoJ25hbWUnKS5zcGxpdCgnZGF0YVsnKS5qb2luKCcnKS5zcGxpdCgnXScpLmpvaW4oJycpO1xyXG5cclxuICAgIHZhciBwYXJhbXMgPSBjdXJyZW50VXJsLmRhdGEucGFyYW0ucXVlcnk7XHJcbiAgICBwYXJhbXNbbGltaXRQYXJhbU5hbWVdID0gbGltaXQudG9TdHJpbmcoKTtcclxuXHJcbiAgICB2YXIgcXVlcnlTdHJpbmcgPSAnJztcclxuICAgIHZhciBwYXJ0cyA9IFtdO1xyXG4gICAgZm9yICh2YXIgcGFyYW0gaW4gcGFyYW1zKSB7XHJcbiAgICAgIGlmICghcGFyYW1zLmhhc093blByb3BlcnR5KHBhcmFtKSkge1xyXG4gICAgICAgIGNvbnRpbnVlO1xyXG4gICAgICB9XHJcbiAgICAgIHBhcnRzLnB1c2gocGFyYW0gKyAnPScgKyBwYXJhbXNbcGFyYW1dKTtcclxuICAgIH1cclxuXHJcbiAgICBxdWVyeVN0cmluZyA9IHBhcnRzLmpvaW4oJyYnKTtcclxuXHJcbiAgICBpZiAocXVlcnlTdHJpbmcgIT09ICcnKSB7XHJcbiAgICAgIHF1ZXJ5U3RyaW5nID0gJz8nICsgcXVlcnlTdHJpbmc7XHJcbiAgICB9XHJcblxyXG4gICAgd2luZG93LmxvY2F0aW9uID0gdGFyZ2V0VXJsICsgcXVlcnlTdHJpbmc7XHJcbiAgfVxyXG5cclxufSk7XHJcbiIsInZhciAkID0gcmVxdWlyZSgnanF1ZXJ5Jyk7XHJcbnZhciBCYXNlVmlldyA9IHJlcXVpcmUoJ3dhc2FiaS5CYXNlVmlldycpO1xyXG52YXIgV1MgPSBnbG9iYWwud2luZG93LldTO1xyXG52YXIgTGFuZ3VhZ2VzVGFibGUgPSByZXF1aXJlKCcuLi92aWV3cy9MYW5ndWFnZXNUYWJsZScpO1xyXG5cclxubW9kdWxlLmV4cG9ydHMgPSBCYXNlVmlldy5leHRlbmQoe1xyXG5cclxuICBlbDogJy53YXNhYmktY29yZS0tbGFuZ3VhZ2VzLWluZGV4JyxcclxuXHJcbiAgaW5pdGlhbGl6ZTogZnVuY3Rpb24ob3B0aW9ucykge1xyXG4gICAgV1MuY3JlYXRlVmlldyhMYW5ndWFnZXNUYWJsZSwge1xyXG4gICAgICBlbDogdGhpcy4kZWwuZmluZCgndGFibGUubGFuZ3VhZ2VzJylcclxuICAgIH0pO1xyXG4gIH1cclxuXHJcbn0pOyIsInZhciAkID0gcmVxdWlyZSgnanF1ZXJ5Jyk7XHJcbnZhciBCYXNlVmlldyA9IHJlcXVpcmUoJ3dhc2FiaS5CYXNlVmlldycpO1xyXG52YXIgV1MgPSBnbG9iYWwud2luZG93LldTO1xyXG5yZXF1aXJlKCdqcXVlcnkudFNvcnRhYmxlJyk7XHJcblxyXG5tb2R1bGUuZXhwb3J0cyA9IEJhc2VWaWV3LmV4dGVuZCh7XHJcblxyXG4gIGV2ZW50czoge1xyXG4gICAgJ3RTb3J0YWJsZS1jaGFuZ2UnOiAnb25Tb3J0J1xyXG4gIH0sXHJcblxyXG4gIGluaXRpYWxpemU6IGZ1bmN0aW9uKG9wdGlvbnMpIHtcclxuICAgIHRoaXMuJGZvcm0gPSB0aGlzLiRlbC5jbG9zZXN0KCdmb3JtJyk7XHJcbiAgICB0aGlzLiRlbC50U29ydGFibGUoe1xyXG4gICAgICBoYW5kbGU6ICdhLmFjdGlvbi1zb3J0JyxcclxuICAgICAgcGxhY2Vob2xkZXI6ICdzb3J0YWJsZS1wbGFjZWhvbGRlcicsXHJcbiAgICAgIG9wYWNpdHk6IDAuOFxyXG4gICAgfSk7XHJcbiAgfSxcclxuXHJcbiAgb25Tb3J0OiBmdW5jdGlvbihldmVudCkge1xyXG4gICAgdmFyICR0YWJsZSA9ICQoZXZlbnQudGFyZ2V0KTtcclxuICAgICR0YWJsZS5maW5kKCd0Ym9keSA+IHRyJykuZWFjaChmdW5jdGlvbihpbmRleCkge1xyXG4gICAgICAkKHRoaXMpLmZpbmQoJ2lucHV0LnBvc2l0aW9uJykuZmlyc3QoKS52YWwoaW5kZXgpO1xyXG4gICAgfSk7XHJcbiAgICAkLmFqYXgoe1xyXG4gICAgICB1cmw6IHRoaXMuJGZvcm0uYXR0cignYWN0aW9uJyksXHJcbiAgICAgIGRhdGE6IHRoaXMuJGZvcm0uc2VyaWFsaXplKCksXHJcbiAgICAgIHR5cGU6ICdwb3N0JyxcclxuICAgICAgc3VjY2VzczogZnVuY3Rpb24oZGF0YSkge1xyXG4gICAgICAgIFdTLmV2ZW50QnVzLnRyaWdnZXIoJ3dhc2FiaS1jb3JlLS1sYW5ndWFnZXMtc29ydCcsIGV2ZW50LCBkYXRhKTtcclxuICAgICAgfVxyXG4gICAgfSk7XHJcbiAgfVxyXG5cclxufSk7IiwidmFyICQgPSByZXF1aXJlKCdqcXVlcnknKTtcclxud2luZG93LmpRdWVyeSA9ICQ7XHJcbnZhciBfID0gcmVxdWlyZSgndW5kZXJzY29yZScpO1xyXG52YXIgQmFja2JvbmUgPSByZXF1aXJlKCdiYWNrYm9uZScpO1xyXG52YXIgQmFzZVZpZXdGYWN0b3J5ID0gcmVxdWlyZSgnLi9jb21tb24vQmFzZVZpZXdGYWN0b3J5Jyk7XHJcbnZhciBicyA9IHJlcXVpcmUoJ2Jvb3RzdHJhcCcpO1xyXG5cclxudmFyIFdTID0gKGZ1bmN0aW9uKCkge1xyXG5cclxuICB2YXIgZXZlbnRCdXMgPSBfLmV4dGVuZCh7fSwgQmFja2JvbmUuRXZlbnRzKTtcclxuICB2YXIgdmlld0ZhY3RvcnkgPSBuZXcgQmFzZVZpZXdGYWN0b3J5KGV2ZW50QnVzKTtcclxuICB2YXIgcmVzaXplVGltZW91dCA9IDA7XHJcblxyXG4gIHJldHVybiB7XHJcbiAgICBmZWF0dXJlczoge30sXHJcblxyXG4gICAgbW9kdWxlczoge1xyXG4gICAgICByZWdpc3RlcmVkOiBbXVxyXG4gICAgfSxcclxuXHJcbiAgICAvKipcclxuICAgICAqIEdsb2JhbCBldmVudCBidXMuXHJcbiAgICAgKlxyXG4gICAgICogQHR5cGUge0JhY2tib25lLkV2ZW50c31cclxuICAgICAqL1xyXG4gICAgZXZlbnRCdXM6IGV2ZW50QnVzLFxyXG5cclxuICAgIC8qKlxyXG4gICAgICogR2xvYmFsIHZpZXcgZmFjdG9yeSB0byBpbnN0YW50aWF0ZSBuZXcgdmlld3MuXHJcbiAgICAgKlxyXG4gICAgICogQHR5cGUge0Jhc2VWaWV3RmFjdG9yeX1cclxuICAgICAqL1xyXG4gICAgdmlld0ZhY3Rvcnk6IHZpZXdGYWN0b3J5LFxyXG5cclxuICAgIGJvb3Q6IGZ1bmN0aW9uICgpIHtcclxuICAgICAgZm9yICh2YXIgaSA9IDAsIGxlbiA9IHRoaXMubW9kdWxlcy5yZWdpc3RlcmVkLmxlbmd0aDsgaSA8IGxlbjsgaSsrKSB7XHJcbiAgICAgICAgdmFyIHJlZ2lzdGVyZWQgPSB0aGlzLm1vZHVsZXMucmVnaXN0ZXJlZFtpXTtcclxuICAgICAgICB2YXIgbmFtZSA9IHJlZ2lzdGVyZWQubmFtZTtcclxuICAgICAgICB2YXIgciA9IHJlZ2lzdGVyZWQucmVxdWlyZTtcclxuICAgICAgICB2YXIgb3B0aW9ucyA9IHJlZ2lzdGVyZWQub3B0aW9ucztcclxuICAgICAgICB0aGlzLm1vZHVsZXNbbmFtZV0gPSByZXF1aXJlKHIpO1xyXG4gICAgICAgIHRoaXMubW9kdWxlc1tuYW1lXS5pbml0aWFsaXplKG9wdGlvbnMpO1xyXG4gICAgICB9XHJcblxyXG4gICAgICAkKHdpbmRvdykub24oJ3Jlc2l6ZScsIGZ1bmN0aW9uKCkge1xyXG4gICAgICAgIGNsZWFyVGltZW91dChyZXNpemVUaW1lb3V0KTtcclxuICAgICAgICByZXNpemVUaW1lb3V0ID0gc2V0VGltZW91dChfLmJpbmQoZnVuY3Rpb24oKSB7XHJcbiAgICAgICAgICB0aGlzLmV2ZW50QnVzLnRyaWdnZXIoJ3dpbmRvdy5yZXNpemUnKTtcclxuICAgICAgICB9LCB0aGlzKSwgMTAwKTtcclxuICAgICAgfSk7XHJcbiAgICB9LFxyXG5cclxuICAgIHJlZ2lzdGVyTW9kdWxlOiBmdW5jdGlvbiAobmFtZSwgb3B0aW9ucykge1xyXG4gICAgICB0aGlzLm1vZHVsZXMucmVnaXN0ZXJlZC5wdXNoKHtcclxuICAgICAgICBuYW1lOiBuYW1lLFxyXG4gICAgICAgIHJlcXVpcmU6IG5hbWUsXHJcbiAgICAgICAgb3B0aW9uczogb3B0aW9ucyB8fCB7fVxyXG4gICAgICB9KTtcclxuICAgIH0sXHJcblxyXG4gICAgZ2V0OiBmdW5jdGlvbiAobmFtZSkge1xyXG4gICAgICBpZiAodGhpcy5tb2R1bGVzW25hbWVdKSB7XHJcbiAgICAgICAgcmV0dXJuIHRoaXMubW9kdWxlc1tuYW1lXTtcclxuICAgICAgfVxyXG4gICAgICBjb25zb2xlLmRlYnVnKCdtb2R1bGUgXCInICsgbmFtZSArIFwiIGlzIG5vdCByZWdpc3RlcmVkLlwiKTtcclxuICAgIH0sXHJcblxyXG4gICAgY3JlYXRlVmlldzogZnVuY3Rpb24odmlld0NsYXNzLCBvcHRpb25zKSB7XHJcbiAgICAgIHJldHVybiB0aGlzLnZpZXdGYWN0b3J5LmNyZWF0ZSh2aWV3Q2xhc3MsIG9wdGlvbnMgfHwge30pO1xyXG4gICAgfSxcclxuXHJcbiAgICBjcmVhdGVWaWV3czogZnVuY3Rpb24oJGVsZW1lbnRzLCBWaWV3Q2xhc3MsIG9wdGlvbnMpIHtcclxuICAgICAgdmFyIHZpZXdzID0gW107XHJcbiAgICAgIGlmICgkZWxlbWVudHMubGVuZ3RoID4gMCkge1xyXG4gICAgICAgICRlbGVtZW50cy5lYWNoKGZ1bmN0aW9uKGluZGV4LCBlbCkge1xyXG4gICAgICAgICAgdmlld3MucHVzaChXUy5jcmVhdGVWaWV3KFZpZXdDbGFzcywgXy5leHRlbmQoe2VsOiBlbH0sIG9wdGlvbnMgfHwge30pKSk7XHJcbiAgICAgICAgfSk7XHJcbiAgICAgIH1cclxuICAgICAgcmV0dXJuIHZpZXdzO1xyXG4gICAgfVxyXG4gIH07XHJcblxyXG59KSgpO1xyXG5cclxuZ2xvYmFsLndpbmRvdy5XUyA9IFdTO1xyXG4iLCJ2YXIgJCA9IHJlcXVpcmUoJ2pxdWVyeScpO1xyXG52YXIgXyA9IHJlcXVpcmUoJ3VuZGVyc2NvcmUnKTtcclxudmFyIEJhY2tib25lID0gcmVxdWlyZSgnYmFja2JvbmUnKTtcclxudmFyIFNwaW5uZXIgPSByZXF1aXJlKCdzcGluLmpzJyk7XHJcblxyXG5CYWNrYm9uZS4kID0gJDtcclxuXHJcbihmdW5jdGlvbihWaWV3KXtcclxuICAndXNlIHN0cmljdCc7XHJcbiAgLy8gQWRkaXRpb25hbCBleHRlbnNpb24gbGF5ZXIgZm9yIFZpZXdzXHJcbiAgVmlldy5mdWxsRXh0ZW5kID0gZnVuY3Rpb24gKHByb3RvUHJvcHMsIHN0YXRpY1Byb3BzKSB7XHJcbiAgICAvLyBDYWxsIGRlZmF1bHQgZXh0ZW5kIG1ldGhvZFxyXG4gICAgdmFyIGV4dGVuZGVkID0gVmlldy5leHRlbmQuY2FsbCh0aGlzLCBwcm90b1Byb3BzLCBzdGF0aWNQcm9wcyk7XHJcbiAgICAvLyBBZGQgYSB1c2FibGUgc3VwZXIgbWV0aG9kIGZvciBiZXR0ZXIgaW5oZXJpdGFuY2VcclxuICAgIGV4dGVuZGVkLnByb3RvdHlwZS5fc3VwZXIgPSB0aGlzLnByb3RvdHlwZTtcclxuICAgIC8vIEFwcGx5IG5ldyBvciBkaWZmZXJlbnQgZXZlbnRzIG9uIHRvcCBvZiB0aGUgb3JpZ2luYWxcclxuICAgIGlmIChwcm90b1Byb3BzLmV2ZW50cykge1xyXG4gICAgICBmb3IgKHZhciBrIGluIHRoaXMucHJvdG90eXBlLmV2ZW50cykge1xyXG4gICAgICAgIGlmICghZXh0ZW5kZWQucHJvdG90eXBlLmV2ZW50c1trXSkge1xyXG4gICAgICAgICAgZXh0ZW5kZWQucHJvdG90eXBlLmV2ZW50c1trXSA9IHRoaXMucHJvdG90eXBlLmV2ZW50c1trXTtcclxuICAgICAgICB9XHJcbiAgICAgIH1cclxuICAgIH1cclxuICAgIHJldHVybiBleHRlbmRlZDtcclxuICB9O1xyXG5cclxufSkoQmFja2JvbmUuVmlldyk7XHJcblxyXG5tb2R1bGUuZXhwb3J0cyA9IEJhY2tib25lLlZpZXcuZXh0ZW5kKHtcclxuXHJcbiAgJGJsb2NrQmFja2Ryb3A6ICQoJzxkaXYgY2xhc3M9XCJibG9jay1iYWNrZHJvcFwiPjwvZGl2PicpLFxyXG4gIG51bWJlck9mQmxvY2tzOiAwLFxyXG4gIHNwaW5uZXI6IG51bGwsXHJcblxyXG4gIGRlbGVnYXRlRXZlbnRzOiBmdW5jdGlvbihldmVudHMpIHtcclxuICAgIHZhciBldmVudCwgaGFuZGxlciwgcmVzdWx0cztcclxuXHJcbiAgICBCYWNrYm9uZS5WaWV3LnByb3RvdHlwZS5kZWxlZ2F0ZUV2ZW50cy5jYWxsKHRoaXMsIGV2ZW50cyk7XHJcblxyXG4gICAgdGhpcy5nbG9iYWxFdmVudHMgPSB0aGlzLmdsb2JhbEV2ZW50cyB8fCB7fTtcclxuXHJcbiAgICBpZiAodHlwZW9mIHRoaXMuZ2xvYmFsRXZlbnRzID09PSAnZnVuY3Rpb24nKSB7XHJcbiAgICAgIHRoaXMuZ2xvYmFsRXZlbnRzID0gdGhpcy5nbG9iYWxFdmVudHMoKTtcclxuICAgIH1cclxuXHJcbiAgICByZXN1bHRzID0gW107XHJcblxyXG4gICAgZm9yIChldmVudCBpbiB0aGlzLmdsb2JhbEV2ZW50cykge1xyXG4gICAgICBpZiAoIXRoaXMuZ2xvYmFsRXZlbnRzLmhhc093blByb3BlcnR5KGV2ZW50KSkge1xyXG4gICAgICAgIGNvbnRpbnVlO1xyXG4gICAgICB9XHJcbiAgICAgIGhhbmRsZXIgPSB0aGlzLmdsb2JhbEV2ZW50c1tldmVudF07XHJcbiAgICAgIHJlc3VsdHMucHVzaCh0aGlzLmV2ZW50QnVzLmJpbmQoZXZlbnQsIF8uYmluZCh0aGlzW2hhbmRsZXJdLCB0aGlzKSkpO1xyXG4gICAgfVxyXG5cclxuICAgIHJldHVybiByZXN1bHRzO1xyXG4gIH0sXHJcblxyXG4gIC8qKlxyXG4gICAqIEJsb2NrIGEgdmlldyBieTpcclxuICAgKiAtIG92ZXJsYXlpbmcgYSBkaXYgd2l0aCBhIHNwZWNpZmllZCBjb2xvciBhbmQgb3BhY2l0eVxyXG4gICAqIC0gc2hvd2luZyBhIHNwaW5uZXIgaW4gdGhlIGNlbnRlciBvZiB0aGlzIGRpdlxyXG4gICAqIC0gY2F0Y2hpbmcgYWxsIHVzZXIgZ2VuZXJhdGVkIGV2ZW50cyBvbiB0aGUgYmxvY2tlZCBlbGVtZW50XHJcbiAgICpcclxuICAgKiBAcGFyYW0gb3B0aW9uc1xyXG4gICAqL1xyXG4gIGJsb2NrVGhpczogZnVuY3Rpb24ob3B0aW9ucykge1xyXG4gICAgdGhpcy5udW1iZXJPZkJsb2NrcysrO1xyXG4gICAgdmFyIG9mZnNldCA9IHRoaXMuJGVsLm9mZnNldCgpO1xyXG4gICAgdmFyIGJvcmRlckxlZnQgPSB0aGlzLiRlbC5jc3MoJ2JvcmRlckxlZnRXaWR0aCcpO1xyXG4gICAgdmFyIGJvcmRlclRvcCA9IHRoaXMuJGVsLmNzcygnYm9yZGVyVG9wV2lkdGgnKTtcclxuICAgIHZhciB3aWR0aCA9IHRoaXMuJGVsLmlubmVyV2lkdGgoKSArIChvcHRpb25zLmRlbHRhV2lkdGggfHwgMCk7XHJcbiAgICB2YXIgaGVpZ2h0ID0gdGhpcy4kZWwuaW5uZXJIZWlnaHQoKSArIChvcHRpb25zLmRlbHRhSGVpZ2h0IHx8IDApO1xyXG5cclxuICAgIGlmIChib3JkZXJMZWZ0ICE9PSAnJykge1xyXG4gICAgICBib3JkZXJMZWZ0ID0gcGFyc2VJbnQoYm9yZGVyTGVmdC5zcGxpdCgncHgnKVswXSk7XHJcbiAgICAgIG9mZnNldC5sZWZ0ICs9IGJvcmRlckxlZnQ7XHJcbiAgICB9XHJcblxyXG4gICAgaWYgKGJvcmRlclRvcCAhPT0gJycpIHtcclxuICAgICAgYm9yZGVyVG9wID0gcGFyc2VJbnQoYm9yZGVyVG9wLnNwbGl0KCdweCcpWzBdKTtcclxuICAgICAgb2Zmc2V0LnRvcCArPSBib3JkZXJUb3A7XHJcbiAgICB9XHJcblxyXG4gICAgdGhpcy4kYmxvY2tCYWNrZHJvcC5jc3Moe1xyXG4gICAgICBwb3NpdGlvbjogJ2Fic29sdXRlJyxcclxuICAgICAgdG9wOiBvZmZzZXQudG9wLFxyXG4gICAgICBsZWZ0OiBvZmZzZXQubGVmdCxcclxuICAgICAgd2lkdGg6IHdpZHRoLFxyXG4gICAgICBoZWlnaHQ6IGhlaWdodCxcclxuICAgICAgYmFja2dyb3VuZENvbG9yOiBvcHRpb25zLmJhY2tncm91bmRDb2xvciB8fCAndHJhbnNwYXJlbnQnLFxyXG4gICAgICBvcGFjaXR5OiBvcHRpb25zLm9wYWNpdHkgfHwgMC42LFxyXG4gICAgICBjdXJzb3I6IG9wdGlvbnMuY3Vyc29yIHx8ICd3YWl0JyxcclxuICAgICAgekluZGV4OiBvcHRpb25zLnpJbmRleCB8fCA5OTk3XHJcbiAgICB9KS5oaWRlKCkuYXBwZW5kVG8oJCgnYm9keScpKTtcclxuXHJcbiAgICB0aGlzLiRibG9ja0JhY2tkcm9wLmZhZGVJbigxMDAsICQucHJveHkoZnVuY3Rpb24oKSB7XHJcbiAgICAgIHRoaXMuJGJsb2NrQmFja2Ryb3Aub24oJ21vdXNlZG93biBtb3VzZXVwIGtleWRvd24ga2V5cHJlc3Mga2V5dXAgdG91Y2hzdGFydCB0b3VjaGVuZCB0b3VjaG1vdmUnLCBfLmJpbmQodGhpcy5oYW5kbGVCbG9ja2VkRXZlbnQsIHRoaXMpKTtcclxuICAgICAgaWYgKG9wdGlvbnMuc3Bpbm5lciAmJiBvcHRpb25zLnNwaW5uZXIgIT09IGZhbHNlKSB7XHJcbiAgICAgICAgdGhpcy5zcGlubmVyID0gdGhpcy5zcGlubmVyIHx8IG5ldyBTcGlubmVyKG9wdGlvbnMuc3Bpbm5lciB8fCBmYWxzZSk7XHJcbiAgICAgIH1cclxuICAgICAgaWYgKHRoaXMuc3Bpbm5lcikge1xyXG4gICAgICAgIHRoaXMuc3Bpbm5lci5zcGluKHRoaXMuJGJsb2NrQmFja2Ryb3AuZ2V0KDApKTtcclxuICAgICAgfVxyXG4gICAgfSwgdGhpcykpO1xyXG4gIH0sXHJcblxyXG4gIC8qKlxyXG4gICAqIFVuYmxvY2sgYSB2aWV3LlxyXG4gICAqXHJcbiAgICogQHBhcmFtIGNhbGxiYWNrXHJcbiAgICovXHJcbiAgdW5ibG9ja1RoaXM6IGZ1bmN0aW9uKGNhbGxiYWNrKSB7XHJcbiAgICBpZiAodGhpcy5udW1iZXJPZkJsb2NrcyA+PSAxKSB7XHJcbiAgICAgIHRoaXMubnVtYmVyT2ZCbG9ja3MtLTtcclxuICAgIH1cclxuICAgIGlmICh0aGlzLm51bWJlck9mQmxvY2tzID09PSAwKSB7XHJcbiAgICAgIGNhbGxiYWNrID0gY2FsbGJhY2sgfHwgZnVuY3Rpb24oKSB7fTtcclxuICAgICAgaWYgKHRoaXMuc3Bpbm5lcikge1xyXG4gICAgICAgIHRoaXMuc3Bpbm5lci5zdG9wKCk7XHJcbiAgICAgIH1cclxuICAgICAgdGhpcy4kYmxvY2tCYWNrZHJvcC5yZW1vdmUoKTtcclxuICAgICAgdGhpcy4kYmxvY2tCYWNrZHJvcC5vZmYoJ21vdXNlZG93biBtb3VzZXVwIGtleWRvd24ga2V5cHJlc3Mga2V5dXAgdG91Y2hzdGFydCB0b3VjaGVuZCB0b3VjaG1vdmUnLCBfLmJpbmQodGhpcy5oYW5kbGVCbG9ja2VkRXZlbnQsIHRoaXMpKTtcclxuICAgICAgaWYgKHR5cGVvZiBjYWxsYmFjayA9PT0gJ2Z1bmN0aW9uJykge1xyXG4gICAgICAgIGNhbGxiYWNrLmNhbGwodGhpcyk7XHJcbiAgICAgIH1cclxuICAgIH1cclxuICB9LFxyXG5cclxuICAvKipcclxuICAgKiBDYXRjaCBhbGwgZXZlbnQgaGFuZGxlciBmb3IgYmxvY2tlZCB2aWV3cy5cclxuICAgKlxyXG4gICAqIEBwYXJhbSBldmVudFxyXG4gICAqL1xyXG4gIGhhbmRsZUJsb2NrZWRFdmVudDogZnVuY3Rpb24oZXZlbnQpIHtcclxuICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XHJcbiAgICBldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcclxuICB9LFxyXG5cclxuICBkZXN0cm95OiBmdW5jdGlvbigpIHtcclxuICAgIC8vIENPTVBMRVRFTFkgVU5CSU5EIFRIRSBWSUVXXHJcbiAgICB0aGlzLnVuZGVsZWdhdGVFdmVudHMoKTtcclxuXHJcbiAgICB0aGlzLiRlbC5yZW1vdmVEYXRhKCkudW5iaW5kKCk7XHJcblxyXG4gICAgLy8gUmVtb3ZlIHZpZXcgZnJvbSBET01cclxuICAgIHRoaXMucmVtb3ZlKCk7XHJcbiAgICBCYWNrYm9uZS5WaWV3LnByb3RvdHlwZS5yZW1vdmUuY2FsbCh0aGlzKTtcclxuICB9XHJcblxyXG59KTtcclxuIiwibW9kdWxlLmV4cG9ydHMgPSB7XHJcbiAgc21hbGw6ICB7IGxpbmVzOiAxMiwgbGVuZ3RoOiAwLCB3aWR0aDogMywgcmFkaXVzOiA2IH0sXHJcbiAgbWVkaXVtOiB7IGxpbmVzOiA5ICwgbGVuZ3RoOiA0LCB3aWR0aDogMiwgcmFkaXVzOiAzIH0sXHJcbiAgbGFyZ2U6ICB7IGxpbmVzOiAxMSwgbGVuZ3RoOiA3LCB3aWR0aDogMiwgcmFkaXVzOiA1IH0sXHJcbiAgbWVnYTogICB7IGxpbmVzOiAxMSwgbGVuZ3RoOiAxMCwgd2lkdGg6IDMsIHJhZGl1czogOH1cclxufTtcclxuIiwidmFyICQgPSByZXF1aXJlKCdqcXVlcnknKTtcclxudmFyIF8gPSByZXF1aXJlKCd1bmRlcnNjb3JlJyk7XHJcbnZhciBjTmF2aWdhdGlvbiA9IHJlcXVpcmUoJy4vY29tcG9uZW50cy9uYXZpZ2F0aW9uL25hdmlnYXRpb24nKTtcclxudmFyIHdpbiA9IHdpbmRvdztcclxudmFyIGRvYyA9IGRvY3VtZW50O1xyXG52YXIgV1MgPSBnbG9iYWwud2luZG93LldTO1xyXG5cclxuLyoqXHJcbiAqIFNldHVwIGRlZmF1bHQgYWpheCBvcHRpb25zIGFuZCBnbG9iYWwgYWpheCBldmVudCBoYW5kbGVycy5cclxuICpcclxuICogQHByaXZhdGVcclxuICovXHJcbmZ1bmN0aW9uIF9zZXR1cEFqYXgoKSB7XHJcbiAgJC5hamF4U2V0dXAoe1xyXG4gICAgZGF0YVR5cGU6ICdqc29uJ1xyXG4gIH0pO1xyXG4gICQoZG9jKVxyXG4gICAgLmFqYXhTdWNjZXNzKF9vbkFqYXhTdWNjZXNzKVxyXG4gICAgLmFqYXhFcnJvcihfb25BamF4RXJyb3IpO1xyXG59XHJcblxyXG4vKipcclxuICogRGVmYXVsdCBhamF4IHN1Y2Nlc3MgaGFuZGxlci5cclxuICogRGlzcGxheXMgYSBmbGFzaCBtZXNzYWdlIGlmIHRoZSByZXNwb25zZSBjb250YWluc1xyXG4gKiB7XHJcbiAgICAgKiAgICdzdGF0dXMnOiAnLi4uJyAjIHRoZSBjbGFzcyBvZiB0aGUgZmxhc2ggbWVzc2FnZVxyXG4gICAgICogICAnZmxhc2hNZXNzYWdlJzogJy4uLicgIyB0aGUgdGV4dCBvZiB0aGUgZmxhc2ggbWVzc2FnZVxyXG4gICAgICogfVxyXG4gKlxyXG4gKiBAcGFyYW0gZXZlbnRcclxuICogQHBhcmFtIHhoclxyXG4gKiBAcHJpdmF0ZVxyXG4gKi9cclxuZnVuY3Rpb24gX29uQWpheFN1Y2Nlc3MoZXZlbnQsIHhocikge1xyXG4gIGlmICh4aHIuc3RhdHVzID09IDIwMCAmJiB4aHIuc3RhdHVzVGV4dCA9PSAnT0snKSB7XHJcbiAgICB2YXIgZGF0YSA9ICQucGFyc2VKU09OKHhoci5yZXNwb25zZVRleHQpIHx8IHt9O1xyXG5cclxuICAgIGlmICh0eXBlb2YgZGF0YS5zdGF0dXMgIT09ICd1bmRlZmluZWQnKSB7XHJcbiAgICAgIGlmICh0eXBlb2YgZGF0YS5mbGFzaCAhPT0gJ3VuZGVmaW5lZCcpIHtcclxuICAgICAgICBfZmxhc2hNZXNzYWdlKGRhdGEuZmxhc2gpO1xyXG4gICAgICB9XHJcbiAgICAgIGlmICh0eXBlb2YgZGF0YS5yZWRpcmVjdCAhPT0gJ3VuZGVmaW5lZCcpIHtcclxuICAgICAgICB3aW4ubG9jYXRpb24gPSBkYXRhLnJlZGlyZWN0O1xyXG4gICAgICB9XHJcbiAgICB9XHJcbiAgfVxyXG59XHJcblxyXG4vKipcclxuICogRGVmYXVsdCBhamF4IGVycm9yIGhhbmRsZXIuXHJcbiAqXHJcbiAqIEBwYXJhbSBldmVudFxyXG4gKiBAcGFyYW0geGhyXHJcbiAqIEBwcml2YXRlXHJcbiAqL1xyXG5mdW5jdGlvbiBfb25BamF4RXJyb3IoZXZlbnQsIHhocikge1xyXG4gIHZhciBkYXRhO1xyXG4gIGlmICh4aHIuc3RhdHVzID09IDQwMSkge1xyXG4gICAgZGF0YSA9ICQucGFyc2VKU09OKHhoci5yZXNwb25zZVRleHQpIHx8IHt9O1xyXG4gICAgaWYgKHR5cGVvZiBkYXRhLm5hbWUgIT09ICd1bmRlZmluZWQnKSB7XHJcbiAgICAgIGlmIChjb25maXJtKGRhdGEubmFtZSkpIHtcclxuICAgICAgICB3aW4ubG9jYXRpb24ucmVsb2FkKCk7XHJcbiAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgd2luLmxvY2F0aW9uLnJlbG9hZCgpO1xyXG4gICAgICB9XHJcbiAgICB9XHJcbiAgfVxyXG4gIGlmICh4aHIuc3RhdHVzID09IDUwMCkge1xyXG4gICAgZGF0YSA9ICQucGFyc2VKU09OKHhoci5yZXNwb25zZVRleHQpIHx8IHt9O1xyXG4gICAgaWYgKHR5cGVvZiBkYXRhLm5hbWUgIT09ICd1bmRlZmluZWQnKSB7XHJcbiAgICAgIF9mbGFzaE1lc3NhZ2Uoe1xyXG4gICAgICAgIGJlZm9yZTogJ2Rpdi50aXRsZS1wYWQnLFxyXG4gICAgICAgIGNsYXNzOiAnZXJyb3InLFxyXG4gICAgICAgIG1lc3NhZ2U6IGRhdGEubmFtZVxyXG4gICAgICB9KTtcclxuICAgIH1cclxuICB9XHJcbn1cclxuXHJcbi8qKlxyXG4gKiBSZW5kZXIgYSBmbGFzaCBtZXNzYWdlIGFmdGVyIGEgc3BlY2lmaWMgZWxlbWVudC5cclxuICpcclxuICogQHBhcmFtIHtPYmplY3R9IGZsYXNoIFRoZSBmbGFzaCBvYmplY3QgcmV0dXJuZWQgYnkgdGhlIGFqYXggcmVxdWVzdC5cclxuICogQHByaXZhdGVcclxuICovXHJcbmZ1bmN0aW9uIF9mbGFzaE1lc3NhZ2UoZmxhc2gpIHtcclxuICB2YXIgaW5zZXJ0QmVmb3JlID0gZmxhc2guYmVmb3JlIHx8IGZhbHNlO1xyXG4gIHZhciBpbnNlcnRBZnRlciA9IGZsYXNoLmFmdGVyIHx8IGZhbHNlO1xyXG4gIHZhciBtZXNzYWdlID0gZmxhc2gubWVzc2FnZTtcclxuICB2YXIgY2xzID0gKGZsYXNoLmNsYXNzKSA/ICdmbGFzaC0nICsgZmxhc2guY2xhc3MgOiAnZmxhc2gtc3VjY2Vzcyc7XHJcbiAgdmFyICR0YXJnZXQgPSBmYWxzZTtcclxuICB2YXIgJGZsYXNoID0gJCgnPGRpdi8+JykuYWRkQ2xhc3MoY2xzKS5odG1sKG1lc3NhZ2UpO1xyXG5cclxuICBpZiAoaW5zZXJ0QmVmb3JlKSB7XHJcbiAgICAkdGFyZ2V0ID0gJChpbnNlcnRCZWZvcmUpO1xyXG4gICAgJHRhcmdldC5wcmV2KCcuJyArIGNscykucmVtb3ZlKCk7XHJcbiAgICAkZmxhc2guaW5zZXJ0QmVmb3JlKCR0YXJnZXQpO1xyXG4gIH0gZWxzZSBpZiAoaW5zZXJ0QWZ0ZXIpIHtcclxuICAgICR0YXJnZXQgPSAkKGluc2VydEFmdGVyKTtcclxuICAgICR0YXJnZXQubmV4dCgnLicgKyBjbHMpLnJlbW92ZSgpO1xyXG4gICAgJGZsYXNoLmluc2VydEFmdGVyKCR0YXJnZXQpO1xyXG4gIH1cclxufVxyXG5cclxuZnVuY3Rpb24gX2luaXRpYWxpemVCYWNrZW5kVmlld3MoKSB7XHJcbiAgdGhpcy5jb21wb25lbnRzLm5hdmlnYXRpb24gPSBuZXcgY05hdmlnYXRpb24oKTtcclxufVxyXG5cclxuZnVuY3Rpb24gX3RvZ2dsZUVtcHR5U2VsZWN0cygpIHtcclxuICByZXF1aXJlKCdqcXVlcnkubGl2ZXF1ZXJ5Jyk7XHJcbiAgJCgnc2VsZWN0JykubGl2ZXF1ZXJ5KGZ1bmN0aW9uKCkge1xyXG4gICAgJCh0aGlzKS50b2dnbGVDbGFzcygnZW1wdHknLCAkKHRoaXMpLnZhbCgpID09PSAnJyk7XHJcbiAgICAkKHRoaXMpLmNoYW5nZShmdW5jdGlvbigpIHtcclxuICAgICAgJCh0aGlzKS50b2dnbGVDbGFzcygnZW1wdHknLCAkKHRoaXMpLnZhbCgpID09PSAnJyk7XHJcbiAgICB9KTtcclxuICB9KTtcclxufVxyXG5cclxuZnVuY3Rpb24gX2luaXRQYWdpbmF0aW9ucygpIHtcclxuICB2YXIgUGFnaW5hdGlvbiA9IHJlcXVpcmUoJy4vY29tcG9uZW50cy9wYWdpbmF0aW9uL3BhZ2luYXRpb24nKTtcclxuICBXUy5jcmVhdGVWaWV3cygkKCcucGFnaW5hdGlvbicpLCBQYWdpbmF0aW9uKTtcclxufVxyXG5cclxuZnVuY3Rpb24gX2luaXRTZWN0aW9ucygpIHtcclxuICBpZiAodGhpcy4kYm9keS5oYXNDbGFzcygnd2FzYWJpLWNvcmUtLWxhbmd1YWdlcy1pbmRleCcpKSB7XHJcbiAgICBXUy5jcmVhdGVWaWV3KHJlcXVpcmUoJy4vc2VjdGlvbnMvTGFuZ3VhZ2VzSW5kZXgnKSk7XHJcbiAgfVxyXG59XHJcblxyXG4vKipcclxuICogV2FzYWJpIENvcmUgbW9kdWxlXHJcbiAqIEluaXRpYWxpemVzOlxyXG4gKiAgIC0gJC5hamF4IChjdXN0b20gaGFuZGxlcnMgdG8gY2hlY2sgbG9naW4gc3RhdHVzIGluIGFqYXggcmVzcG9uc2VzKVxyXG4gKiAgIC0gQmFja2VuZCBVSSBjb21wb25lbnRzIHRoYXQgYXJlIHVzZWQgb24gZXZlcnkgcGFnZVxyXG4gKiAgIC0gcnVucyBmZWF0dXJlIHRlc3RzICh0b3VjaCwgLi4uKSB2aWEgbW9kZXJuaXpyXHJcbiAqICAgLSBzZXRzIHVwIGEgZ2xvYmFsIHRocmVzaG9sZGVkIHdpbmRvdyByZXNpemUgZXZlbnQgaGFuZGxlciB0aGF0XHJcbiAqICAgICBwdWJsaXNoZXMgdGhlIGV2ZW50IHRvIHRoZSBiYWNrYm9uZSB2aWV3cyBhcyAnd2luZG93LnJlc2l6ZSdcclxuICpcclxuICogQHBhcmFtIG9wdGlvbnNcclxuICogQGNvbnN0cnVjdG9yXHJcbiAqL1xyXG5tb2R1bGUuZXhwb3J0cyA9IHtcclxuXHJcbiAgLyoqXHJcbiAgICogQ29yZSBvcHRpb25zXHJcbiAgICogdGhhdCBoYXZlIGJlZW4gc3VwcGxpZWQgdGhyb3VnaCB0aGUgV2FzYWJpIENvcmUgZGVmYXVsdCBsYXlvdXQuXHJcbiAgICpcclxuICAgKiBAdHlwZSB7b2JqZWN0fVxyXG4gICAqL1xyXG4gIG9wdGlvbnM6IHt9LFxyXG5cclxuICAvKipcclxuICAgKiBIb2xkcyBhIHJlZmVyZW5jZSB0byB0aGUgd2luZG93LlxyXG4gICAqXHJcbiAgICogQHR5cGUge3dpbmRvd31cclxuICAgKi9cclxuICB3OiBudWxsLFxyXG5cclxuICAvKipcclxuICAgKiBIb2xkcyBhIHJlZmVyZW5jZSB0byB0aGUgZG9jdW1lbnQuXHJcbiAgICpcclxuICAgKiBAdHlwZSB7SFRNTERvY3VtZW50fVxyXG4gICAqL1xyXG4gIGQ6IG51bGwsXHJcblxyXG4gIC8qKlxyXG4gICAqIEhvbGQgYSByZWZlcmVuY2UgdG8gdGhlIGh0bWwgdGFnXHJcbiAgICpcclxuICAgKiBAdHlwZSB7alF1ZXJ5fVxyXG4gICAqL1xyXG4gICRodG1sOiBudWxsLFxyXG5cclxuICAvKipcclxuICAgKiBIb2xkIGEgcmVmZXJlbmNlIHRvIHRoZSBib2R5IHRhZ1xyXG4gICAqXHJcbiAgICogQHR5cGUge2pRdWVyeX1cclxuICAgKi9cclxuICAkYm9keTogbnVsbCxcclxuXHJcbiAgLyoqXHJcbiAgICogQHR5cGUge0FycmF5PEJhc2VWaWV3Pn1cclxuICAgKi9cclxuICB2aWV3czogW10sXHJcblxyXG4gIC8qKlxyXG4gICAqIEB0eXBlIHtBcnJheTxCYWNrYm9uZS5Db2xsZWN0aW9uPn1cclxuICAgKi9cclxuICBjb2xsZWN0aW9uczogW10sXHJcblxyXG4gIC8qKlxyXG4gICAqIEB0eXBlIHtBcnJheTxCYWNrYm9uZS5Nb2RlbD59XHJcbiAgICovXHJcbiAgbW9kZWxzOiBbXSxcclxuXHJcbiAgLyoqXHJcbiAgICogUmVzaXplIHRpbWVvdXQgZm9yIHdpbmRvdy5yZXNpemUgZXZlbnRzLlxyXG4gICAqXHJcbiAgICogQHR5cGUge251bWJlcn1cclxuICAgKi9cclxuICByZXNpemVUaW1lb3V0OiAwLFxyXG5cclxuICAvKipcclxuICAgKiBAdHlwZSB7b2JqZWN0fVxyXG4gICAqL1xyXG4gIGNvbXBvbmVudHM6IHt9LFxyXG5cclxuICAvKipcclxuICAgKiBJbml0aWFsaXphdGlvbiBvZiB0aGUgd2FzYWJpIGNvcmUgbW9kdWxlLlxyXG4gICAqXHJcbiAgICogQHBhcmFtIG9wdGlvbnNcclxuICAgKi9cclxuICBpbml0aWFsaXplOiBmdW5jdGlvbihvcHRpb25zKSB7XHJcbiAgICB0aGlzLm9wdGlvbnMgPSBvcHRpb25zIHx8IHt9O1xyXG4gICAgdGhpcy53ID0gd2luO1xyXG4gICAgdGhpcy5kID0gZG9jO1xyXG4gICAgdGhpcy4kaHRtbCA9ICQoJ2h0bWwnKTtcclxuICAgIHRoaXMuJGJvZHkgPSAkKCdib2R5Jyk7XHJcblxyXG4gICAgX3NldHVwQWpheC5jYWxsKHRoaXMpO1xyXG4gICAgX2luaXRpYWxpemVCYWNrZW5kVmlld3MuY2FsbCh0aGlzKTtcclxuICAgIF90b2dnbGVFbXB0eVNlbGVjdHMuY2FsbCh0aGlzKTtcclxuICAgIF9pbml0UGFnaW5hdGlvbnMuY2FsbCh0aGlzKTtcclxuICAgIF9pbml0U2VjdGlvbnMuY2FsbCh0aGlzKTtcclxuICB9XHJcbn07XHJcbiJdfQ==
