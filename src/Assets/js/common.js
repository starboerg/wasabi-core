require.config({
  config: {
    moment: {
      noGlobal: true
    }
  },
  paths: {
    backbone: 'vendor/backbone/backbone',
    bootstrap: 'vendor/bootstrap/dist/js/bootstrap',
    jquery: 'vendor/jquery/dist/jquery',
    purl: 'vendor/purl/purl',
    spin: 'vendor/spin.js/spin',
    underscore: 'vendor/underscore/underscore',
    'wasabi.BaseView': 'common/BaseView',
    'wasabi.core': 'core/main',
    'jquery.color': 'vendor/jquery-color/jquery.color',
    'jquery.eventMagic': 'common/lib/frankfoerster/jquery.eventMagic',
    'jquery.livequery': 'common/lib/frankfoerster/jquery.livequery',
    'jquery.nSortable': 'common/lib/frankfoerster/jquery.nSortable',
    'jquery.scrollParent': 'common/lib/frankfoerster/jquery.scrollParent',
    'jquery.spin': 'vendor/spin.js/jquery.spin',
    'jquery.tSortable': 'common/lib/frankfoerster/jquery.tSortable',
    'bootstrap.dropdown': 'vendor/bootstrap/js/dropdown'
  },
  packages: [
    {
      name: 'feature-detects',
      location: '../../../node_modules/modernizr/feature-detects'
    }
  ],
  shim: {
    backbone: {
      deps: [
        'underscore',
        'jquery'
      ],
      exports: 'Backbone'
    },
    underscore: {
      exports: '_'
    },
    jquery: {
      exports: '$'
    },
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
    ]
  }
});

// removed in production by uglify
if (typeof DEBUG === 'undefined') {
  DEBUG = true;
}
