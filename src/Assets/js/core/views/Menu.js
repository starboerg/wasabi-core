
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
      'window.resize': 'onWindowResize'
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

    $window: null,
    $document: null,
    $wrap: null,

    isIOS: /iPhone|iPad|iPod/.test(navigator.userAgent),

    menuIsPinned: false,
    pinnedMenuTop: false,
    pinnedMenuBottom: false,
    lastScrollPosition: 0,

    /**
     * Initialization of the view.
     *
     * @param {Object=} options
     */
    initialize: function(options) {
      this.options = $.extend({}, defaults, options);
      this.$('> li').filter('.active').prev().addClass('prev-active');
      this.$window = $(window);
      this.$document = $(document);
      this.$wrap = this.$el.parent().parent();
      this.lastScrollPosition = this.$window.scrollTop();
      this.$window.on('scroll', _.bind(this.pinMenu, this));
      this.collapseMenu();
      this.pinMenu();
    },

    onWindowResize: function(event) {
      this.collapseMenu();
      this.pinMenu();
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
        this.$el.find('.' + this.options.selectedClass).removeClass(this.options.selectedClass).find('.sub-nav').css({marginTop: ''});
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

      var $otherOpened = this.$('.' + this.options.selectedClass).filter(function() {
        return $(this)[0] !== $target[0];
      });

      $otherOpened.removeClass(this.options.selectedClass);

      if (this.isCollapsed) {
        var $subnav = $target.find('.sub-nav');
        $subnav.css({marginTop: '', height: ''});
        if (!$target.hasClass(this.options.selectedClass)) {
          $target.addClass(this.options.selectedClass);
          this.adjustPopoutMenu($target);
          this.$document.on('click', _.bind(this.hideSubnav, this));
        } else {
          $target.removeClass(this.options.selectedClass);
          this.$document.off('click', _.bind(this.hideSubnav, this));
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
        var $menuItem = this.$('.' + this.options.selectedClass);
        $menuItem.removeClass(this.options.selectedClass);
        $menuItem.find('.sub-nav').css('margin-top', '');
      }
    },

    adjustPopoutMenu: function($menuItem) {
      var $submenu = $menuItem.find('.sub-nav');
      var offset = $submenu.offset();
      var adjustment;

      if (offset) {
        var offsetTop = offset.top;
        var height = $submenu.outerHeight();
        var windowHeight = this.$window.height();

        if (offsetTop + height > windowHeight) {
          adjustment = offsetTop + height - windowHeight - this.$window.scrollTop();
        }
      }

      if ( adjustment > 1 ) {
        $submenu.css( 'margin-top', '-' + adjustment + 'px' );
      } else {
        $submenu.css( 'margin-top', '' );
      }
    },

    pinMenu: function(event) {
      var windowPos = this.$window.scrollTop();
      var resizing = !event || (event.type !== 'scroll');
      var menuTop;

      if ($('body').hasClass('backend-nav--is-visible')) {
        return;
      }

      if (this.isIOS) {
        return;
      }

      var headerHeight = $('#page-header').outerHeight();
      var menuHeight = this.$el.outerHeight();
      var windowHeight = this.$window.height();
      var documentHeight = this.$document.height();

      if (menuHeight + headerHeight < windowHeight) {
        this.unpinMenu();
        return;
      }

      this.menuIsPinned = true;

      if (menuHeight + headerHeight > windowHeight) {

        // Check for overscrolling
        if (windowPos < 0) {
          if (!this.pinnedMenuTop) {
            this.pinnedMenuTop = true;
            this.pinnedMenuBottom = false;

            this.$wrap.css({
              position: 'fixed',
              top: '',
              bottom: ''
            });
          }
          return;
        } else if (windowPos + windowHeight > documentHeight - 1) {
          if (!this.pinnedMenuBottom) {
            this.pinnedMenuBottom = true;
            this.pinnedMenuTop = false;

            this.$wrap.css({
              position: 'fixed',
              top: '',
              bottom: 0
            });
          }
          return;
        }

        if (windowPos > this.lastScrollPosition) {
          // Scrolling down
          if (this.pinnedMenuTop) {
            // let it scroll
            this.pinnedMenuTop = false;
            menuTop = this.$wrap.offset().top - headerHeight - (windowPos - this.lastScrollPosition);

            if (menuTop + menuHeight + headerHeight < windowPos + windowHeight) {
              menuTop = windowPos + windowHeight - menuHeight - headerHeight;
            }

            this.$wrap.css({
              position: 'absolute',
              top: menuTop,
              bottom: ''
            });
          } else if (!this.pinnedMenuBottom && this.$wrap.offset().top + menuHeight < windowPos + windowHeight) {
            // pin the bottom
            this.pinnedMenuBottom = true;

            this.$wrap.css({
              position: 'fixed',
              top: '',
              bottom: 0
            });
          }
        } else if (windowPos < this.lastScrollPosition) {
          // Scrolling up
          if (this.pinnedMenuBottom) {
            // let it scroll
            this.pinnedMenuBottom = false;
            menuTop = this.$wrap.offset().top - headerHeight + (this.lastScrollPosition - windowPos);

            if (menuTop + menuHeight > windowPos + windowHeight) {
              menuTop = windowPos;
            }

            this.$wrap.css({
              position: 'absolute',
              top: menuTop,
              bottom: ''
            });
          } else if (!this.pinnedMenuTop && this.$wrap.offset().top >= windowPos + headerHeight) {
            // pin the top
            this.pinnedMenuTop = true;

            this.$wrap.css({
              position: 'fixed',
              top: '',
              bottom: ''
            });
          }
        } else if (resizing) {
          // Resizing
          this.pinnedMenuTop = this.pinnedMenuBottom = false;
          menuTop = windowPos + windowHeight - menuHeight - headerHeight - 1;

          if (menuTop > 0) {
            this.$wrap.css({
              position: 'absolute',
              top: menuTop,
              bottom: ''
            });
          } else {
            this.unpinMenu();
          }
        }

      }

      this.lastScrollPosition = windowPos;
    },

    unpinMenu: function() {
      if (this.isIOS || !this.menuIsPinned) {
        return;
      }

      this.pinnedMenuTop = this.pinnedMenuBottom = this.menuIsPinned = false;
      this.$wrap.css({
        position: '',
        top: '',
        bottom: ''
      });
    }

  });
});
