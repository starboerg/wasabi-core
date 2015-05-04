var $ = require('jquery');
var BaseView = require('wasabi.BaseView');
var WS = global.window.WS;
var LanguagesTable = require('../views/LanguagesTable');

module.exports = BaseView.extend({

  el: '.wasabi-core--languages-index',

  initialize: function(options) {
    WS.createView(LanguagesTable, {
      el: this.$el.find('table.languages')
    });
  }

});