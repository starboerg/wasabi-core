module.exports = function(grunt) {
  "use strict";

  require('jit-grunt')(grunt);
  require('time-grunt')(grunt);
  require('babelify');

  grunt.initConfig({

    //-------------------------------------------- STYLE PROCESSING --------------------------------------------------//
    sass: {
      dev: {
        options: {
          sourceMap: true,
          sourceMapEmbed: true
        },
        files: {
          'src/Assets/_build/css/core.compiled.dev.css': 'src/Assets/sass/styles.scss'
        }
      },
      prod: {
        options: {
          sourceMap: false
        },
        files: {
          'src/Assets/_build/css/core.compiled.prod.css': 'src/Assets/sass/styles.scss'
        }
      }
    },
    postcss: {
      options: {
        map: false,
        processors: [
          require('autoprefixer')()
        ]
      },
      dev: {
        files: {
          'src/Assets/_build/css/core.dev.css': 'src/Assets/_build/css/core.compiled.dev.css'
        }
      },
      prod: {
        files: {
          'src/Assets/_build/css/core.prod.css': 'src/Assets/_build/css/core.compiled.prod.css'
        }
      }
    },
    cssmin: {
      options: {
        shorthandCompacting: false,
        roundingPrecision: -1
      },
      default: {
        files: {
          'src/Assets/_build/css/core.min.css': 'src/Assets/_build/css/core.prod.css'
        }
      }
    },

    //--------------------------------------------- JS PROCESSING ----------------------------------------------------//
    eslint: {
      app: {
        src: ['src/Assets/js/**/*.js'],
        options: {
          quiet: true
        }
      }
    },
    browserify: {
      app: {
        src: [
          'src/Assets/js/Wasabi.js',
          'src/Assets/js/core/WasabiCore.js'
        ],
        dest: 'src/Assets/_build/js/wasabi.js',
        options: {
          debug: true,
          transform: [
            ['babelify', {
              presets: ['es2015'],
              parserOpts: {
                sourceType: 'module'
              },
              plugins: ['transform-object-rest-spread']
            }]
          ]
        }
      }
    },
    uglify: {
      options: {
        mangle: true
      },
      app: {
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
      },
      css: {
        files: [
          { src: 'src/Assets/_build/css/core.dev.css', dest: 'webroot/css/core.css' },
          { src: 'src/Assets/_build/css/core.min.css', dest: 'webroot/css/core.min.css' }
        ]
      }
    },

    //----------------------------------------------- WATCHERS -------------------------------------------------------//
    watch: {
      sass: {
        files: ['src/Assets/sass/**/*.scss'],
        tasks: ['sass', 'postcss', 'cssmin', 'copy:css']
      },
      js: {
        files: ['src/Assets/js/**/*.js'],
        tasks: ['browserify', 'uglify', 'copy:js']
      }
    }

  });

  //--------------------------------------------- REGISTERED TASKS ---------------------------------------------------//
  grunt.registerTask('eslint-run', [
    'eslint'
  ]);

  grunt.registerTask('watch-sass', [
    'watch:sass'
  ]);

  grunt.registerTask('watch-js', [
    'watch:js'
  ]);

  grunt.registerTask('build-js', [
    'eslint-run',
    'browserify',
    'uglify',
    'copy:js'
  ]);

  grunt.registerTask('dev-js', [
    'browserify',
    'copy:js'
  ]);
};
