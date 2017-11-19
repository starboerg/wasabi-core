import $ from 'jquery';
import {View} from 'backbone.marionette';

const TabContainer = View.extend({

  template: false,

  events: {
    'click li': 'onClick',
    'click a': 'onClick'
  },

  initialize (options) {
    this.eventBus = options.eventBus;
    this.render();
  },

  onRender () {
    this.tabifyId = this.$el.attr('data-tabify-id');
    this.$listItems = this.$('> li');
    this.$tabs = $('div[data-tabify-tab]').filter('[data-tabify-id="' + this.tabifyId + '"]');

    let target = window.location.hash.split('#')[1] || null;
    let $activeItem = this.$listItems.filter('[data-tabify-target="' + target + '"]:not([data-tabify-disabled="1"])');
    let isActiveClassSet = this.$listItems.filter('.active').length > 0;
    if (!isActiveClassSet) {
      if ($activeItem.length > 0) {
        $activeItem.find('a').trigger('click');
      } else {
        this.$listItems.first().addClass('active');
      }
    }
  },

  onClick (event) {
    event.preventDefault();
    event.stopPropagation();

    let $$ = $(event.target);

    if ($$.is('a')) {
      $$ = $$.parent();
    }

    if (!$$.is('li')) {
      return false;
    }

    let target = $$.attr('data-tabify-target');
    let disabled = $$.attr('data-tabify-disabled');
    let active = $$.hasClass('active');

    if (typeof disabled !== 'undefined' && disabled === '1' || active === true) {
      return false;
    }

    this.$listItems.removeClass('active');
    this.$tabs.removeClass('active').hide();
    this.$tabs.filter('[data-tabify-tab="' + target + '"]').addClass('active').show();
    window.history.replaceState(null, null, '#' + target);
    this.eventBus.trigger('tab-changed.' + target);

    $$.addClass('active');
  }
});

export default TabContainer;
