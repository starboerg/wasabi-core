var $ = require('jquery');
var _ = require('underscore');
var Backbone = require('backbone');

Backbone.$ = $;

var CoreRouter = Backbone.Router.extend({

  routes: {
    '': 'dashboard',
    'backend/users': 'users'
  },

  initialize: function(baseUrl) {
    Backbone.history.start({pushState: true});
    console.log(baseUrl);
  },

  dashboard: function() {
    console.log('dashboard');
  },

  users: function() {
    console.log('users');
  }

});

module.exports = CoreRouter;
