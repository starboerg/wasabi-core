import $ from 'jquery';
import {View} from 'backbone.marionette';

const LoadingButton = View.extend({

  template: false,

  loading: false,

  initialize () {
    this.toggleLoadingState = this.toggleLoadingState.bind(this);

    this.render();
  },

  onRender () {
    let innerContent = this.$el.html();
    this.$el.html($('<span class="hide-when-loading" />').html(innerContent));
    this.$el.append($('<div class="spinner spinner-white" />'));

    this.$el.closest('form')
      .off('submit', this.toggleLoadingState)
      .on('submit', this.toggleLoadingState);

    this.$el.data('loadingButton', this);
  },

  toggleLoadingState (event) {
    if (!this.loading) {
      this.loading = true;
      let width = this.$el.outerWidth();
      this.$el.css('min-width', width + 'px');
      this.$el.addClass('btn-loading');
      this.$el.attr('disabled', 'disabled');
    } else {
      this.loading = false;
      this.$el.css('min-width', '');
      this.$el.removeClass('btn-loading');
      this.$el.prop('disabled', false);
    }
  }

});

export default LoadingButton;
