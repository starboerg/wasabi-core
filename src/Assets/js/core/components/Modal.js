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
 * Holds a reference to the document.
 *
 * @type {jQuery}
 */
let $document = $(document);

/**
 * Holds a reference to the window.
 *
 * @type {jQuery}
 */
let $window = $(window);

/**
 * Default settings.
 *
 * @type {Object}
 */
const defaults = {
  width: 400,
  offsetTop: 200,
  opacity: 0.35,
  padding: 20,
  cssClasses: {
    backdrop: 'modal-backdrop',
    scrollable: 'modal-scrollable',
    container: 'modal-container',
    modalHeader: 'modal-header',
    modalBody: 'modal-body',
    modalFooter: 'modal-footer',
    confirmFooter: 'modal-confirm'
  },
  confirmYes: 'Yes',
  confirmNo: 'No',
  closeOnScrollable: true,
  closeOnSubmit: true
};

const Modal = View.extend({

  /**
   * The template used to render a modal view.
   */
  template: false,

  /**
   * Holds a reference to the modal backdrop element.
   *
   * @type {jQuery}
   */
  $backdrop: null,

  /**
   * Holds a reference to the modal container element.
   *
   * @type {jQuery}
   */
  $container: null,

  /**
   * Holds references to all close buttons ([data-dismiss="modal"]) of the modal.
   *
   * @type {jQuery}
   */
  $closeButtons: null,

  /**
   * Holds the modal content including backdrop, scrollable wrapper and the modal container.
   *
   * @type {jQuery}
   */
  $modal: null,

  /**
   * An optional element on which modal interaction events should be triggered on.
   *
   * @type {jQuery}
   */
  $notify: null,

  /**
   * Holds a reference to the modal scrollable wrapper of the container.
   *
   * @type {jQuery}
   */
  $scrollable: null,

  /**
   * Holds references to all submit buttons (button[type="submit"]) of the modal.
   *
   * @type {jQuery}
   */
  $submitButtons: null,

  /**
   * Determines if the modal dialogue is a confirm dialogue (true) or not (false).
   *
   * @type {boolean}
   */
  isConfirmModal: false,

  /**
   * Tracks if the modal is currently opened (true) or not (false).
   *
   * @type {boolean}
   */
  isOpened: false,

  /**
   * The method for ajax modals that should submit data.
   *
   * @type {null|string}
   */
  method: null,

  /**
   * The event name that should be triggered on
   * the $notify element on modal success.
   *
   * @type {null|string}
   */
  modalSuccessEvent: null,

  /**
   * The name of the modal
   *
   * @type {null|string}
   */
  modalName: null,

  /**
   * Registered events of the modal view.
   *
   * @type {Object}
   */
  events: {
    'click': 'openModal'
  },

  /**
   * Initialization of the Modal View.
   *
   * @param {Object=} options
   */
  initialize (options) {
    this.options = $.extend({}, defaults, options);
    this.eventBus = options.eventBus;
    this.onResize = this.onResize.bind(this);

    this.render();
  },

  onRender () {
    this.template = _.template($('#wasabi-core-modal').html());
    this.eventBus.on('window-resize', this.onResize);
  },

  /**
   * Initialize the modal options.
   */
  initModalOptions () {
    this.method = this.$el.attr('data-modal-method') || 'post';
    this.isAjax = (this.$el.attr('data-modal-ajax') === '1' || this.$el.attr('data-modal-ajax') === 'true');
    this.action = this.$el.attr('data-modal-action') || this.$el.attr('href') || false;

    if (this.isAjax) {
      let notify = this.$el.attr('data-modal-notify');
      this.$notify = (notify !== undefined) ? $(notify) : $document;
      this.modalSuccessEvent = this.$el.attr('data-modal-event') || 'modal:success';
    }

    let modalWidth = parseInt(this.$el.attr('data-modal-width'));

    if (modalWidth) {
      this.options.width = modalWidth;
    }

    if (typeof this.$el.attr('data-modal-close-on-scrollable') !== 'undefined') {
      this.options.closeOnScrollable = parseInt(this.$el.attr('data-modal-close-on-scrollable'));
    }

    if (typeof this.$el.attr('data-modal-close-on-submit') !== 'undefined') {
      this.options.closeOnSubmit = parseInt(this.$el.attr('data-modal-close-on-submit'));
    }

    this.options.forceCloseModalLink = parseInt(this.$el.attr('data-force-close-modal-link'));
  },

  /**
   * Configure the template options that are used to
   * render the modalTemplate (hbs).
   *
   * @returns {Object}
   */
  createTemplateOptions () {
    let options = {}, hasTarget, $target;

    options.modalHeader = this.$el.attr('data-modal-header') || this.options.modalHeader;
    options.hasHeader = (options.modalHeader !== undefined);

    options.modalBody = this.$el.attr('data-modal-body') || this.options.modalBody;
    options.hasBody = (options.modalBody !== undefined);

    if (!options.hasBody) {
      $target = $(this.$el.attr('data-modal-target'));
      hasTarget = ($target.length > 0);
      if (hasTarget) {
        options.modalBody = $target.html();
        options.hasBody = true;
      }
    }

    options.isConfirmModal = (this.$el.attr('data-toggle') === 'confirm');
    if (options.isConfirmModal) {
      options.hasFooter = true;
    } else {
      options.modalFooter = this.$el.attr('data-modal-footer');
      options.hasFooter = (options.modalFooter !== undefined);
    }

    options.action = this.action;
    options.method = this.method;
    options.cssClasses = this.options.cssClasses;
    options.confirmYes = this.options.confirmYes;
    options.confirmNo = this.options.confirmNo;
    options.forceCloseModalLink = this.options.forceCloseModalLink;

    options.hasCloseLink = !options.isConfirmModal || options.forceCloseModalLink;

    return options;
  },

  /**
   * Initialize all modal elements.
   */
  initModalElements () {
    this.$modal = $(this.template(this.createTemplateOptions()));

    this.$backdrop = this.$modal.find('.' + this.options.cssClasses.backdrop);
    this.$backdrop.css({
      opacity: this.options.opacity
    });

    this.$scrollable = this.$modal.find('.' + this.options.cssClasses.scrollable);

    this.$container = this.$modal.find('.' + this.options.cssClasses.container);
    this.$container.css({
      width: this.options.width,
      position: 'fixed',
      left: -99999,
      top: this.options.offsetTop,
      opacity: 0,
      zIndex: 10000
    });

    this.$closeButtons = this.$modal.find('[data-dismiss="modal"]');
    this.$submitButtons = this.$modal.find('button[type="submit"]');
  },

  /**
   * Update the modal position.
   */
  updatePosition () {
    let modalHeight = this.$container.outerHeight();
    let modalOffsetTop = this.$container.offset().top - this.$scrollable.offset().top;
    let screenHeight = this.$scrollable.height();
    let needsVerticalCentering = ((screenHeight - modalHeight - modalOffsetTop) < this.options.offsetTop);

    if ($window.height() < $document.height()) {
      $body
        .addClass('page-overflow')
        .addClass('fix-scroll-' + this.options.scrollbarWidth);
    } else {
      $body
        .removeClass('page-overflow')
        .removeClass('fix-scroll-' + this.options.scrollbarWidth);
    }

    if (needsVerticalCentering) {
      if ((modalHeight + 2 * this.options.padding) <= screenHeight) {
        this.$container.css({
          top: '50%',
          marginTop: -modalHeight / 2
        });
      } else {
        this.$container.css({
          top: this.options.padding,
          marginTop: 0
        });
      }
    } else {
      this.$container.css({
        top: this.options.offsetTop,
        marginTop: 0
      });
    }

    this.$container.css({
      position: 'absolute',
      marginLeft: -this.options.width / 2,
      left: '50%'
    });
  },

  /**
   * Reset the body and $modal references.
   */
  resetDOM () {
    $body.removeClass('modal-open').removeClass('page-overflow');
    this.$modal = null;
    this.$container = null;
    this.$scrollable = null;
    this.$backdrop = null;
    this.$submitButtons = [];
  },

  /**
   * onResize is called if the window gets resized.
   * If the modal is currently opened, then its position will be updated.
   */
  onResize () {
    if (this.isOpened) {
      this.updatePosition();
    }
  },

  /**
   * Submit a form in the modal body.
   *
   * @param {Object} event
   */
  submit (event) {
    let $target = $(event.target), that = this;

    if ($target.attr('disabled') !== undefined) {
      return;
    }

    if (this.isAjax) {
      event.preventDefault();
      $target.prop('disabled', true);
      this.$notify.trigger('modal:beforeAjax');
      let $form = this.$modal.find('form'), data;
      if ($form.length > 0) {
        data = this.$modal.find('form').serialize();
      }
      $.ajax({
        type: this.method,
        url: this.action,
        cache: false,
        data: data,
        success: function(data) {
          that.closeModal();
          that.$notify.trigger(that.modalSuccessEvent, data);
        }
      });
    } else {
      if (this.modalName) {
        this.eventBus.trigger('modal-submitted-' + this.modalName, this, event);
      }
      if (this.options.closeOnSubmit) {
        this.closeModal();
      }
    }
  },

  /**
   * Open the modal.
   *
   * @param {Object=} event
   */
  openModal (event) {
    if (event) {
      event.preventDefault();
    }

    if (this.isOpened || this.$el.hasClass('disabled')) {
      return;
    }
    this.isOpened = true;

    this.initModalOptions();
    this.initModalElements();

    $body.addClass('modal-open');
    this.$modal.appendTo($body);

    this.updatePosition();
    this.$container.hide().css('opacity', 1).fadeIn();

    if (this.$closeButtons.length > 1) {
      for (let i = 0, len = this.$closeButtons.length; i < len; i++) {
        $(this.$closeButtons[i]).on('click', _.bind(this.closeModal, this));
      }
    } else {
      this.$closeButtons.on('click', _.bind(this.closeModal, this));
    }
    if (this.options.closeOnScrollable) {
      this.$scrollable.on('click', _.bind(this.closeModal, this));
    }
    if (this.$submitButtons.length) {
      this.$submitButtons.on('click', _.bind(this.submit, this));
    }

    this.modalName = this.$el.attr('data-modal-name');
    if (this.modalName) {
      this.$modal.attr('data-modal-name', this.modalName);
      this.eventBus.trigger('modal-opened-' + this.modalName, this);
    }
  },

  /**
   * Close the modal.
   * Fades out the backdrop and modal container, resets the body classes and detaches the
   * window resize event listener.
   *
   * The event param is either an event object if triggered via the DOM or it's a callback function
   * when used manually.
   *
   * @param {Object|Function=} event
   */
  closeModal (event) {
    if (typeof event === 'object') {
      if (event.target !== event.currentTarget && $(event.target).parent()[0] !== event.currentTarget) {
        return;
      }
      event.preventDefault();
    }

    if (this.$closeButtons.length > 1) {
      for (let i = 0, len = this.$closeButtons.length; i < len; i++) {
        $(this.$closeButtons[i]).off('click', _.bind(this.closeModal, this));
      }
    } else {
      this.$closeButtons.off('click', _.bind(this.closeModal, this));
    }
    if (this.options.closeOnScrollable) {
      this.$scrollable.off('click', _.bind(this.closeModal, this));
    }
    if (this.$submitButtons.length) {
      this.$submitButtons.off('click', _.bind(this.submit, this));
    }

    $.when.apply(null, [
      this.$container.fadeOut(200),
      this.$backdrop.fadeOut(200)
    ]).then(() => {
      if (this.$modal) {
        this.$modal.remove();
      }
      this.resetDOM();
      this.isOpened = false;
      if (typeof event === 'function') {
        event.call();
      }
    });
  }
});

export default Modal;
