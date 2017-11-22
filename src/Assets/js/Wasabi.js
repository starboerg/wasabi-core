/**
 * @author       Frank Förster <github@frankfoerster.com>
 * @copyright    Copyright (c) 2017 Frank Förster
 */
import Marionette from 'backbone.marionette';
import $ from 'jquery';
import _ from 'underscore';
import Backbone from 'backbone';
import Module from './Module';
import './core/vendor/jquery.livequery';
import Pace from 'pace-progress';

/**
 * 'undefined' string for typeof comparisons.
 *
 * @type {string}
 */
const strundefined = 'undefined';

/**
 * Holds all registered modules.
 * @type {Array}
 */
let registeredModules = []

class Wasabi {

  /**
   * Constructor
   */
  constructor () {

    /**
     * Global event bus.
     * @type {Backbone.Events}
     */
    this.eventBus = _.extend({}, Backbone.Events);

    /**
     * Holds all initialized module instances.
     * @type {Object}
     */
    this.modules = {};

    /**
     * Holds all ES6 modules and libaries that should be available to other Wasabi modules (e.g. in an individual app).
     *
     * @type {*}
     */
    this.exports = {
      $: $,
      _: _,
      Backbone: Backbone,
      Marionette: Marionette,
      Module: Module,
      Pace: Pace
    };

    Pace.start();
    console.info('Wasabi instance initialized.');
  }

  /**
   * Register a module.
   *
   * @param {String} name
   * @param {Module} module
   */
  registerModule (name, module) {
    registeredModules.push({
      name: name,
      module: module,
      options: {}
    });
    console.info('Module "' + name + '" registered.');
  }

  /**
   * Configure the module with the given options.
   *
   * @param {String} name
   * @param {Object} options
   */
  configureModule (name, options) {
    registeredModules.forEach((m) => {
      if (m.name === name) {
        m.options = options;
        console.info('Module "' + name + '" configured.');
      }
    });
  }

  /**
   * Boot the application.
   */
  boot () {
    console.info('--- Bootstrap [STARTED] ---');
    registeredModules.forEach((m) => {
      let module = new m.module(this, this.eventBus, _.extend({}, m.options));
      this.modules[m.name] = module;
      module.start();
      console.info('Bootstrapped module "' + m.name + '".');
    });
    console.info('--- Bootstrap [FINISHED] ---');
  }

  /**
   * Get an initialized module instance by name.
   *
   * @param {String} name
   * @returns {*}
   */
  getModule (name) {
    if (typeof this.modules[name] === strundefined) {
      throw new Error('Module "' + name + '" not found.');
    }
    return this.modules[name];
  }
}

window.WS = new Wasabi();

export default Wasabi;
