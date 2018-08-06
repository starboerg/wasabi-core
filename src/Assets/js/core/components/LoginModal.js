import _ from 'underscore';
import $ from 'jquery';
import {View} from 'backbone.marionette';

const LoginModal = View.extend({

  template: '',
  isOpen: false,
  heartBeatTimeout: 0,
  heartbeat: 18000,

  events: {
    'submit form': 'onSubmit'
  },

  initialize (options) {
    this.template = _.template($('#wasabi-core-login-modal').html());
    this.eventBus = options.eventBus;
    this.WS = options.WS;
    this.$body = $('body');

    this.handleHeartBeatResponse = this.handleHeartBeatResponse.bind(this);
    this.authError = this.authError.bind(this);
    this.initHeartBeat = this.initHeartBeat.bind(this);

    this.eventBus.on('init-heartbeat', this.initHeartBeat);
    this.eventBus.on('auth-error', this.authError);
  },

  openModal () {
    if (this.isOpen) {
      return;
    }
    this.isOpen = true;
    this.$body.addClass('modal-open');
    this.$modal = $(this.template());
    this.setElement(this.$modal);
    this.$button = this.$('button');
    this.$modal.appendTo(this.$body);
    this.$('#email').focus();
  },

  onSubmit (event) {
    event.preventDefault();

    let $form = this.$('form');

    $.ajax({
      url: $form.attr('action'),
      data: $form.serialize(),
      method: 'post',
      dataType: 'json',
      success: (responseData) => {
        if (responseData.error) {
          this.$('.modal-body').html(responseData.data.content || '');
          this.$('#email').focus();
          let loadingButton = this.$button.data('loadingButton');
          loadingButton.toggleLoadingState();
        } else {
          this.closeModal();
          this.initHeartBeat();
        }
      }
    });
  },

  closeModal () {
    $.when.apply(null, [
      this.$('.modal-container').fadeOut(200),
      this.$('.modal-backdrop').fadeOut(200)
    ]).then($.proxy(function() {
      if (this.$modal) {
        this.$modal.remove();
      }
      this.$body.removeClass('modal-open');
      this.isOpen = false;
    }, this));
  },

  initHeartBeat (heartbeat) {
    if (typeof heartbeat !== 'undefined') {
      this.heartbeat = heartbeat;
    }
    clearTimeout(this.heartBeatTimeout);
    this.heartBeatTimeout = setTimeout(() => {
      $.ajax({
        url: this.WS.getModule('Wasabi/Core').options.heartbeatEndpoint,
        method: 'post',
        dataType: 'json',
        success: this.handleHeartBeatResponse,
        error: this.authError,
        heartBeat: true
      });
    }, this.heartbeat);
  },

  handleHeartBeatResponse (responseData) {
    if (responseData.error) {
      this.authError();
    } else {
      this.initHeartBeat();
    }
  },

  authError () {
    clearTimeout(this.heartBeatTimeout);
    this.openModal();
  }
});

export default LoginModal;
