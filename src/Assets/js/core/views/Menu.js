
define(function(require) {
  var $ = require('jquery');
  var BaseView = require('common/BaseView');
  var Tether = require('tether');
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
    el: '.sidebar--menu',

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
      'window.resize': 'onWindowResize',
      'sidebar.toggle-collapse': 'onToggleCollapse'
    },

    /**
     * Determines if the navigation bar is collapsed to the small size (true) or not (false).
     *
     * @type {boolean}
     */
    isCollapsed: false,

    /**
     * Determines wheter the sidebar is collapsed by an outer component.
     */
    isForceCollapsed: false,

    /**
     * Options
     *
     * @param {Object}
     */
    options: {},

    $document: null,
    $scrollContainer: null,

    subnavTether: null,
    $subnavClone: null,

    /**
     * Initialization of the view.
     *
     * @param {Object=} options
     */
    initialize: function(options) {
      this.options = $.extend({}, defaults, options);
      this.$document = $(document);
      this.$scrollContainer = this.$el.closest('.gm-scrollbar-container');
      this.collapseMenu();
    },

    onWindowResize: function(event) {
      this.collapseMenu();
    },

    /**
     * Attaches/detaches event handlers if the navigation is collapsed
     * and toggles the .collapsed class.
     *
     * The visuals of the collapsed navigation are done via media queries and not via JS.
     */
    collapseMenu: function() {
      var content = window.getComputedStyle(document.querySelector(this.$el.selector), '::before').getPropertyValue('content').replace(/'/g, "").replace(/"/g, "");
      this.isCollapsed = (this.isForceCollapsed || this.$document.find('body').hasClass('sidebar-is-collapsed') || content === 'collapsed');

      if (!this.isCollapsed && this.subnavTether !== null) {
        this.subnavTether.destroy();
        this.$subnavClone.remove();
      }
    },

    onToggleCollapse: function(collapsed) {
      this.isForceCollapsed = collapsed;
      this.isCollapsed = collapsed;

      if (!collapsed && this.subnavTether !== null) {
        this.subnavTether.destroy();
        this.$subnavClone.remove();
      }

      if (this.$scrollContainer.data('scrollbar')) {
        this.$scrollContainer.data('scrollbar').update();
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
      var $subnav = $target.find('> ul');

      if ($subnav.length === 0) {
        return;
      }

      event.preventDefault();
      event.stopPropagation();

      var $otherOpened = this.$('.' + this.options.selectedClass).filter(function() {
        return $(this)[0] !== $target[0];
      });

      $otherOpened.removeClass(this.options.selectedClass);

      if (this.isCollapsed && !this.$document.find('body').hasClass('sidebar-is-open')) {
        if ($target.hasClass(this.options.selectedClass)) {
          this.hideSubnav();
        } else {
          this.hideSubnav();
          $target.addClass(this.options.selectedClass);
          this.$subnavClone = $subnav.clone().appendTo('body');
          this.subnavTether = new Tether({
            element: this.$subnavClone,
            target: $target,
            attachment: 'top left',
            targetAttachment: 'top right',
            constraints: [
              {
                to: this.$document.find('#content').get(0),
                pin: true
              }
            ]
          });
          this.$document.on('click', _.bind(this.hideSubnav, this));
        }
      } else {
        if (!$target.hasClass(this.options.openClass)) {
          $target.addClass(this.options.openClass);
          $subnav.one('shown.bs.collapse', _.bind(function() {
            setTimeout(_.bind(function() {
              if (this.$scrollContainer.data('scrollbar')) {
                this.$scrollContainer.data('scrollbar').update();
              }
            }, this), 0);
          }, this));
          $subnav.collapse('show');
        } else {
          $target.removeClass(this.options.openClass);
          $subnav.one('hidden.bs.collapse', _.bind(function() {
            setTimeout(_.bind(function() {
              if (this.$scrollContainer.data('scrollbar')) {
                this.$scrollContainer.data('scrollbar').update();
              }
            }, this), 0);
          }, this));
          $subnav.collapse('hide');
        }
      }
    },

    hideSubnav: function() {
      this.$('.' + this.options.selectedClass).removeClass(this.options.selectedClass);
      if (this.subnavTether !== null) {
        this.subnavTether.destroy();
        this.$subnavClone.remove();
        this.$document.off('click', _.bind(this.hideSubnav, this));
      }
    }

  });
});
