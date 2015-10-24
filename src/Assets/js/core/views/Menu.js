define(function(require) {
  var $ = require('jquery');
  var BaseView = require('common/BaseView');
  var WS = require('wasabi');
  require('bootstrap.collapse');

  /**
   * Default options.
   *
   * @type {{collapsedClass: string, openClass: string, popoutClass: string}}
   */
  var defaults = {
    collapsedClass: 'collapsed',
    openClass: 'open',
    selectedClass: 'popout'
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
    events: {
      'click > li:has(ul) > a': 'onMainItemClick'
    },

    /**
     * Registered global event handlers.
     */
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
      var content = window.getComputedStyle(document.querySelector('#backend-menu'), '::before').getPropertyValue('content').replace(/'/g, "").replace(/"/g, "");
      this.isCollapsed = (content === 'collapsed');
      this.$el.toggleClass(this.options.collapsedClass, this.isCollapsed);
      if (!this.isCollapsed) {
        this.$el.find('.' + this.options.selectedClass).removeClass(this.options.selectedClass);
      }
    },

    /**
     * onMainItemClick event handler
     * Toggles the .open class on every li that has a child ul.
     *
     * @param {Object} event
     */
    onMainItemClick: function(event) {
      var $target = _getEventTarget(event);

      if ($target.find('.sub-nav').length === 0) {
        return;
      }

      event.preventDefault();
      event.stopPropagation();

      this.$('.' + this.options.selectedClass).removeClass(this.options.selectedClass);

      if (this.isCollapsed) {
        if (!$target.hasClass(this.options.selectedClass)) {
          $target.addClass(this.options.selectedClass);
          $(document).on('click', _.bind(this.hideSubnav, this));
        } else {
          $target.removeClass(this.options.selectedClass);
          $(document).off('click', _.bind(this.hideSubnav, this));
        }
      } else {
        if (!$target.hasClass(this.options.openClass)) {
          $target.addClass(this.options.openClass);
          $target.find('.sub-nav').collapse('show');
        } else {
          $target.removeClass(this.options.openClass);
          $target.find('.sub-nav').collapse('hide');
        }
      }
    },

    hideSubnav: function(event) {
      var $target = _getEventTarget(event);
      if ($target.parents('.'+ this.options.selectedClass).length === 0) {
        this.$('.' + this.options.selectedClass).removeClass(this.options.selectedClass)
      }
    }

  });
});
