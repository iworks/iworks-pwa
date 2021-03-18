/*global require*/

/**
 * When grunt command does not execute try these steps:
 *
 * - delete folder 'node_modules' and run command in console:
 *   $ npm install
 *
 * - Run test-command in console, to find syntax errors in script:
 *   $ grunt hello
 */

module.exports = function( grunt ) {
	// Show elapsed time at the end.
	require( 'time-grunt' )(grunt);

	// Load all grunt tasks.
	require( 'load-grunt-tasks' )(grunt);

	var buildtime = new Date().toISOString();
	var buildyear = 1900 + new Date().getYear();

	var conf = {

		// Concatenate those JS files into a single file (target: [source, source, ...]).
		js_files_concat: {
            'assets/scripts/frontend.js': [
                'assets/scripts/src/frontend/service-worker-loader.js'
            ]
		},

		// SASS files to process. Resulting CSS files will be minified as well.
		css_files_compile: {
		},
		css_files_concat: {
		},


		// Regex patterns to exclude from transation.
		translation: {
			ignore_files: [
				'.git*',
				'inc/external/.*', // External libraries.
				'node_modules/.*',
				'(^.php)',		 // Ignore non-php files.
				'release/.*',	  // Temp release files.
				'.sass-cache/.*',
				'tests/.*',		// Unit testing.
			],
			pot_dir: 'languages/', // With trailing slash.
			textdomain: '<%= pkg.name %>',
		},

		dev_plugin_file: '<%= pkg.name %>.php',
		dev_plugin_dir: '<%= pkg.name %>/',

		// BUILD patterns to exclude code for specific builds.
		replaces: {
			patterns: [
				{ match: /PLUGIN_VERSION/g, replace: '<%= pkg.version %>' },
				{ match: /PLUGIN_TAGLINE/g, replace: '<%= pkg.tagline %>' },
				{ match: /BUILDTIME/g, replace: buildtime }
			],

			// Files to apply above patterns to (not only php files).
			files: {
				expand: true,
				src: [
					'**/*.php',
					'**/*.css',
					'**/*.js',
					'**/*.html',
					'**/*.txt',
					'!node_modules/**',
					'!lib/**',
					'!docs/**',
					'!Gruntfile.js',
					'!package-lock.json',
					'!build/**',
					'!tests/**',
					'!.git/**',
					'!stylelint.config.js',
					'!vendor',
					'!vendor/*',
					'!vendor/**',
					'!release',
					'!release/*',
					'!release/**',
				],
				dest: './release/<%= pkg.name %>/'
			}
		}
    };

	// Project configuration
	grunt.initConfig( {
		pkg: grunt.file.readJSON( 'package.json' ),

		// JS - Concat .js source files into a single .js file.
		concat: {
			options: {
				stripBanners: true,
				banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
					' * <%= pkg.homepage %>\n' +
					' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
					' * Licensed GPLv2+\n' +
					' */\n'
			},
			scripts: {
				files: conf.js_files_concat
			}
		},

		// CSS - concat .css source files into single .css file
		concat_css: {
			options: {
				stripBanners: true,
				banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
					' * <%= pkg.homepage %>\n' +
					' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
					' * Licensed GPLv2+\n' +
					' */\n'
			},
			scripts: {
				files: conf.css_files_concat
			}
		},

		// JS - Validate .js source code.
		jshint: {
			all: [
				'Gruntfile.js',
				'scripts/src/**/*.js',
			],
			options: {
				curly:   true,
				eqeqeq:  true,
				immed:   true,
				latedef: true,
				newcap:  true,
				noarg:   true,
				sub:	 true,
				undef:   true,
				boss:	true,
				eqnull:  true,
				globals: {
					exports: true,
					module:  false
				}
			}
		},

		// JS - Uglyfies the source code of .js files (to make files smaller).
		uglify: {
			all: {
				files: [{
					expand: true,
					src: ['*.js', '!*.min.js'],
					cwd: 'assets/scripts/',
					dest: 'assets/scripts/',
					ext: '.min.js',
					extDot: 'last'
				}],
				options: {
					banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
						' * <%= pkg.homepage %>\n' +
						' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
						' * Licensed GPLv2+' +
						' */\n',
					mangle: {
						except: ['jQuery']
					}
				}
			}
		},


		// TEST - Run the PHPUnit tests.
		/* -- Not used right now...
		phpunit: {
			classes: {
				dir: ''
			},
			options: {
				bin: 'phpunit',
				bootstrap: 'tests/php/bootstrap.php',
				testsuite: 'default',
				configuration: 'tests/php/phpunit.xml',
				colors: true,
				//tap: true,
				//testdox: true,
				//stopOnError: true,
				staticBackup: false,
				noGlobalsBackup: false
			}
		},
		*/

		// CSS - Compile a .scss file into a normal .css file.
		sass:   {
			all: {
				options: {
					'sourcemap=none': true, // 'sourcemap': 'none' does not work...
					unixNewlines: true,
					style: 'expanded'
				},
				files: conf.css_files_compile
			}
		},

		// CSS - Automaticaly create prefixed attributes in css file if needed.
		//	   e.g. add `-webkit-border-radius` if `border-radius` is used.
		autoprefixer: {
			options: {
				browsers: ['last 2 version', 'ie 8', 'ie 9'],
				diff: false
			},
			single_file: {
				files: [{
					expand: true,
					src: ['**/*.css', '!**/*.min.css'],
					cwd: 'assets/styles/',
					dest: 'assets/styles/',
					ext: '.css',
					extDot: 'last',
					flatten: false
				}]
			}
		},

		// CSS - Required for CSS-autoprefixer and maybe some SCSS function.
		compass: {
			options: {
			},
			server: {
				options: {
					debugInfo: true
				}
			}
		},

		// CSS - Minify all .css files.
		cssmin: {
			options: {
				banner: '/*! <%= pkg.title %> - v<%= pkg.version %>\n' +
					' * <%= pkg.homepage %>\n' +
					' * Copyright (c) <%= grunt.template.today("yyyy") %>;\n' +
					' * Licensed GPLv2+\n' +
					' */\n'
			},
			minify: {
				expand: true,
				src: ['*.css', '!*.min.css'],
				cwd: 'assets/styles/',
				dest: 'assets/styles/',
				ext: '.min.css',
				extDot: 'last'
			}
		},


		// WATCH - Watch filesystem for changes during development.
		watch:  {
			sass: {
				files: ['assets/sass/**/*.scss', 'assets/sass/externals/*.scss'],
				tasks: ['sass', 'autoprefixer', 'concat_css', 'cssmin' ],
				options: {
					debounceDelay: 500
				}
			},

			scripts: {
				files: ['assets/scripts/src/**/*.js', 'assets/scripts/admin/src/**/*.js'],
				tasks: ['jshint', 'concat', 'uglify'],
				options: {
					debounceDelay: 500
				}
			},

			po2mo: {
				files: {
					src: conf.translation.pot_dir + '<%= pkg.name %>-pl_PL.po',
					desc: conf.translation.pot_dir + '<%= pkg.name %>-pl_PL.mo'
                }
			}
		},

		copy: {
			release: {
				expand: true,
				src: [
					'*',
					'**',
					'!languages/*~',
					'!release',
					'!release/*',
					'!release/**',
					'!node_modules',
					'!node_modules/*',
					'!node_modules/**',
					'!bitbucket-pipelines.yml',
					'!.idea', // PHPStorm settings
					'!.git',
					'!Gruntfile.js',
					'!package.json',
					'!package-lock.json',
					'!tests/*',
					'!tests/**',
					'!assets/js/src',
					'!assets/js/src/*',
					'!assets/js/src/**',
					'!assets/sass',
					'!assets/sass/*',
					'!assets/sass/**',
					'!phpcs.xml.dist',
					'!README.md',
					'!composer.json',
					'!composer.lock',
					'!stylelint.config.js'
				],
				dest: './release/<%= pkg.name %>/',
				noEmpty: true
			}
		},

		// BUILD - Create a zip-version of the plugin.
		compress: {
			target: {
				options: {
					mode: 'zip',
					archive: './release/<%= pkg.name %>-<%= pkg.version %>.zip'
				},
				expand: true,
				cwd: './release/<%= pkg.name %>/',
				src: [ '**/*' ]
			}
		},



		// BUILD - update the translation index .po file.
		makepot: {
			target: {
				options: {
					cwd: '',
					domainPath: conf.translation.pot_dir,
					exclude: conf.translation.ignore_files,
					mainFile: conf.dev_plugin_file,
					potFilename: conf.translation.textdomain + '.pot',
					potHeaders: {
						poedit: true, // Includes common Poedit headers.
						'x-poedit-keywordslist': true // Include a list of all possible gettext functions.
					},
					updatePoFiles: true,
//					exclude: [ 'node_modules', '.git', '.sass-cache', 'release' ],
					type: 'wp-plugin' // wp-plugin or wp-theme
				}
			}
		},

		po2mo: {
			files: {
				src: 'languages/<%= pkg.name %>-pl_PL.po',
				dest: 'languages/<%= pkg.name %>-pl_PL.mo',
			},
		},

		// BUILD: Replace conditional tags in code.
		replace: {
			target: {
				options: {
					patterns: conf.replaces.patterns
				},
				files: [ conf.replaces.files ]
			}
		},

		clean: {
			options: { force: true },
			release: {
				options: { force: true },
				src: [ './release', './release/*', './release/**' ]
			}
		},

        eslint: {
            target: conf.js_files_concat['assets/scripts/frontend.js']
        },

	} );

	// Test task.
	grunt.registerTask( 'hello', 'Test if grunt is working', function() {
		grunt.log.subhead( 'Hi there :)' );
		grunt.log.writeln( 'Looks like grunt is installed!' );
	});


	grunt.registerTask( 'release', 'Generating release copy', function() {
		grunt.task.run( 'clean' );
		grunt.task.run( 'js' );
		grunt.task.run( 'css' );
		grunt.task.run( 'makepot' );

		//		grunt.task.run( 'po2mo');
		grunt.task.run( 'copy' );
		grunt.task.run( 'replace' );
		grunt.task.run( 'compress' );
	});
	// Default task.

	grunt.registerTask( 'default', ['clean', 'jshint', 'concat', 'uglify', 'concat_css', 'sass', 'autoprefixer', 'cssmin' ] );
	grunt.registerTask( 'build', [ 'release' ]);
	grunt.registerTask( 'js', ['jshint', 'eslint', 'concat', 'uglify'] );
	grunt.registerTask( 'css', ['concat_css', 'sass', 'autoprefixer', 'cssmin' ] );
	grunt.registerTask( 'i18n', ['makepot', 'po2mo' ] );
	//grunt.registerTask( 'test', ['phpunit', 'jshint'] );

	grunt.task.run( 'clear' );
	grunt.util.linefeed = '\n';
};
