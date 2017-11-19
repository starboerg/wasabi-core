import _ from 'underscore';
import {View} from 'backbone.marionette';

const LangSwitch = View.extend({

  template: false,

  initialize (options) {
    this.eventBus = options.eventBus;
    this.updateLanguageLinkPositions = this.updateLanguageLinkPositions.bind(this);

    this.eventBus.on('wasabi-core--languages-sort', this.updateLanguageLinkPositions);

    this.render();
  },

  updateLanguageLinkPositions (event, data) {
    let frontendLanguages = data.frontendLanguages;
    let $sortedLi = [];
    let $li = this.$('li');
    _.each(frontendLanguages, function(el) {
      $sortedLi.push($li.filter('[data-language-id="' + el.id + '"]'));
    });
    this.$el.html($sortedLi);
  }
});

export default LangSwitch;
