define(function(require) {

  var $ = require('jquery');
  var _ = require('underscore');
  var Marionette = require('marionette');

  return Marionette.ItemView.extend({

    template: '#wasabi-core-dialog',

    templateData: {},

    /**
     * Holds a reference to the left sidebar.
     */
    $sidebarLeft: null,

    /**
     * Holds a reference to the right sidebar.
     */
    $sidebarRight: null,

    /**
     * Holds a reference to the content container.
     */
    $content: null,

    events: {
      'click [data-toggle="close"]': 'closeDialog'
    },

    ui: {
      sidebarLeft: '.dialog-sidebar-left',
      sidebarRight: '.dialog-sidebar-right',
      content: '.dialog-content'
    },

    initialize: function() {
      if (!this.templateData.sidebarLeft) {
        this.templateData.sidebarLeft = false;
      }
      if (!this.templateData.sidebarRight) {
        this.templateData.sidebarRight = false;
      }

      this.templateHelpers = function() {
        return this.templateData
      }
    },

    onRender: function () {
      $('body').addClass('dialog-open');
      $(window).on('keyup', _.bind(this.onKeyup, this));

      if (typeof this.initDialogContent === 'function') {
        this.initDialogContent();
      }
    },

    closeDialog: function() {
      if (typeof this.beforeClose === 'function') {
        this.beforeClose();
      }
      $(window).off('keyup', _.bind(this.onKeyup, this));
      this.destroy();
      $('body').removeClass('dialog-open');
    },

    onKeyup: function(event) {
      if (event.which === 27) {
        this.closeDialog();
      }
    }

  });

});
