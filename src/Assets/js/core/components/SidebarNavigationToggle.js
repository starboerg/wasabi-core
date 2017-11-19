import _ from 'underscore';
import $ from 'jquery';
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
 * @type {{forceOpenClass: string}}
 */
const defaults = {
  forceOpenClass: 'sidebar-is-open'
};

const SidebarNavigationToggle = View.extend({

  template: false,

  /**
   * Registered events of this view.
   *
   * @type {Object}
   */
  events: {
    'click': 'toggleNav'
  },

  /**
   * Initialization of the view.
   *
   * @param {Object=} options
   */
  initialize (options) {
    this.options = $.extend({}, defaults, options);
    this.$sidebar = $('.sidebar');
  },

  /**
   * toggleNav event handler
   * Toggles navClosedClass on the body to show/hide the navigation.
   */
  toggleNav (event) {
    if (!$body.hasClass(this.options.forceOpenClass)) {
      $body.addClass(this.options.forceOpenClass);
      setTimeout(_.bind(function() {
        let scrollbar = this.$sidebar.find('.gm-scrollbar-container').data('scrollbar');
        if (typeof scrollbar !== 'undefined') {
          scrollbar.update();
        }
      }, this), 0);
    } else {
      this.$sidebar.css({
        left: 0,
        width: '100%'
      });
      $body.removeClass(this.options.forceOpenClass);
      setTimeout(_.bind(function() {
        this.$sidebar.css({
          left: '',
          width: ''
        });
      }, this), 200);
    }
  }
});

export default SidebarNavigationToggle;
