module.exports = function(grunt) {
  "use strict";

  // measures the time each task takes
  require('time-grunt')(grunt);

  var vendorModules = [
    'jquery',
    'underscore',
    'backbone',
    'marionette',
    'purl',
    'bootstrap.dropdown',
    'common/BaseView',
    'jquery.eventMagic',
    'jquery.scrollParent',
    'jquery.livequery',
    'jquery.nSortable',
    'jquery.tSortable',
    'jquery.color',
    'bootstrap.dropdown',
    'bootstrap.transition',
    'bootstrap.collapse'
  ];

  grunt.initConfig({

    //-------------------------------------------- STYLE PROCESSING --------------------------------------------------//
    less: {
      compile: {
        options: {
          sourceMap: true,
          sourceMapURL: 'core.css.map'
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

          fileExclusionRegExp: /^\.|\.md$|^LICENSE$|\.json$|^src$|\.map$|^demo$|^test$|^tests$|\.TXT$|\.txt$|^fonts$|^css$|\.css$|^less$|\.less$|^grunt$|\.sh$|^r.js$/,

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
        tasks: ['jshint:core', 'requirejs:compile']
      }
    }

  });

  //--------------------------------------------- REGISTERED TASKS ---------------------------------------------------//
  grunt.registerTask('default', [
    'less:compile',
    'cssmin:core',
    'jshint:core',
    'requirejs:compile',
    'copy:js'
  ]);

  grunt.registerTask('watch2', [
    'watch:less',
    'watch:commonjs',
    'watch:appjs'
  ]);

  require('jit-grunt')(grunt);
};
