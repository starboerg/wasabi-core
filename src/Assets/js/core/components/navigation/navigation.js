var vMenu = require('./views/Menu');
var vNavigationToggle = require('./views/NavigationToggle');

var Navigation = function() {
  this.menu = WS.viewFactory.create(vMenu, {});
  this.navigationToggle = WS.viewFactory.create(vNavigationToggle, {});
};

module.exports = Navigation;
