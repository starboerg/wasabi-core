import {View} from 'backbone.marionette';

const FlashMessage = View.extend({

  template: false,

  ui: {
    dismiss: '.dismiss-flash'
  },

  events: {
    'click @ui.dismiss': 'dismiss'
  },

  initialize () {
    this.render();
  },

  dismiss (event) {
    event.preventDefault();
    this.$el.slideUp();
  }
});

export default FlashMessage;
