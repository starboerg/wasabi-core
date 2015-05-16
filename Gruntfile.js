module.exports = function(grunt) {
  "use strict";

  var vendorModules = [
    'jquery',
    'underscore',
    'backbone',
    'purl',
    'spin',
    'bootstrap.dropdown',
    'common/BaseView',
    'common/util/eventify',
    'jquery.eventMagic',
    'jquery.scrollParent',
    'jquery.livequery',
    'jquery.spin',
    'jquery.tSortable',
    'bootstrap.dropdown'
  ];

  grunt.initConfig({

    pkg: grunt.file.readJSON('package.json'),

    //-------------------------------------------- STYLE PROCESSING --------------------------------------------------//
    less: {
      compile: {
        options: {
          sourceMap: true
        },
        files: {
          'webroot/css/core.css': 'src/Assets/less/core.less'
        }
      }
    },
    cssmin: {
      options: {
        shorthandCompacting: false,
        roundingPrecision: -1
      },
      core: {
        files: {
          'webroot/css/core.min.css': 'webroot/css/core.css'
        }
      }
    },

    //--------------------------------------------- JS PROCESSING -----------------------------------------------------//
    jshint: {
      core: [
        'package.json',
        'Gruntfile.js',
        'src/Assets/js/**/*.js'
      ]
    },
    bowerRequirejs: {
      all: {
        rjsConfig: './src/Assets/js/common.js',
        options: {
          baseUrl: './src/Assets/js/'
        }
      }
    },
    requirejs: {
      compile: {
        options: {
          // Define our base URL - all module paths are relative to this
          // base directory.
          baseUrl: 'src/Assets/js',

          // Define our build directory. All files in the base URL will be
          // COPIED OVER into the build directory as part of the
          // concatentation and optimization process.
          dir: 'src/Assets/_build/js/',

          // Load the RequireJS config() definition from the config.js file.
          // Otherwise, we'd have to redefine all of our paths again here.
          mainConfigFile: 'src/Assets/js/common.js',
          findNestedDependencies: true,

          waitSeconds: 0,

          // Define the modules to compile.
          modules: [
            {
              name: 'common',
              include: vendorModules
            },
            // Main application module.
            // All basic resources, vendor files will be included here automatically.
            // We also merge all other modules that are used in different frontend parts
            // of the app e.g. for the Catalog and Accident plugins.
            {
              name: 'wasabi',
              include: [
                'wasabi',
                'wasabi.core'
              ],
              exclude: [
                'common'
              ]
            }
          ],

          // Minify all js files via uglify2 and set DEBUG to false during the build.
          // This way you can use statements like:
          //      if (DEBUG) {
          //        console.log('foobar')
          //      }
          // during development (Configure::write('debug', 2))
          // which will be excluded from the build.
          optimize: 'uglify2',
          uglify2: {
            compress:{
              global_defs: {
                DEBUG: false
              }
            }
          }
        }
      }
    },
    copy: {
      js: {
        files: [
          { src: 'src/Assets/_build/js/common.js', dest: 'webroot/js/common.js' },
          { src: 'src/Assets/_build/js/wasabi.js', dest: 'webroot/js/wasabi.js' }
        ]
      }
    },
    browserifyResolve: {
      options: {
        bower: grunt.file.readJSON('bower.json'),
        pkg: grunt.file.readJSON('package.json')
      },
      common: {
        vendorOnly: true,
        require: {
          'jquery.livequery': './src/Assets/js/common/lib/frankfoerster/jquery.livequery.js',
          'jquery.eventMagic': './src/Assets/js/common/lib/frankfoerster/jquery.eventMagic.js',
          'jquery.scrollParent': './src/Assets/js/common/lib/frankfoerster/jquery.scrollParent.js',
          'jquery.tSortable': './src/Assets/js/common/lib/frankfoerster/jquery.tSortable.js'
        },
        dest: 'webroot/js/common.js'
      },
      core: {
        require: {
          'wasabi.BaseView': './src/Assets/js/common/BaseView.js',
          'wasabi.SpinPresets': './src/Assets/js/common/SpinPresets.js',
          'wasabi/core': './src/Assets/js/core/main.js'
        },
        external: [
          'jquery.livequery',
          'jquery.eventMagic',
          'jquery.scrollParent',
          'jquery.tSortable'
        ],
        files: {
          'webroot/js/wasabi.js': ['src/Assets/js/wasabi.js']
        }
      }
    },

    //----------------------------------------------- WATCHERS -------------------------------------------------------//
    watch: {
      less: {
        files: ['src/Assets/less/**/*.less'],
        tasks: ['less:compile', 'cssmin:core']
      },
      commonjs: {
        files: ['src/vendor/**/*.js'],
        tasks: ['browserifyResolve:common']
      },
      appjs: {
        files: ['src/Assets/js/**/*.js'],
        tasks: ['jshint:core', 'browserifyResolve:core']
      }
    }

  });

  //--------------------------------------------- REGISTERED TASKS ---------------------------------------------------//
  grunt.registerTask('default', [
    'less:compile',
    'cssmin:core',
    'jshint:core',
    'uglify:all'
  ]);

  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-requirejs');
  grunt.loadNpmTasks('grunt-bower-requirejs');
  grunt.loadNpmTasks('grunt-bytesize');
};
