/*!
 * jQuery Table Sortable v0.0.1
 *
 * Copyright (c) 2013 Frank Förster (http://frankfoerster.com)
 * Licensed under the MIT License
 */
import $ from 'jquery';
import Scrollparent from 'scrollparent';
import './jquery.eventMagic';

let win = window;
let doc = document;

const TableSortable = function(el, options) {
  this.$el = $(el);
  this.settings = $.extend({}, $.fn.tSortable.defaults, options);

  this.isDragging = false;
  this.$tr = null;
  this.$clone = null;
  this.$placeholder = null;

  this.startPosition = {};
  this.trOffset = {};

  this.placeholderHeight = null;

  this.scrollParent = null;

  this._primaryEvents = [];
  this._secondaryEvents = [];

  if (!this.$el.is('table')) {
    throw new Error('DOM element is not supported. Use table!');
  }

  this._buildEvents();
  $.attachEvents(this._primaryEvents);
};

TableSortable.prototype = {

  _buildEvents: function() {
    this._primaryEvents = [
      [this.$el, this.settings.items + ' ' + this.settings.handle, [
        ['mousedown', $.proxy(this._onMouseDown, this)]
      ]]
    ];

    this._secondaryEvents = [
      [$(doc), {
        mousemove: $.proxy(this._onMouseMove, this),
        mouseup: $.proxy(this._onMouseUp, this)
      }]
    ];
  },

  _onMouseDown: function(event) {
    event.preventDefault();
    this._initStart(event);
    $.attachEvents(this._secondaryEvents);
  },

  _onMouseUp: function(event) {
    let that = this;
    $.detachEvents(this._secondaryEvents);
    if (this.isDragging) {
      if (this.settings.animateTarget) {
        let placeholderOffset = this.$placeholder.offset();
        this.$clone.animate({
          top: placeholderOffset.top - this.settings.offsetTop(),
          left: placeholderOffset.left - this.settings.offsetLeft(true)
        }, parseInt(this.settings.animationLength), _stop);
      } else {
        _stop();
      }
    }

    function _stop() {
      that.$clone.remove();
      that.$clone = null;
      that.$tr.insertBefore(that.$placeholder);
      that.$placeholder.remove();
      that.$placeholder = null;
      that.$tr.show();
      that.$tr = null;
      that.scrollParent = null;
      that.isDragging = false;
      that.$el.removeClass('table--sorting');
      that._trigger('tSortable-change', event);
    }
  },

  _onMouseMove: function(event) {
    if (!this.isDragging) {
      this._initClone();
      this._initPlaceholder();
      this.$tr.hide();
      this.scrollParent = Scrollparent(this.$clone.get(0));
      this.isDragging = true;
    }
    this._updateClonePosition(event);

    if (this.settings.scroll === true) {
      this._scroll(event);
    }

    (function(that) {
      if (that._yIntersectsPlaceholder(event)) {
        return;
      }

      let $items = that.$el.find(that.settings.items).filter(function() {
        return (
          ($(this)[0] !== that.$tr[0]) &&
          ($(this).css('display') !== 'none') &&
          ($(this).css('position') !== 'absolute') &&
          !$(this).hasClass('sortable-clone') &&
          !$(this).hasClass(that.settings.placeholder)
        );
      });

      let $intersectedItem = null;
      let direction = null;

      $items.each(function() {
        let min = $(this).offset().top;
        let max = min + $(this).outerHeight();
        let middle = parseInt((min + max) / 2);
        if (event.pageY >= min && event.pageY < middle) {
          $intersectedItem = $(this);
          direction = 'up';
          return false;
        }
        if (event.pageY > middle && event.pageY <= max ) {
          $intersectedItem = $(this);
          direction = 'down';
          return false;
        }
      });

      if ($intersectedItem === null) {
        return;
      }

      if (direction === 'up') {
        if ($intersectedItem.prev().hasClass(that.settings.placeholder)) {
          return;
        }
        that.$placeholder.insertBefore($intersectedItem);
        that._updatePlaceholderClass();
        return;
      }
      if (direction === 'down') {
        if ($intersectedItem.next().css('display') === 'none' && $intersectedItem.next().next().hasClass(that.settings.placeholder)) {
          return;
        }
        that.$placeholder.insertAfter($intersectedItem);
        that._updatePlaceholderClass();
      }
    })(this);
  },

  _initStart: function(event) {
    this.$tr = $(event.target).closest('tr');

    this.startPosition = {
      x: event.pageX,
      y: event.pageY
    };

    let trOffset = this.$tr.offset();
    this.trOffset = {
      x: this.startPosition.x - trOffset.left,
      y: this.startPosition.y - trOffset.top
    };
  },

  _initClone: function() {
    this.$clone = this.$tr.clone();
    this.$clone
      .css({
        width: this.$tr.outerWidth(),
        height: this.$tr.outerHeight(),
        position: 'fixed',
        zIndex: 10000,
        opacity: this.settings.opacity
      })
      .addClass('sortable-clone');
    let originalTds = this.$tr.find('> td');
    this.$clone.find('> td').each(function(index) {
      $(this).css('width', originalTds.eq(index).outerWidth());
    });
    this.$el.find('tbody').append(this.$clone);
    this.$el.addClass('table--sorting');
  },

  _initPlaceholder: function() {
    this.placeholderHeight = this.$clone.outerHeight();
    this.$placeholder = $('<tr><td colspan="' + this.$clone.find('td').length + '">&nbsp;</td></tr>');
    this.$placeholder
      .addClass(this.settings.placeholder)
      .find('> td').first()
      .css({
        height: this.placeholderHeight
      });
    this.$tr.after(this.$placeholder);
    this._updatePlaceholderClass();
  },

  _updateClonePosition: function(event) {
    this.$clone.css({
      left: this.settings.offsetLeft(),
      top: event.pageY - this.trOffset.y - this.settings.offsetTop()
    });
  },

  _updatePlaceholderClass: function() {
    let $lastVisibleTr = this.$el.find('tr:visible:not(.sortable-clone):last');
    if ($lastVisibleTr[0] === this.$placeholder[0]) {
      this.$placeholder.addClass('placeholder--last');
    } else {
      this.$placeholder.removeClass('placeholder--last');
    }
  },

  _scroll: function(event) {
    let sParent = this.scrollParent;
    if (sParent !== doc && sParent.tagName !== 'HTML') {
      let overflowOffset = $(this.scrollParent).offset();
      if ((overflowOffset.top + sParent.offsetHeight - event.pageY) < this.settings.scrollSensitivity) {
        sParent.scrollTop = sParent.scrollTop + this.settings.scrollSpeed;
      } else if (event.pageY - overflowOffset.top < this.settings.scrollSensitivity) {
        sParent.scrollTop = sParent.scrollTop - this.settings.scrollSpeed;
      }
      if ((overflowOffset.left + sParent.offsetWidth - event.pageX) < this.settings.scrollSensitivity) {
        sParent.scrollLeft = sParent.scrollLeft + this.settings.scrollSpeed;
      } else if(event.pageX - overflowOffset.left < this.settings.scrollSensitivity) {
        sParent.scrollLeft = sParent.scrollLeft - this.settings.scrollSpeed;
      }
    } else {
      let $doc = $(doc), $win = $(win);
      if (event.pageY - $doc.scrollTop() < this.settings.scrollSensitivity) {
        $doc.scrollTop($doc.scrollTop() - this.settings.scrollSpeed);
      } else if ($win.height() - (event.pageY - $doc.scrollTop()) < this.settings.scrollSensitivity) {
        $doc.scrollTop($doc.scrollTop() + this.settings.scrollSpeed);
      }
      if (event.pageX - $doc.scrollLeft() < this.settings.scrollSensitivity) {
        $doc.scrollLeft($doc.scrollLeft() - this.settings.scrollSpeed);
      } else if ($win.width() - (event.pageX - $doc.scrollLeft()) < this.settings.scrollSensitivity) {
        $doc.scrollLeft($doc.scrollLeft() + this.settings.scrollSpeed);
      }
    }
  },

  _yIntersectsPlaceholder: function(event) {
    let min = this.$placeholder.offset().top;
    let max = min + this.placeholderHeight;
    return (event.pageY >= min && event.pageY <= max);
  },

  _trigger: function(eventType, eventOrigin) {
    this.$el.trigger(eventType, {
      event: eventOrigin
    });
  }
};

$.fn.tSortable = function(options) {
  if (!options || typeof options === 'object') {
    return this.each(function() {
      if (!$(this).data('tSortable')) {
        $(this).data('tSortable', new TableSortable(this, options));
      }
    });
  } else if (typeof options === 'string' && options.charAt(0) !== '_') {
    let tSortable = this.data('tSortable');
    if (!tSortable) {
      throw new Error('tSortable is not initialized on this DOM element.');
    }
    if (tSortable && tSortable[options]) {
      return tSortable[options].apply(tSortable, Array.prototype.slice.apply(arguments, [1]));
    }
  }
  throw new Error('"' + options + '" is no valid api method.');
};

$.fn.tSortable.defaults = {
  handle: 'div',
  items: '> tbody > tr',
  opacity: 0.6,
  placeholder: 'placeholder',
  animateTarget: true,
  animationLength: 300,
  scroll: true,
  scrollSensitivity: 20,
  scrollSpeed: 20,
  offsetTop: () => {
    return 0;
  },
  offsetLeft: () => {
    return 0;
  }
};
