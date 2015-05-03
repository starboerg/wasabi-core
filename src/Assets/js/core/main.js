var $ = require('jquery');
var _ = require('underscore');
var cNavigation = require('./components/navigation/navigation');
var win = window;
var doc = document;
var WS = global.window.WS;

/**
 * Setup default ajax options and global ajax event handlers.
 *
 * @private
 */
function _setupAjax() {
  $.ajaxSetup({
    dataType: 'json'
  });
  $(doc)
    .ajaxSuccess(_onAjaxSuccess)
    .ajaxError(_onAjaxError);
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
  this.components.navigation = new cNavigation();
}

function _toggleEmptySelects() {
  require('jquery.livequery');
  $('select').livequery(function() {
    $(this).toggleClass('empty', $(this).val() === '');
    $(this).change(function() {
      $(this).toggleClass('empty', $(this).val() === '');
    });
  });
}

function _initPaginations() {
  var Pagination = require('./components/pagination/pagination');
  WS.createViews($('.pagination'), Pagination);
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
module.exports = {

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
    _initializeBackendViews.call(this);
    _toggleEmptySelects.call(this);
    _initPaginations.call(this);
  }
};
