define(function(require) {

  var $ = require('jquery');
  var _ = require('underscore');
  var Handlebars = require('handlebars');
  var BaseView = require('common/BaseView');

  return BaseView.extend({

    templateData: {},

    /**
     * Holds a reference to the left sidebar.
     */
    $sidebarLeft: {},

    /**
     * Holds a reference to the right sidebar.
     */
    $sidebarRight: {},

    /**
     * Holds a reference to the content container.
     */
    $content: {},

    events: {
      'click [data-toggle="close"]': 'closeDialog'
    },

    initialize: function() {
      this.template = Handlebars.compile($('#wasabi-core-dialog').html());
    },

    render: function() {
      if (typeof this.beforeRender === 'function') {
        this.beforeRender();
      }
      this.setElement(this.template(this.templateData));
      $('body').addClass('dialog-open').append(this.$el);

      $(window).on('keyup', _.bind(this.onKeyup, this));

      this.$sidebarLeft = this.$('dialog-sidebar-left');
      this.$sidebarRight = this.$('dialog-sidebar-right');
      this.$content = this.$('.dialog-content');

      if (typeof this.initDialogContent === 'function') {
        this.initDialogContent();
      }
    },

    closeDialog: function() {
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
