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

      var target = window.location.hash.split('#')[1] || null;
      var $activeItem = this.$listItems.filter('[data-tabify-target="' + target + '"]:not([data-tabify-disabled="1"])');
      if ($activeItem.length > 0) {
        $activeItem.find('a').trigger('click');
      } else {
        this.$listItems.first().addClass('active');
      }
    },

    onClick: function(event) {
      event.preventDefault();
      event.stopPropagation();

      var $$ = $(event.target);

      if ($$.is('a')) {
        $$ = $$.parent();
      }

      if (!$$.is('li')) {
        return false;
      }

      var target = $$.attr('data-tabify-target');
      var disabled = $$.attr('data-tabify-disabled');
      var active = $$.hasClass('active');

      if (typeof disabled !== 'undefined' && disabled === '1' || active === true) {
        return false;
      }

      this.$listItems.removeClass('active');
      this.$tabs.removeClass('active').hide();
      this.$tabs.filter('[data-tabify-tab="' + target + '"]').addClass('active').show();
      window.history.replaceState(null, null, '#' + target);
      $('body').trigger('tab-changed.' + target);

      $$.addClass('active');
    }

  });

});
