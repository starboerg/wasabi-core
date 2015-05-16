define(function(require) {
  var $ = require('jquery');
  var BaseView = require('common/BaseView');
  var SpinPresets = require('common/SpinPresets');
  require('jquery.color');
  require('jquery.spin');

  return BaseView.extend({

    el: '.wasabi-core--grouppermissions-index',

    /**
     * Registered events of this view.
     *
     * @type {Object}
     */
    events: {
      'click .single-submit': 'onSingleSubmit'
    },

    onSingleSubmit: function(event) {
      event.preventDefault();
      var $target = $(event.target);
      $target.hide().blur().parent().spin(SpinPresets.small);
      $.ajax({
        type: 'post',
        url: this.$('.permissions-update-form').attr('action'),
        data: $target.parent().parent().find('input').serialize(),
        dataType: 'json',
        cache: false,
        success: function() {
          var tr = $target.parent().parent();
          var bgColor = $target.parent().css('backgroundColor');
          $target.parent().spin(false);
          $target.show();
          tr.find('td:not(.controller)').stop().css({
            backgroundColor: '#fff7d9'
          }).animate({
            backgroundColor: bgColor
          }, 1000, function() {
            $(this).css({backgroundColor: ''});
          });
        }
      });
    }
  });
});
