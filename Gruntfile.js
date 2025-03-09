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

module.exports = function(grunt) {

	// Load all grunt tasks.
	require('load-grunt-tasks')(grunt);

	var buildtime = new Date().toISOString();
	var buildyear = 1900 + new Date().getYear();

	/**
	 * excludes
	 */
	var excludeCopyFiles = [
		'**',
		'!**/bitbucket-pipelines.yml',
		'!contributing.md',
		'!**/css/less/**',
		'!**/css/sass/**',
		'!**/css/src/**',
		'!.editorconfig',
		'!.git*',
		'!.git/**',
		'!**/Gruntfile.js',
		'!**/img/src/**',
		'!**/js/src/**',
		'!**/LICENSE',
		'!LICENSE',
		'!**/*.map',
		'!node_modules/**',
		'!**/package.json',
		'!package-lock.json',
		'!postcss.config.js',
		'!**/README.md',
		'!README.md',
		'!release/**',
		'!.sass-cache/**',
		'!**/tests/**',
		'!webpack.config.js',
	];

	var excludeCopyFilesGIT = excludeCopyFiles.slice(0).concat(
		[
			'!includes/pro/**',
			'!readme.txt',
		]
	);

	var excludeCopyFilesWPorg = excludeCopyFiles.slice(0).concat(
		[
			'!assets/sass/**',
			'!assets/scripts/src/**',
			'!assets/scss/**',
			'!assets/styles/frontend/**',
			'!includes/iworks/iworks-pwa/class-iworks-iworks-pwa-github.php',
			'!includes/pro/**',
			'!languages/*.mo',
			'!languages/*.po',
		]
	);

	var conf = {
		// Concatenate those JS files into a single file (target: [source, source, ...]).
		js_files_concat: {
			'assets/scripts/frontend.js': [
				'assets/scripts/src/frontend/*.js'
			],
			'assets/scripts/admin.js': [
				'assets/scripts/src/admin/*.js'
			]
		},

		// SASS files to process. Resulting CSS files will be minified as well.
		css_files_compile: {
			'assets/styles/frontend/add-to-home-screen.css': 'assets/sass/frontend/add-to-home-screen.scss',
		},

		replace_patterns: [{
			match: /AUTHOR_NAME/g,
			replace: '<%= pkg.author[0].name %>'
		}, {
			match: /AUTHOR_URI/g,
			replace: '<%= pkg.author[0].uri %>'
		}, {
			match: /BUILDTIME/g,
			replace: buildtime
		}, {
			match: /IWORKS_RATE_TEXTDOMAIN/g,
			replace: '<%= pkg.name %>'
		}, {
			match: /IWORKS_OPTIONS_TEXTDOMAIN/g,
			replace: '<%= pkg.name %>'
		}, {
			match: /PLUGIN_DESCRIPTION/g,
			replace: '<%= pkg.description %>'
		}, {
			match: /PLUGIN_GITHUB_WEBSITE/g,
			replace: '<%= pkg.repository.website %>'
		}, {
			match: /PLUGIN_NAME/g,
			replace: '<%= pkg.name %>'
		}, {
			match: /PLUGIN_REQUIRES_PHP/g,
			replace: '<%= pkg.requires.PHP %>'
		}, {
			match: /PLUGIN_REQUIRES_WORDPRESS/g,
			replace: '<%= pkg.requires.WordPress %>'
		}, {
			match: /PLUGIN_TESTED_WORDPRESS/g,
			replace: '<%= pkg.tested.WordPress %>'
		}, {
			match: /PLUGIN_TAGLINE/g,
			replace: '<%= pkg.tagline %>'
		}, {
			match: /PLUGIN_TAGS/g,
			replace: '<%= pkg.tags.join(", ") %>'
		}, {
			match: /PLUGIN_TILL_YEAR/g,
			replace: buildyear
		}, {
			match: /PLUGIN_TITLE/g,
			replace: '<%= pkg.title %>'
		}, {
			match: /PLUGIN_URI/g,
			replace: '<%= pkg.homepage %>'
		}, {
			match: /PLUGIN_VERSION/g,
			replace: '<%= pkg.version %>'
		}, {
			match: /^Version: .+$/g,
			replace: 'Version: <%= pkg.version %>'
		}],

		plugin_dir: '',
		plugin_file: '<%= pkg.name %>.php',

		// Regex patterns to exclude from transation.
		translation: {
			ignore_files: [
				'.git*',
				'includes/external/.*', // External libraries.
				'node_modules/.*',
				'(^.php)', // Ignore non-php files.
				'release/.*', // Temp release files.
				'.sass-cache/.*',
				'tests/.*', // Unit testing.
				'.editorconfig', // editor configuration
			],
			pot_dir: 'languages/', // With trailing slash.
			textdomain: '<%= pkg.name %>',
		},
	};

	// Project configuration
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		// JS - Concat .js source files into a single .js file.
		concat: {
			options: {
				stripBanners: true,
				banner: '/*! <%= pkg.title %> - <%= pkg.version %>\n' +
				' * <%= pkg.homepage %>\n' +
				' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
				' * Licensed <%= pkg.license %>' +
				' */\n'
			},
			scripts: {
				files: conf.js_files_concat
			}
		},

		// JS - Validate .js source code.
		jshint: {
			all: [
				'Gruntfile.js',
				'assets/scripts/src/**/*.js',
				'assets/scripts/test/**/*.js'
			],
			options: {
				curly: true,
				eqeqeq: true,
				immed: true,
				latedef: true,
				newcap: true,
				noarg: true,
				sub: true,
				undef: true,
				boss: true,
				eqnull: true,
				globals: {
					exports: true,
					module: false
				}
			}
		},

		// JS - Uglyfies the source code of .js files (to make files smaller).
		uglify: {
			all: {
				files: [{
					expand: true,
					src: ['*.js', '!**/*.min.js', '!shared*'],
					cwd: 'assets/scripts/',
					dest: 'assets/scripts/',
					ext: '.min.js',
					extDot: 'last'
				}],
				options: {
					banner: '/*! <%= pkg.title %> - <%= pkg.version %>\n' +
					' * <%= pkg.homepage %>\n' +
					' * Copyright (c) <%= grunt.template.today("yyyy") %>;' +
					' * Licensed <%= pkg.license %>' +
					' */\n',
					mangle: {
						except: ['jQuery']
					}
				}
			}
		},

		test: {
			files: ['assets/scripts/test/**/*.js']
		},

		/**
		 * TEST - Run the PHPUnit tests.
		 * -- Not used right now...
		 */
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
				tap: true,
				staticBackup: false,
				noGlobalsBackup: false
			}
		},

		// CSS - Compile a .scss file into a normal .css file.
		sass: {
			all: {
				options: {
					'sourcemap=none': true, // 'sourcemap': 'none' does not work...
					unixNewlines: true,
					style: 'expanded'
				},
				files: conf.css_files_compile
			}
		},
		concat_css: {
			options: {},
			all: {
				src: ['assets/styles/frontend/settings.css', 'assets/styles/frontend/*.css'],
				dest: 'assets/styles/<%= pkg.name %>-frontend.css'
			}
		},

		// CSS - Minify all .css files.
		cssmin: {
			options: {
				banner: '/*! <%= pkg.title %> - <%= pkg.version %>\n' +
				' * <%= pkg.homepage %>\n' +
				' * Copyright (c) <%= grunt.template.today("yyyy") %>;\n' +
				' * Licensed <%= pkg.license %>' +
				' */\n',
				mergeIntoShorthands: false
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
		watch: {
			sass: {
				files: [
					'assets/sass/*.scss',
					'assets/sass/**/*.scss',
					'include/modules/**/*.scss'
				],
				tasks: ['sass', 'concat_css', 'cssmin'],
				options: {
					debounceDelay: 500
				}
			},
			scripts: {
				files: [
					'assets/scripts/src/**/*.js',
					'assets/scripts/admin/src/**/*.js',
				],
				tasks: ['jshint', 'concat', 'uglify'],
				options: {
					debounceDelay: 500
				}
			}
		},

		// BUILD - Remove previous build version and temp files.
		clean: {
			wporg: {
				src: [
					'release/wporg/<%= pkg.version %>',
					'release/wporg/<%= pkg.name %>.zip'
				]
			},
			github: {
				src: [
					'release/github/<%= pkg.version %>',
					'release/github/<%= pkg.name %>.zip'
				]
			},
			temp: {
				src: ['**/*.tmp', '**/.afpDeleted*', '**/.DS_Store'],
				dot: true,
				filter: 'isFile'
			}
		},

		// BUILD - update the translation index .po file.
		makepot: {
			target: {
				options: {
					domainPath: conf.translation.pot_dir,
					exclude: conf.translation.ignore_files,
					mainFile: conf.plugin_file,
					potFilename: conf.translation.textdomain + '.pot',
					potHeaders: {
						poedit: true, // Includes common Poedit headers.
						'project-id-version': '<%= pkg.version %>',
						'language-team': 'iWorks <support@iworks.pl>',
						'last-translator': '<%= pkg.translator.name %> <<%= pkg.translator.email %>>',
						'report-msgid-bugs-to': 'http://iworks.pl',
						'x-poedit-keywordslist': true // Include a list of all possible gettext functions.
					},
					exclude: ['node_modules', '.git', '.sass-cache', 'release'],
					type: 'wp-plugin',
					updateTimestamp: true,
					updatePoFiles: true
				}
			}
		},

		potomo: {
			dist: {
				options: {
					poDel: false
				},
				files: [{
					expand: true,
					cwd: conf.translation.pot_dir,
					src: ['*.po'],
					dest: conf.translation.pot_dir,
					ext: '.mo',
					nonull: true
				}]
			}
		},

		copy: {
			// Copy the plugin to a versioned release directory
			wporg: {
				src: excludeCopyFilesWPorg,
				dest: 'release/wporg/<%= pkg.version %>/<%= pkg.name %>/'
			},
			github: {
				src: excludeCopyFilesGIT,
				dest: 'release/github/<%= pkg.version %>/<%= pkg.name %>/'
			}
		},

		// BUILD: Replace conditional tags in code.
		replace: {
			options: {
				patterns: conf.replace_patterns
			},
			files: {
				expand: true,
				src: [
					'release/*/<%= pkg.version %>/<%= pkg.name %>/**',
					'!release/*/**/*.gif',
					'!release/*/**/images/**',
					'!release/*/**/*.jpg',
					'!release/*/**/languages/*.mo',
					'!release/*/**/*.png',
					'!release/*/**/*.webp',
				],
				dest: '.'
			}
		},

		compress: {
			wporg: {
				options: {
					mode: 'zip',
					archive: './release/wporg/<%= pkg.name %>.zip'
				},
				expand: true,
				cwd: 'release/wporg/<%= pkg.version %>/',
				src: ['**/*'],
				dest: '.'
			},
			github: {
				options: {
					mode: 'zip',
					archive: './release/github/<%= pkg.name %>.zip'
				},
				expand: true,
				cwd: 'release/github/<%= pkg.version %>/',
				src: ['**/*'],
				dest: '.'
			}
		},

		checktextdomain: {
			options: {
				text_domain: ['<%= pkg.name %>', 'IWORKS_RATE_TEXTDOMAIN', 'IWORKS_OPTIONS_TEXTDOMAIN'],
				keywords: [ //List keyword specifications
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				]
			},
			files: {
				src: ['<%= pkg.name %>.php', 'includes/**/*.php'],
				expand: true,
			},
		},
	});

	grunt.registerTask('notes', 'Show release notes', function() {
		grunt.log.subhead('Release notes');
		grunt.log.writeln('  1. Check FORUM for open threads');
		grunt.log.writeln('  2. REPLY to forum threads + unsubscribe');
		grunt.log.writeln('  3. Update the TRANSLATION files');
		grunt.log.writeln('  4. Generate ARCHIVE');
		grunt.log.writeln('  5. Check ARCHIVE structure - it should be a folder with plugin name');
		grunt.log.writeln('  6. INSTALL on a clean WordPress installation');
		grunt.log.writeln('  7. RELEASE the plugin on WordPress.org!');
		grunt.log.writeln('  8. Add git tag!');
		grunt.log.writeln('  9. RELEASE the plugin on GitHub!');
		grunt.log.writeln(' 10. RELEASE the plugin!');
	});

	// Default task.

	grunt.registerTask('default', ['clean:temp', 'concat', 'uglify', 'sass', 'concat_css', 'cssmin']);
	grunt.registerTask('js', ['concat', 'uglify']);
	grunt.registerTask('css', ['sass', 'concat_css', 'cssmin']);
	grunt.registerTask('i18n', ['checktextdomain', 'makepot']);

	grunt.registerTask(
		'build',
		[
			'default',
			'i18n',
			'clean:wporg',
			'copy:wporg',
			'replace',
			'compress:wporg'
		],
	);
	grunt.registerTask(
		'build:github',
		[
			'default',
			'i18n',
			'potomo',
			'clean:github',
			'copy:github',
			'replace',
			'compress:github'
		]
	);
	grunt.registerTask('release', ['build:wporg', 'build:github', 'notes' ]);
	grunt.registerTask('test', ['phpunit', 'jshint', 'notes']);
	grunt.util.linefeed = '\n';
};
