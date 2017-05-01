define(function(require) {
  var BaseView = require('common/BaseView');
  var WS = require('wasabi');
  var LanguagesTable = require('../components/LanguagesTable');

  return BaseView.extend({

    el: '.wasabi-core--languages-index',

    initialize: function(options) {
      WS.createView(LanguagesTable, {
        el: this.$('table.languages')
      });
    }
  });
});
