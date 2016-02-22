define(function (require) {

  var _ = require('underscore');
  var $ = require('jquery');
  var BaseView = require('common/BaseView');
  var WS = require('wasabi');

  return BaseView.extend({

    template: '',

    isOpen: false,

    events: {
      'submit form': 'onSubmit'
    },

    initialize: function() {
      this.template = _.template($('#wasabi-core-login-modal').html());
      this.$body = $('body');
    },

    openModal: function() {
      if (this.isOpen) {
        return;
      }
      this.isOpen = true;
      this.$body.addClass('modal-open');
      this.$modal = $(this.template());
      this.setElement(this.$modal);
      this.$button = this.$('button');
      this.$modal.appendTo(this.$body);
      this.$('#email').focus();
    },

    onSubmit: function(event) {
      event.preventDefault();

      var $form = this.$('form');

      this.$button.prepend('<span class="spinner spinner-white"></span>').attr('disabled', 'disabled');

      $.ajax({
        url: $form.attr('action'),
        data: $form.serialize(),
        method: 'post',
        success: _.bind(function(data) {
          if (data.content) {
            this.$('.modal-body').html(data.content);
            this.$('#email').focus();
            this.$button.find('.spinner').remove();
            this.$button.attr('disabled', '').prop('disabled', false);
          } else if (!data.redirect) {
            this.closeModal();
            WS.get('wasabi.core').initHeartBeat();
          }
        }, this)
      })
    },

    closeModal: function() {
      $.when.apply(null, [
        this.$('.modal-container').fadeOut(200),
        this.$('.modal-backdrop').fadeOut(200)
      ]).then($.proxy(function() {
        if (this.$modal) {
          this.$modal.remove();
        }
        this.$body.removeClass('modal-open');
        this.isOpen = false;
      }, this));
    }
  });

});
