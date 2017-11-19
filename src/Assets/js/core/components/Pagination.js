import $ from 'jquery';
import purl from '../vendor/purl';
import {View} from 'backbone.marionette';

const Pagination = View.extend({

  template: false,

  events: {
    'change .limit': 'onChangeLimit'
  },

  initialize () {
    this.render();
  },

  onChangeLimit (event) {
    let $target = $(event.target);
    let limit = $target.val();
    let targetUrl = $target.attr('data-url');
    let currentUrl = purl(window.location);
    let limitParamName = $target.attr('name').split('data[').join('').split(']').join('');

    let params = currentUrl.data.param.query;
    params[limitParamName] = limit.toString();

    let queryString = '';
    let parts = [];
    for (let param in params) {
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

export default Pagination;
