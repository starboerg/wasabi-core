define(function(require) {
  var $ = require('jquery');
  var _ = require('underscore');
  var MenuView = require('core/views/Menu');
  var NavigationToggleView = require('core/views/NavigationToggle');
  var LangSwitchView = require('core/views/LangSwitch');
  var PaginationView = require('core/views/Pagination');
  var LoginModalView = require('core/views/LoginModal');
  var ModalView = require('common/ModalView');
  var TabView = require('common/TabView');
  var WS = require('wasabi');
  require('jquery.livequery');

  var win = window;
  var doc = document;

  /**
   * Setup default ajax options and global ajax event handlers.
   *
   * @private
   */
  function _setupAjax() {
    $.ajaxSetup({
      dataType: 'json',
      beforeSend: _.bind(function(xhr, options) {
        if (!options.heartBeat) {
          _initHeartBeat.call(this);
        }
      }, this)
    });
    $(doc)
      .ajaxSuccess(_.bind(_onAjaxSuccess, this))
      .ajaxError(_.bind(_onAjaxError, this));
  }

  /**
   * Default ajax success handler.
   * Displays a flash message if the response contains
   * {
   *   'status': '...' # the class of the flash message
   *   'flashMessage': '...' # the text of the flash message
   * }
   *
   * @param event
   * @param xhr
   * @private
   */
  function _onAjaxSuccess(event, xhr) {
    if (xhr.status == 200 && xhr.statusText == 'OK') {
      var contentType = xhr.getResponseHeader('Content-Type');
      if (contentType.split('application/json').length === 2) {
        var data = $.parseJSON(xhr.responseText) || {};

        if (typeof data.status !== 'undefined') {
          if (typeof data.flash !== 'undefined') {
            _flashMessage(data.flash);
          }
          if (typeof data.redirect !== 'undefined') {
            win.location = data.redirect;
          }
        }
      }
    }
  }

  /**
   * Default ajax error handler.
   *
   * @param event
   * @param xhr
   * @private
   */
  function _onAjaxError(event, xhr) {
    var data;
    if (xhr.status == 401) {
      data = $.parseJSON(xhr.responseText) || {};
      if (typeof data.name !== 'undefined') {
        if (confirm(data.name)) {
          win.location.reload();
        } else {
          win.location.reload();
        }
      }
    }
    if (xhr.status == 403) {
      _authError.call(this);
    }
    if (xhr.status == 500) {
      data = $.parseJSON(xhr.responseText) || {};
      if (typeof data.name !== 'undefined') {
        _flashMessage({
          before: 'div.title-pad',
          class: 'error',
          message: data.name
        });
      }
    }
  }

  function _detectFeatures() {

    function getScrollbarWidth() {
      var $scrollDetector = $('<div/>').css({
        width: '100px',
        height: '100px',
        overflow: 'scroll',
        position: 'absolute',
        top: '-9999px'
      });
      var $body = $('body');
      $scrollDetector.prependTo($body);
      var scrollbarWidth = 0;
      setTimeout(function() {
        scrollbarWidth = $scrollDetector[0].offsetWidth - $scrollDetector[0].clientWidth;
      }, 0);
      $scrollDetector.remove();
      return scrollbarWidth;
    }

    WS.features.scrollbarWidth = getScrollbarWidth();
  }

  /**
   * Render a flash message after a specific element.
   *
   * @param {Object} flash The flash object returned by the ajax request.
   * @private
   */
  function _flashMessage(flash) {
    var insertBefore = flash.before || false;
    var insertAfter = flash.after || false;
    var message = flash.message;
    var cls = (flash.class) ? 'flash-' + flash.class : 'flash-success';
    var $target = false;
    var $flash = $('<div/>').addClass(cls).html(message);

    if (insertBefore) {
      $target = $(insertBefore);
      $target.prev('.' + cls).remove();
      $flash.insertBefore($target);
    } else if (insertAfter) {
      $target = $(insertAfter);
      $target.next('.' + cls).remove();
      $flash.insertAfter($target);
    }
  }

  function _initializeBackendViews() {
    WS.views.menu = WS.createView(MenuView);
    WS.views.navigationToggle = WS.createView(NavigationToggleView);
    WS.views.langSwitch = WS.createView(LangSwitchView);
    WS.views.paginations = WS.createViews($('.pagination'), PaginationView);
  }

  function _toggleEmptySelects() {
    $('select').livequery(function() {
      $(this).toggleClass('empty', $(this).val() === '');
      $(this).change(function() {
        $(this).toggleClass('empty', $(this).val() === '');
      });
    });
  }

  /**
   * Initialize modal dialogs
   *
   * @private
   */
  function _initModals() {
    var that = this;
    WS.views.modals = [];
    $('[data-toggle="modal"], [data-toggle="confirm"]').livequery(function() {
      WS.views.modals.push(WS.createView(ModalView, {
        el: $(this),
        scrollbarWidth: WS.features.scrollbarWidth,
        confirmYes: that.options.translations.confirmYes,
        confirmNo: that.options.translations.confirmNo
      }));
    });
  }

  function _initTabs() {
    $('.tabs[data-tabify-id]').livequery(function() {
      WS.views.push(WS.createView(TabView, {
        el: $(this)
      }));
    });
  }

  function _initInteractionDropdownMenus() {
    this.$body.on('click', '.dropdown-interaction', function(event) {
      event.stopPropagation();
    });
  }

  function _initSections() {
    if (this.$body.hasClass('wasabi-core--languages-index')) {
      WS.createView(require('core/sections/LanguagesIndex'));
    }
    if (this.$body.hasClass('wasabi-core--grouppermissions-index')) {
      WS.createView(require('core/sections/GroupPermissionsIndex'));
    }
  }

  function _handleHeartBeatResponse(data) {
    if (data.status === 401) {
      _authError.call(this);
    } else {
      _initHeartBeat.call(this);
    }
  }

  function _authError() {
    clearTimeout(this.heartBeatTimeout);

    // open login modal
    this.loginModal.openModal();
  }

  function _initHeartBeat() {
    if (!this.loginModal) {
      this.loginModal = WS.createView(LoginModalView, {});
    }

    clearTimeout(this.heartBeatTimeout);
    this.heartBeatTimeout = setTimeout(_.bind(function() {
      $.ajax({
        url: this.options.heartbeatEndpoint,
        method: 'post',
        type: 'json',
        success: _.bind(_handleHeartBeatResponse, this),
        error: _.bind(_authError, this),
        heartBeat: true
      });
    }, this), this.options.heartbeat);
  }

  /**
   * Wasabi Core module
   * Initializes:
   *   - $.ajax (custom handlers to check login status in ajax responses)
   *   - Backend UI components that are used on every page
   *   - runs feature tests (touch, ...) via modernizr
   *   - sets up a global thresholded window resize event handler that
   *     publishes the event to the backbone views as 'window.resize'
   *
   * @param options
   * @constructor
   */
  return {

    /**
     * Core options
     * that have been supplied through the Wasabi Core default layout.
     *
     * @type {object}
     */
    options: {},

    /**
     * Holds a reference to the window.
     *
     * @type {window}
     */
    w: null,

    /**
     * Holds a reference to the document.
     *
     * @type {HTMLDocument}
     */
    d: null,

    /**
     * Hold a reference to the html tag
     *
     * @type {jQuery}
     */
    $html: null,

    /**
     * Hold a reference to the body tag
     *
     * @type {jQuery}
     */
    $body: null,

    /**
     * @type {Array<BaseView>}
     */
    views: [],

    /**
     * @type {Array<Backbone.Collection>}
     */
    collections: [],

    /**
     * @type {Array<Backbone.Model>}
     */
    models: [],

    /**
     * HearBeat timeout to keep the session alive.
     *
     * @type {number}
     */
    heartBeatTimeout: 0,

    /**
     * Resize timeout for window.resize events.
     *
     * @type {number}
     */
    resizeTimeout: 0,

    /**
     * @type {object}
     */
    components: {},

    /**
     * Holds a reference to the login modal.
     */
    loginModal: null,

    /**
     * Initialization of the wasabi core module.
     *
     * @param options
     */
    initialize: function(options) {
      this.options = options || {};
      this.w = win;
      this.d = doc;
      this.$html = $('html');
      this.$body = $('body');

      _setupAjax.call(this);
      _detectFeatures.call(this);
      _initializeBackendViews.call(this);
      _toggleEmptySelects.call(this);
      _initModals.call(this);
      _initTabs.call(this);
      _initSections.call(this);
      _initInteractionDropdownMenus.call(this);
      _initHeartBeat.call(this);
    },

    initHeartBeat: function() {
      _initHeartBeat.call(this);
    }
  };
});
