define(function(require) {

  var $ = require('jquery');
  var BaseView = require('common/BaseView');

  return BaseView.extend({

    events: {
      'click li': 'onClick',
      'click a': 'onClick'
    },

    initialize: function() {
      this.tabifyId = this.$el.attr('data-tabify-id');
      this.$listItems = this.$('> li');
      this.$tabs = $('div[data-tabify-tab]').filter('[data-tabify-id="' + this.tabifyId + '"]');
    },

    onClick: function(event) {
      event.preventDefault();

      var $$ = $(event.target);

      if ($$.is('a')) {
        $$ = $$.parent();
      }

      if (!$$.is('li')) {
        return false;
      }

      var target = $$.attr('data-tabify-target');
      var disabled = $$.attr('data-tabify-disabled');

      if (typeof disabled !== 'undefined' && disabled === '1') {
        return false;
      }

      this.$listItems.removeClass('active');
      this.$tabs.removeClass('active').hide();
      this.$tabs.filter('[data-tabify-tab="' + target + '"]').addClass('active').show();

      $$.addClass('active');
    }

  });

});