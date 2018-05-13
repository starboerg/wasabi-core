import {View} from 'backbone.marionette';
import GeminiScrollbar from 'gemini-scrollbar';

const ScrollbarContainer = View.extend({

  template: false,

  initialize () {
    this.render();
  },

  onRender () {
    const options = JSON.parse(this.$el.attr('data-scrollbar') || '{}');
    this.instance = new GeminiScrollbar({
      element: this.el,
      autoshow: false,
      createElements: false,
      forceGemini: false,
      ...options
    });
    this.instance.create();
    this.$el.data('scrollbar', this.instance);
  }

});

export default ScrollbarContainer;
