require.config({
  config: {
    moment: {
      noGlobal: true
    }
  },
  paths: {
    backbone: 'vendor/backbone/backbone',
    marionette: 'vendor/marionette/lib/backbone.marionette',
    bootstrap: 'vendor/bootstrap/dist/js/bootstrap',
    jquery: 'vendor/jquery/dist/jquery',
    purl: 'vendor/purl/purl',
    spin: 'vendor/spin.js/spin',
    underscore: 'vendor/underscore/underscore',
    'wasabi.core': 'core/main',
    'jquery.color': 'vendor/jquery-color/jquery.color',
    'jquery.eventMagic': 'common/jquery-plugins/jquery.eventMagic',
    'jquery.scrollParent': 'common/jquery-plugins/jquery.scrollParent',
    'jquery.livequery': 'common/jquery-plugins/jquery.livequery',
    'jquery.nSortable': 'common/jquery-plugins/jquery.nSortable',
    'jquery.spin': 'vendor/spin.js/jquery.spin',
    'jquery.tSortable': 'common/jquery-plugins/jquery.tSortable',
    'jquery.position': 'common/jquery-plugins/jquery.position',
    'bootstrap.dropdown': 'vendor/bootstrap/js/dropdown'
  },
  hbs: { // optional
    helpers: true,            // default: true
    i18n: false,              // default: false
    templateExtension: 'hbs', // default: 'hbs'
    partialsUrl: ''           // default: ''
  },
  packages: [
    {
      name: 'feature-detects',
      location: '../../../node_modules/modernizr/feature-detects'
    }
  ],
  shim: {
    backbone: [
      'underscore',
      'jquery'
    ],
    marionette: [
      'backbone'
    ],
    'bootstrap.dropdown': [
      'jquery'
    ],
    'jquery.tooltipster': [
      'jquery'
    ],
    'jquery.color': [
      'jquery'
    ],
    'jquery.livequery': [
      'jquery'
    ],
    'jquery.spin': [
      'jquery'
    ],
    'jquery.resize': [
      'jquery'
    ],
    'jquery.perfectScrollbar': [
      'jquery'
    ],
    'jquery.nSortable': [
      'jquery'
    ],
    'jquery.tSortable': [
      'jquery'
    ],
    'jquery.position': [
      'jquery'
    ]
  }
});

// removed in production by uglify
if (typeof DEBUG === 'undefined') {
  DEBUG = true;
}
