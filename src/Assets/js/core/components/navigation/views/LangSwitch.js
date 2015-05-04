var $ = require('jquery');
var BaseView = require('wasabi.BaseView');
var WS = global.window.WS;
var _ = require('underscore');

module.exports = BaseView.extend({

  /**
   * The element of this view.
   *
   * @type {string} CSS selector
   */
  el: '.lang-switch',

  /**
   * global event listeners
   */
  globalEvents: {
    'wasabi-core--languages-sort': 'updateLanguageLinkPositions'
  },

  updateLanguageLinkPositions: function(event, data) {
    var frontendLanguages = data.frontendLanguages;
    var $sortedLi = [];
    var $li = this.$('li');
    _.each(frontendLanguages, function(el) {
      $sortedLi.push($li.filter('[data-language-id="' + el.id + '"]'));
    });
    this.$el.html($sortedLi);
  }

});
