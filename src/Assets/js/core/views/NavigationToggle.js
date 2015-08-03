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
    forceOpenClass: 'nav-open'
  };

  return BaseView.extend({

    /**
     * The element of this view.
     *
     * @type {string} CSS selector
     */
    el: 'body > header .nav-toggle',

    /**
     * Registered events of this view.
     *
     * @type {Object}
     */
    events: {
      'click': 'toggleNav'
    },

    globalEvents: {
      'window.resize': 'onResize'
    },

    /**
     * Determines if the navigation bar is forced open.
     *
     * @type {boolean}
     */
    isOpened: false,

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
      if (!this.isOpened && $('#asidebg').css('display') === 'none') {
        $body.addClass(this.options.forceOpenClass);
        this.isOpened = true;
      } else {
        $body.removeClass(this.options.forceOpenClass);
        this.isOpened = false;
      }
    },

    onResize: function() {
      //if (!this.isClosed && $('#asidebg').css('display') === 'none') {
      //  $body.addClass(this.options.navClosedClass);
      //  this.isClosed = true;
      //}
    }
  });
});
