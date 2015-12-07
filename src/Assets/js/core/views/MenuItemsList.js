define(function(require) {
  var _ = require('underscore');
  var $ = require('jquery');
  var BaseView = require('common/BaseView');
  require('jquery.nSortable');

  return BaseView.extend({

    /**
     * Registered events of this view.
     *
     * @type {Object}
     */
    events: {
      'nSortable-change': 'onSort'
    },

    /**
     * Holds a reference to the section (MenusEdit)
     */
    section: {},

    menuItemsReorderUrl: false,

    initialize: function(options) {
      this.section = options.section;
      this.menuItemsReorderUrl = this.$el.attr('data-reorder-url');
      this.$el.nSortable({
        handle: 'a.move',
        tabWidth: 20,
        placeholder: 'sortable-placeholder',
        dataAttribute: 'data-menu-item-id',
        maxDepth: parseInt(this.$el.attr('data-maximum-nesting-level')),
        serializeKey: '',
        serializeWrapKeys: false,
        serializeParentIdNullable: true,
        leftKey: 'lft',
        rightKey: 'rght',
        animateTarget: false
      });
    },

    onSort: function(event, nSortable) {
      this.blockThis({
        backgroundColor: '#fff',
        deltaHeight: -1
      });
      var items = nSortable.toArray();
      for (var key in items) {
        if (!items.hasOwnProperty(key)) {
          continue;
        }
        var item = items[key];
        var $add = this.$el.find('li[data-menu-item-id="' + item.id + '"]').first().find('.wicon-add').first().parent();
        if (item.depth > 2) {
          $add.addClass('hidden');
        } else {
          $add.removeClass('hidden');
        }
      }
      $.ajax({
        type: 'post',
        dataType: 'json',
        url: this.menuItemsReorderUrl,
        data: nSortable.serialize(),
        cache: false,
        success: _.bind(this.unblockThis, this)
      });
    }
  });
});
