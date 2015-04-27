module.exports = function(grunt) {
  "use strict";

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
    browserifyResolve: {
      options: {
        bower: grunt.file.readJSON('bower.json'),
        pkg: grunt.file.readJSON('package.json')
      },
      common: {
        vendorOnly: true,
        dest: 'webroot/js/common.js'
      },
      core: {
        require: {
          'wasabi.BaseView': './src/Assets/js/common/BaseView.js',
          'wasabi.SpinPresets': './src/Assets/js/common/SpinPresets.js',
          'wasabi/core': './src/Assets/js/core/main.js'
        },
        files: {
          'webroot/js/wasabi.js': ['src/Assets/js/wasabi.js']
        }
      }
    },
    uglify: {
      options: {
        compress: {
          global_defs: {
            "DEBUG": false
          }
        }
      },
      all: {
        files: {
          'webroot/js/common.min.js': ['webroot/js/common.js'],
          'webroot/js/wasabi.min.js': ['webroot/js/wasabi.js']
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
    'browserifyResolve:common',
    'browserifyResolve:core',
    'uglify:all'
  ]);

  grunt.loadNpmTasks('grunt-browserify-resolve');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-imagemin');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');
};
