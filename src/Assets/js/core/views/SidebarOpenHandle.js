define(function(require) {

  var $ = require('jquery');
  var BaseView = require('common/BaseView');
  var WS = require('wasabi');
  var Cookies = require('js-cookie');

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
    forceCollapsedClass: 'sidebar-is-collapsed'
  };

  return BaseView.extend({

    /**
     * The element of this view.
     *
     * @type {string} CSS selector
     */
    el: '.sidebar--open-handle',

    /**
     * Registered events of this view.
     *
     * @type {Object}
     */
    events: {
      'click': 'collapseToggleNav'
    },

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
    },

    /**
     * collapseToggleNav event handler
     * Toggles forceCollapsedClass on the body to collapse/uncollapse the navigation.
     */
    collapseToggleNav: function() {
      if (!$body.hasClass(this.options.forceCollapsedClass)) {
        $body.addClass(this.options.forceCollapsedClass);
        Cookies.set('sidebar-collapsed', 1, {expires: 365});
        WS.eventBus.trigger('sidebar.toggle-collapse', true);
      } else {
        $body.removeClass(this.options.forceCollapsedClass);
        Cookies.set('sidebar-collapsed', 0, {expires: 365});
        WS.eventBus.trigger('sidebar.toggle-collapse', false);
      }
    }
  });
});
