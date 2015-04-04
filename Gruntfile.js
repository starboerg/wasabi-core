module.exports = function(grunt) {
  "use strict";

  grunt.loadNpmTasks('grunt-browserify');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-imagemin');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');

  grunt.initConfig({

    pkg: grunt.file.readJSON('package.json'),

    less: {
      compile: {
        options: {
          sourceMap: true
        },
        files: {
          'webroot/css/core.css': 'src/Assets/less/core.less'
        }
      },
      minify: {
        options: {
          cleancss: true
        },
        files: {
          'webroot/css/core.min.css': 'webroot/css/core.css'
        }
      }
    },

    jshint: {
      options: {

      },
      all: [
        'package.json',
        'Gruntfile.js',
        'src/Assets/js/src/**/*.js'
      ]
    },

    watch: {
      less: {
        files: ['src/Assets/less/**/*.less'],
        tasks: ['less']
      }
    }

  });

  grunt.registerTask('default', ['less', 'jshint']);
  grunt.registerTask('build', ['less', 'jshint']);
};
