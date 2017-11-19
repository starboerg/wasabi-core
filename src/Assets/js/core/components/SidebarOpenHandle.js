import $ from 'jquery';
import Cookies from 'js-cookie';
import {View} from 'backbone.marionette';

/**
 * Holds a reference to the body.
 *
 * @type {jQuery}
 */
let $body = $('body');

/**
 * Default options.
 *
 * @type {{forceCollapsedClass: string}}
 */
const defaults = {
  forceCollapsedClass: 'sidebar-is-collapsed'
};

const SidebarOpenHandle = View.extend({

  template: false,

  /**
   * Registered events of this view.
   *
   * @type {Object}
   */
  events: {
    'click': 'toggleCollapse'
  },

  /**
   * Initialization of the view.
   *
   * @param {Object=} options
   */
  initialize (options) {
    this.options = $.extend({}, defaults, options);
    this.eventBus = options.eventBus;

    this.render();
  },

  /**
   * toggleCollapse event handler
   * Toggles forceCollapsedClass on the body to collapse/uncollapse the navigation.
   */
  toggleCollapse () {
    if (!$body.hasClass(this.options.forceCollapsedClass)) {
      $body.addClass(this.options.forceCollapsedClass);
      Cookies.set('sidebar-collapsed', 1, {expires: 365});
      this.eventBus.trigger('sidebar.toggle-collapse', true);
    } else {
      $body.removeClass(this.options.forceCollapsedClass);
      Cookies.set('sidebar-collapsed', 0, {expires: 365});
      this.eventBus.trigger('sidebar.toggle-collapse', false);
    }
  }
});

export default SidebarOpenHandle;
