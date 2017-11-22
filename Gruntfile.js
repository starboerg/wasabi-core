module.exports = function(grunt) {
  "use strict";

  require('jit-grunt')(grunt);
  require('time-grunt')(grunt);
  require('babelify');

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
    browserify: {
      wasabi: {
        src: [
          'src/Assets/js/Wasabi.js',
          'src/Assets/js/core/WasabiCore.js'
        ],
        dest: 'src/Assets/_build/js/wasabi.js',
        options: {
          debug: true,
          transform: [
            ['babelify', {
              presets: [
                ['es2015'/*, { modules: 'commonjs' }*/]
              ],
              parserOpts: {
                sourceType: 'module'
              }
            }]
          ]
        }
      }
    },
    uglify: {
      options: {
        mangle: true
      },
      wasabi: {
        files: {
          'src/Assets/_build/js/wasabi.min.js': ['src/Assets/_build/js/wasabi.js']
        }
      }
    },
    copy: {
      js: {
        files: [
          { src: 'src/Assets/_build/js/wasabi.js', dest: 'webroot/js/wasabi.js' },
          { src: 'src/Assets/_build/js/wasabi.min.js', dest: 'webroot/js/wasabi.min.js' }
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
    'browserify:wasabi',
    'uglify:wasabi',
    'copy:js'
  ]);

  grunt.registerTask('build-js', [
    'browserify:wasabi',
    'uglify:wasabi',
    'copy:js'
  ]);

  grunt.registerTask('watch2', [
    'watch:less',
    'watch:commonjs',
    'watch:appjs'
  ]);

  require('jit-grunt')(grunt);
};
