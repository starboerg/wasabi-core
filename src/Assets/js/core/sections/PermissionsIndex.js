import $ from 'jquery';
import {View} from 'backbone.marionette';
import "../vendor/bootstrap/button";

const PermissionsIndex = View.extend({

  template: false,

  initialize () {
    this.render();
  },

  onRender () {
    $('.datatable').find('input[type="radio"][checked="checked"]').parent().addClass('active');
  }
});

export default PermissionsIndex;
