var Menu = require('./views/Menu');
var NavigationToggle = require('./views/NavigationToggle');
var LangSwitch = require('./views/LangSwitch');
var WS = global.window.WS;

module.exports = function() {
  this.menu = WS.createView(Menu);
  this.navigationToggle = WS.createView(NavigationToggle);
  this.langSwitch = WS.createView(LangSwitch);
};
