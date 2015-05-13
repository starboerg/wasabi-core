define(function(require) {
  var $ = require('jquery');
  var BaseView = require('wasabi.BaseView');
  var WS = require('wasabi');

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

  return BaseView.extend({

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
});
