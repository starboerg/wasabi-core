define(function(require) {

  var _ = require('underscore');
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
    forceOpenClass: 'sidebar-is-open'
  };

  return BaseView.extend({

    /**
     * The element of this view.
     *
     * @type {string} CSS selector
     */
    el: '.sidebar--navigation-toggle',

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
      this.$sidebar = $('.sidebar');
    },

    /**
     * toggleNav event handler
     * Toggles navClosedClass on the body to show/hide the navigation.
     */
    toggleNav: function(event) {
      if (!$body.hasClass(this.options.forceOpenClass)) {
        $body.addClass(this.options.forceOpenClass);
        this.$sidebar.fadeIn();
        setTimeout(_.bind(function() {
          this.$sidebar.find('.gm-scrollbar-container').data('scrollbar').update();
        }, this), 0);
      } else {
        $body.removeClass(this.options.forceOpenClass);
        this.$sidebar.fadeOut(_.bind(function() {
          this.$sidebar.attr('style', '');
        }, this));
      }
    }
  });
});
