import $ from 'jquery';
import {View} from 'backbone.marionette';
import '../vendor/jquery.tSortable';

const LanguagesIndex = View.extend({

  template: false,

  initialize (options) {
    this.eventBus = options.eventBus;
    this.onSort = this.onSort.bind(this);

    this.render();
  },

  onRender () {
    this.$languagesTable = $('.datatable.languages');
    this.$languagesTable.on('tSortable-change', this.onSort);
    this.$form = this.$languagesTable.closest('form');
    this.$languagesTable.tSortable({
      handle: 'a.action-sort',
      placeholder: 'sortable-placeholder',
      opacity: 0.8,
      offsetTop: () => {
        return $('#page-header').height();
      },
      offsetLeft: (animation) => {
        let paddingLeft = parseInt(this.$languagesTable.closest('.content--padding').css('padding-left').split('px').join(''))
        if (animation) {
          return $('.sidebar').width();
        }
        return paddingLeft;
      }
    });
  },

  onSort (event) {
    this.$languagesTable.find('tbody > tr').each(function(index) {
      $(this).find('input.position').first().val(index);
    });
    $.ajax({
      url: this.$form.attr('action'),
      data: this.$form.serialize(),
      type: 'post',
      success: (data) => {
        this.eventBus.trigger('wasabi-core--languages-sort', event, data);
      }
    });
  }
});

export default LanguagesIndex;
