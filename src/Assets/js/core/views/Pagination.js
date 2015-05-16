define(function(require) {
  var $ = require('jquery');
  var BaseView = require('common/BaseView');
  var purl = require('purl');

  return BaseView.extend({
    el: '.pagination',

    events: {
      'change .limit': 'onChangeLimit'
    },

    onChangeLimit: function(event) {
      var $target = $(event.target);
      var limit = $target.val();
      var targetUrl = $target.attr('data-url');
      var currentUrl = purl(window.location);
      var limitParamName = $target.attr('name').split('data[').join('').split(']').join('');

      var params = currentUrl.data.param.query;
      params[limitParamName] = limit.toString();

      var queryString = '';
      var parts = [];
      for (var param in params) {
        if (!params.hasOwnProperty(param)) {
          continue;
        }
        parts.push(param + '=' + params[param]);
      }

      queryString = parts.join('&');

      if (queryString !== '') {
        queryString = '?' + queryString;
      }

      window.location = targetUrl + queryString;
    }
  });
});
