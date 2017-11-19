import {View} from 'backbone.marionette';

const EmptySelect = View.extend({

  template: false,

  events: {
    'change': 'onChange'
  },

  initialize () {
    this.render();
  },

  onRender () {
    this.onChange();
  },

  onChange () {
    this.$el.toggleClass('empty', this.$el.val() === '');
  }
});

export default EmptySelect;
