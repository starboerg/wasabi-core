define(function(require) {
  var _ = require('underscore');
  var BaseView = require('common/BaseView');

  return BaseView.extend({

    /**
     * The element of this view.
     *
     * @type {string} CSS selector
     */
    el: '[data-toggle="btn-loading"]',

    loading: false,

    initialize: function() {
      var innerContent = this.$el.html();
      this.$el.html($('<span class="hide-when-loading" />').html(innerContent));
      this.$el.append($('<div class="spinner spinner-white" />'));

      this.$el.closest('form')
        .off('submit', _.bind(function(event) { this.toggleLoadingState(event); }, this))
        .on('submit', _.bind(function(event) { this.toggleLoadingState(event); }, this));

      this.$el.data('loadingButton', this);
    },

    toggleLoadingState: function(event) {
      if (!this.loading) {
        this.loading = true;
        var width = this.$el.outerWidth();
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
});
