define(function(require) {
  var BaseView = require('common/BaseView');
  var WS = require('wasabi');
  var MenuItemsList = require('../views/MenuItemsList');

  return BaseView.extend({

    el: '.wasabi-core--menus-edit',

    initialize: function(options) {
      WS.createView(MenuItemsList, {
        el: this.$('.menu-items'),
        section: this
      });
    }
  });
});
