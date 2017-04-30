define(function(require) {

  var BaseView = require('common/BaseView');

  require('bootstrap.button');

  return BaseView.extend({

    el: '.wasabi-core--permissions-index',

    initialize: function () {
      this.$('.datatable').find('input[type="radio"][checked="checked"]').parent().addClass('active');
    }
  });
});
