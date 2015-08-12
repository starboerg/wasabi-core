define(function(require) {

  var $ = require('jquery');
  var Handlebars = require('handlebars');
  var BaseView = require('common/BaseView');

  return BaseView.extend({

    templateData: {},

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
    },

    closeDialog: function() {
      this.destroy();
      $('body').removeClass('dialog-open');
    }

  });

});
