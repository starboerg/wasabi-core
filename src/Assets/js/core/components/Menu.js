import _ from 'underscore';
import $ from 'jquery';
import {View} from 'backbone.marionette';
import Tether from 'tether';
import '../vendor/bootstrap/collapse';

/**
 * Default options.
 *
 * @type {{collapsedClass: string, openClass: string, popoutClass: string}}
 */
const defaults = {
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
const _getEventTarget = (event, cls) => {
  let $target = $(event.target);
  if (!$target.is('li')) {
    cls = cls || '';
    return $target.closest('li' + cls);
  }

  return $target;
};

/**
 * Menu Component
 */
const Menu = View.extend({

  template: false,

  /**
   * Registered events of this view.
   *
   * @returns {Object}
   */
  events: {
    'click > li:has(ul) > a': 'onMainItemClick'
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
   * @type {Object}
   */
  options: {},

  /**
   * Holds a reference to the DOM Document jQuery object.
   * @type {jQuery}
   */
  $document: null,

  /**
   * Holds a reference to scroll container.
   * @type {jQuery}
   */
  $scrollContainer: null,

  /**
   * Holds a reference to the subnav tether instance.
   * @type {Tether}
   */
  subnavTether: null,

  /**
   * Holds the subnav clone.
   * @type {jQuery}
   */
  $subnavClone: null,

  /**
   * Initialization of the view.
   *
   * @param {Object=} options
   */
  initialize (options) {
    this.options = $.extend({}, defaults, options);
    this.eventBus = options.eventBus;
    this.$document = $(document);
    this.$scrollContainer = this.$el.closest('.gm-scrollbar-container');

    this.eventBus.on('window-resize', this.onWindowResize.bind(this));
    this.eventBus.on('sidebar.toggle-collapse', this.onToggleCollapse.bind(this));

    this.render();
  },

  onRender () {
    this.collapseMenu();
  },

  onWindowResize (event) {
    this.collapseMenu();
  },

  /**
   * Attaches/detaches event handlers if the navigation is collapsed
   * and toggles the .collapsed class.
   *
   * The visuals of the collapsed navigation are done via media queries and not via JS.
   */
  collapseMenu () {
    this.isCollapsed = (
      this.isForceCollapsed ||
      this.$document.find('body').hasClass('sidebar-is-collapsed') ||
      this.getStyledContent() === 'collapsed'
    );

    if (!this.isCollapsed && this.subnavTether !== null) {
      this.subnavTether.destroy();
      this.$subnavClone.remove();
    }
  },

  onToggleCollapse (collapsed) {
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
  onMainItemClick (event) {
    let $target = _getEventTarget(event);
    let $subnav = $target.find('> ul');

    if ($subnav.length === 0) {
      return;
    }

    event.preventDefault();
    event.stopPropagation();

    let $otherOpened = this.$('.' + this.options.selectedClass).filter(function() {
      return $(this)[0] !== $target[0];
    });

    $otherOpened.removeClass(this.options.selectedClass);

    if (this.isCollapsed && this.getStyledContent() !== 'open') {
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

  hideSubnav () {
    this.$('.' + this.options.selectedClass).removeClass(this.options.selectedClass);
    if (this.subnavTether !== null) {
      this.subnavTether.destroy();
      this.$subnavClone.remove();
      this.$document.off('click', _.bind(this.hideSubnav, this));
    }
  },

  getStyledContent () {
    let selector = this.$el
      .parents()
      .map(function() { return this.tagName; })
      .get()
      .reverse()
      .concat([this.el.nodeName])
      .join('>');

    return window.getComputedStyle(document.querySelector(selector), '::before').getPropertyValue('content').replace(/'/g, '').replace(/"/g, '')
  }
});

export default Menu;
