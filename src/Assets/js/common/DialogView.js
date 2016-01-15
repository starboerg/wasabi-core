define(function(require) {

  var $ = require('jquery');
  var _ = require('underscore');
  var Marionette = require('marionette');

  return Marionette.ItemView.extend({

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

    initialize: function() {
      this.template = _.template($('#wasabi-core-dialog').html());
    },

    render: function() {
      if (typeof this.beforeRender === 'function') {
        this.beforeRender();
      }
      if (!this.templateData.sidebarLeft) {
        this.templateData.sidebarLeft = false;
      }
      if (!this.templateData.sidebarRight) {
        this.templateData.sidebarRight = false;
      }
      this.setElement(this.template(this.templateData));
      $('body').addClass('dialog-open').append(this.$el);

      $(window).on('keyup', _.bind(this.onKeyup, this));

      this.$sidebarLeft = this.$('.dialog-sidebar-left');
      this.$sidebarRight = this.$('.dialog-sidebar-right');
      this.$content = this.$('.dialog-content');

      if (typeof this.initDialogContent === 'function') {
        this.initDialogContent();
      }
    },

    closeDialog: function() {
      $(window).off('keyup', _.bind(this.onKeyup, this));
      this.destroy();
      this.remove();
      $('body').removeClass('dialog-open');
    },

    onKeyup: function(event) {
      if (event.which === 27) {
        this.closeDialog();
      }
    }

  });

});
