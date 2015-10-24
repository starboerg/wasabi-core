define(function(require) {
  var $ = require('jquery');
  var BaseView = require('common/BaseView');

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
    forceOpenClass: 'backend-nav--is-visible'
  };

  return BaseView.extend({

    /**
     * The element of this view.
     *
     * @type {string} CSS selector
     */
    el: '#page-header .nav-toggle',

    /**
     * Registered events of this view.
     *
     * @type {Object}
     */
    events: {
      'click': 'toggleNav'
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
     * toggleNav event handler
     * Toggles navClosedClass on the body to show/hide the navigation.
     */
    toggleNav: function() {
      if (!$body.hasClass(this.options.forceOpenClass)) {
        $body.addClass(this.options.forceOpenClass);
      } else {
        $body.removeClass(this.options.forceOpenClass);
      }
    }
  });
});
