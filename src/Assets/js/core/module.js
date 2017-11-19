import {Application} from 'backbone.marionette';
import $ from 'jquery';
import _ from 'underscore';
import Wasabi from '../wasabi';
import Menu from "./components/Menu";
import SidebarNavigationToggle from "./components/SidebarNavigationToggle";
import SidebarOpenHandle from "./components/SidebarOpenHandle";
import Pagination from "./components/Pagination";
import LoadingButton from "./components/LoadingButton";
import LangSwitch from "./components/LangSwitch";
import LoginModal from "./components/LoginModal";
import FlashMessage from "./components/FlashMessage";
import EmptySelect from "./components/EmptySelect";
import Modal from "./components/Modal";
import "./vendor/bootstrap/dropdown";
import TabContainer from "./components/TabContainer";
import ScrollbarContainer from "./components/ScrollbarContainer";
import LanguagesIndex from "./sections/LanguagesIndex";
import PermissionsIndex from "./sections/PermissionsIndex";

let WasabiCore = Application.extend({

  /**
   * Holds a reference to the Wasabi application instance.
   * @type {Wasabi}
   */
  WS: null,

  /**
   * Holds all components that should be initialized.
   */
  components: {
    sidebarMenu: {
      selector: '.sidebar--menu',
      viewClass: Menu
    },
    sidebarOpenHandle: {
      selector: '.sidebar--open-handle',
      viewClass: SidebarOpenHandle
    },
    sidebarNavigationToggle: {
      selector: '.sidebar--navigation-toggle',
      viewClass: SidebarNavigationToggle
    },
    pagination: {
      selector: '.pagination',
      viewClass: Pagination
    },
    loadingButton: {
      selector: '[data-toggle="btn-loading"]',
      viewClass: LoadingButton,
      liveQuery: true
    },
    langSwitch: {
      selector: '.lang-switch',
      viewClass: LangSwitch
    },
    loginModal: {
      selector: false,
      viewClass: LoginModal
    },
    flashMessage: {
      selector: '.flash-message',
      viewClass: FlashMessage,
      liveQuery: true
    },
    emptySelect: {
      selector: 'select',
      viewClass: EmptySelect,
      liveQuery: true
    },
    modal: {
      selector: '[data-toggle="modal"], [data-toggle="confirm"]',
      viewClass: Modal,
      liveQuery: true
    },
    tabContainer: {
      selector: '.tabs[data-tabify-id]',
      viewClass: TabContainer,
      liveQuery: true
    },
    scrollbarContainer: {
      selector: '[data-init="gm-scrollbar"]',
      viewClass: ScrollbarContainer
    }
  },

  /**
   * Holds all sections that should be initialized.
   */
  sections: {
    languagesIndex: {
      selector: '.wasabi-core--languages-index',
      viewClass: LanguagesIndex
    },
    permissionsIndex: {
      selector: '.wasabi-core--permissions-index',
      viewClass: PermissionsIndex
    }
  },

  /**
   * Initialize the module.
   */
  initialize (options) {
    this.options = options;
    this.options.scrollbarWidth = this.detectScrollbarWidth();
    this.WS = options.WS;

    this.onAjaxSuccess = this.onAjaxSuccess.bind(this);
    this.onAjaxError = this.onAjaxError.bind(this);

    this.setupAjax();
    this.components.modal.options = {
      scrollbarWidth: this.options.scrollbarWidth,
      confirmYes: this.options.translations.confirmYes,
      confirmNo: this.options.translations.confirmNo
    };
    this.WS.initComponents(this.components);
    this.WS.initSections(this.sections);
    this.initEvents();
  },

  /**
   * Apply default ajax configuration.
   */
  setupAjax () {
    $.ajaxSetup({
      dataType: 'json',
      beforeSend: (xhr, options) => {
        if (!options.heartBeat) {
          this.WS.eventBus.trigger('init-heartbeat', this.options.heartbeat);
        }
      }
    });

    $(document)
      .ajaxSuccess(this.onAjaxSuccess)
      .ajaxError(this.onAjaxError);
  },

  /**
   * Global success handler for all ajax responses.
   *
   * @param {Event} event
   * @param {XMLHttpRequest} xhr
   */
  onAjaxSuccess (event, xhr) {
    if (xhr.status === 200 && xhr.statusText === 'OK') {
      let contentType = xhr.getResponseHeader('Content-Type');
      if (contentType.split('application/json').length === 2) {
        let data = $.parseJSON(xhr.responseText) || {};

        if (typeof data.status !== 'undefined') {
          if (typeof data.flash !== 'undefined') {
            this.flashMessage(data.flash);
          }
          if (typeof data.redirect !== 'undefined') {
            window.location = data.redirect;
          }
        }
      }
    }
  },

  /**
   * Global error handler for all ajax responses.
   *
   * @param {Event} event
   * @param {XMLHttpRequest} xhr
   */
  onAjaxError (event, xhr) {
    let data;
    if (xhr.status === 401) {
      data = $.parseJSON(xhr.responseText) || {};
      if (typeof data.name !== 'undefined') {
        if (confirm(data.name)) {
          window.location.reload();
        } else {
          window.location.reload();
        }
      }
    }
    if (xhr.status === 403) {
      this.WS.eventBus.trigger('auth-error');
    }
    if (xhr.status === 500) {
      data = $.parseJSON(xhr.responseText) || {};
      if (typeof data.name !== 'undefined') {
        this.flashMessage({
          before: 'div.title-pad',
          class: 'error',
          message: data.name
        });
      }
    }
  },

  detectScrollbarWidth () {
    let $scrollDetector = $('<div/>').css({
      width: '100px',
      height: '100px',
      overflow: 'scroll',
      position: 'absolute',
      top: '-9999px'
    });

    let $body = $('body');
    $scrollDetector.prependTo($body);
    let scrollbarWidth = 0;
    setTimeout(function() {
      scrollbarWidth = $scrollDetector[0].offsetWidth - $scrollDetector[0].clientWidth;
    }, 0);
    $scrollDetector.remove();

    return scrollbarWidth;
  },

  /**
   * Render a flash message after a specific element.
   *
   * @param {Object} flash The flash object returned by the ajax request.
   * @private
   */
  flashMessage (flash) {
    let insertBefore = flash.before || false;
    let insertAfter = flash.after || false;
    let message = flash.message;
    let cls = (flash.class) ? 'flash-' + flash.class : 'flash-success';
    let $target = false;
    let $flash = $('<div/>').addClass(cls).html(message);

    if (insertBefore) {
      $target = $(insertBefore);
      $target.prev('.' + cls).remove();
      $flash.insertBefore($target);
    } else if (insertAfter) {
      $target = $(insertAfter);
      $target.next('.' + cls).remove();
      $flash.insertAfter($target);
    }
  },

  /**
   * Initialize global events like scroll and resize that are debounced and triggered on the eventBus.
   */
  initEvents () {
    let $window = $(window);
    let $body = $('body');

    $window.on('scroll', _.debounce(() => {
      this.WS.eventBus.trigger('window-scroll')
    }, 50));

    $window.on('resize', _.debounce(() => {
      this.WS.eventBus.trigger('window-resize')
    }, 50));

    $body.on('click', '.dropdown-interaction', function(event) {
      event.stopPropagation();
    });

    this.WS.eventBus.trigger('init-heartbeat', this.options.heartbeat);
  }
});

export default WasabiCore;
