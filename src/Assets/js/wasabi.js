/**
 * @author       Frank Förster <github@frankfoerster.com>
 * @copyright    Copyright (c) 2017 Frank Förster
 */
import Marionette from 'backbone.marionette';
import $ from 'jquery';
import _ from 'underscore';
import Backbone from 'backbone';
import WasabiCore from "./core/module";
import './core/vendor/jquery.livequery';
import Pace from 'pace-progress';

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
     * Holds all registered modules.
     * @type {Array}
     */
    this.registeredModules = [];

    /**
     * Holds all initialized module instances.
     * @type {Object}
     */
    this.modules = {};

    /**
     * Holds all initialized components.
     * @type {Object}
     */
    this.components = {};

    /**
     * Holds all initialized sections.
     * @type {Object}
     */
    this.sections = {};

    console.info('Wasabi instance initialized.');
  }

  /**
   * Register a module.
   *
   * @param {String} name
   * @param {Marionette.Application} module
   */
  registerModule (name, module) {
    this.registeredModules.push({
      name: name,
      module: module,
      options: {}
    });
    console.info('Module "' + name + '" registered.');
  }

  configureModule (name, options) {
    this.registeredModules.forEach((m) => {
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
    this.registeredModules.forEach((m) => {
      let module = new m.module(_.extend({}, m.options, { WS: this }));
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
    if (typeof this.modules[name] === 'undefined') {
      throw new Error('Module "' + name + '" not found.');
    }
    return this.modules[name];
  }

  /**
   * Initialize the given components.
   *
   * @param {Object} components
   */
  initComponents (components) {
    let component;

    const initComponent = (componentName, $el = false) => {
      let options;
      if (typeof component.options === 'function') {
        options = component.options($el);
      } else {
        options = component.options;
      }
      if (typeof this.components[componentName] === 'undefined') {
        this.components[componentName] = {};
      }
      if (typeof this.components[componentName]['instances'] === 'undefined') {
        this.components[componentName]['instances'] = [];
      }

      let ViewClass = component.viewClass;
      this.components[componentName]['instances'].push(
        new ViewClass(_.extend({}, {
          el: $el,
          eventBus: this.eventBus,
          WS: this
        }, options))
      );
    };

    for (let componentName in components) {
      if (!components.hasOwnProperty(componentName)) {
        continue;
      }
      component = components[componentName];
      component.options = component.options || {};
      component.liveQuery = component.liveQuery || false;

      if (component.selector !== false) {
        if (component.liveQuery) {
          $(component.selector).livequery(function() {
            initComponent(componentName, $(this));
          });
        }
        $(component.selector).each((index, el) => {
          initComponent(componentName, $(el));
        });
      } else {
        initComponent(componentName);
      }
    }
  }

  getComponent (name, all = false) {
    if (
      typeof this.components[name] === 'undefined' ||
      typeof this.components[name]['instances'] === 'undefined' ||
      typeof this.components[name]['instances'][0] === 'undefined'
    ) {
      throw new Error('Component instance "' + name + '" not found.');
    }

    if (all) {
      return typeof this.components[name]['instances'];
    }

    return typeof this.components[name]['instances'][0];
  }

  /**
   * Initialize the given sections.
   *
   * @param {Object} sections
   */
  initSections (sections) {
    let section;
    for (let sectionName in sections) {
      if (!sections.hasOwnProperty(sectionName)) {
        continue;
      }
      section = sections[sectionName];
      section.options = section.options || {};
      $(section.selector).each((el, index) => {
        let options;
        if (typeof section.options === 'function') {
          options = section.options($(el));
        } else {
          options = section.options;
        }
        if (typeof this.sections[sectionName] === 'undefined') {
          this.sections[sectionName] = {};
        }
        if (typeof this.sections[sectionName]['instances'] === 'undefined') {
          this.sections[sectionName]['instances'] = [];
        }

        let ViewClass = section.viewClass;
        this.sections[sectionName]['instances'].push(
          new ViewClass(_.extend({}, {
            el: $(el),
            eventBus: this.eventBus,
            WS: this
          }, options))
        );
      });
    }
  }
}

Pace.start();
window.WS = new Wasabi();
window.WS.registerModule('Wasabi/Core', WasabiCore);

export default Wasabi;
