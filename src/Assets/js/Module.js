import Backbone from 'backbone';
import $ from 'jquery';
import _ from 'underscore';

const strundefined = 'undefined';

class Module {

  /**
   * Constructor
   *
   * @param {Wasabi} WS
   * @param {Backbone.Events} eventBus
   * @param {Object} options
   */
  constructor (WS, eventBus, options) {
    /**
     * Holds a reference to the global Wasabi instance.
     *
     * @type {Wasabi}
     */
    this.WS = WS;

    /**
     * Holds a reference to the global event bus.
     *
     * @type {Backbone.Events}
     */
    this.eventBus = eventBus;

    this.initialize.apply(this, [options]);
  }

  /**
   * Start the module by triggering the onStart method.
   */
  start () {
    if (typeof this.onStart === 'function') {
      this.onStart();
    }
  }

  /**
   * Initialize the module components.
   */
  initComponents () {
    let component;

    const initComponent = (componentName, $el = false) => {
      let options;
      if (typeof component.options === 'function') {
        options = component.options($el);
      } else {
        options = component.options;
      }
      if (typeof this.components[componentName] === strundefined) {
        this.components[componentName] = {};
      }
      if (typeof this.components[componentName]['instances'] === strundefined) {
        this.components[componentName]['instances'] = [];
      }

      let ViewClass = component.viewClass;
      this.components[componentName]['instances'].push(
        new ViewClass(_.extend({}, {
          el: $el,
          eventBus: this.eventBus,
          WS: this.WS
        }, options))
      );
    };

    if (typeof this.components === strundefined) {
      this.components = {};
    }

    for (let componentName in this.components) {
      if (!this.components.hasOwnProperty(componentName)) {
        continue;
      }
      component = this.components[componentName];
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

  /**
   * Initialize the module sections.
   */
  initSections () {
    let section;

    if (typeof this.sections === strundefined) {
      this.sections = {};
    }

    for (let sectionName in this.sections) {
      if (!this.sections.hasOwnProperty(sectionName)) {
        continue;
      }
      section = this.sections[sectionName];
      section.options = section.options || {};
      $(section.selector).each((el, index) => {
        let options;
        if (typeof section.options === 'function') {
          options = section.options($(el));
        } else {
          options = section.options;
        }
        if (typeof this.sections[sectionName] === strundefined) {
          this.sections[sectionName] = {};
        }
        if (typeof this.sections[sectionName]['instances'] === strundefined) {
          this.sections[sectionName]['instances'] = [];
        }

        let ViewClass = section.viewClass;
        this.sections[sectionName]['instances'].push(
          new ViewClass(_.extend({}, {
            el: $(el),
            eventBus: this.eventBus,
            WS: this.WS
          }, options))
        );
      });
    }
  }

  getComponent (name) {
    if (
      typeof this.components[name] === strundefined ||
      typeof this.components[name]['instances'] === strundefined ||
      typeof this.components[name]['instances'][0] === strundefined
    ) {
      throw new Error('Component instance "' + name + '" not found in module "' + this.getName() + '".');
    }

    return this.components[name]['instances'][0];
  }
}

Module.extend = Backbone.Model.extend;

export default Module;
