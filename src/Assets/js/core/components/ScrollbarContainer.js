import {View} from 'backbone.marionette';
import GeminiScrollbar from 'gemini-scrollbar';

const ScrollbarContainer = View.extend({

  template: false,

  initialize () {
    this.render();
  },

  onRender () {
    this.instance = new GeminiScrollbar({
      element: this.el,
      autoshow: false,
      createElements: false,
      forceGemini: false
    });
    this.instance.create();
    this.$el.data('scrollbar', this.instance);
  }

});

export default ScrollbarContainer;
