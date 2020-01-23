"use strict";

/**
 * NPM packages.
 */
const gulp      = require( 'gulp' ),
      rename    = require( 'gulp-rename' ),
      babel     = require( 'gulp-babel'),
      uglify    = require( 'gulp-uglify' ),
      uglifycss = require( 'gulp-uglifycss' ),
      sass      = require( 'gulp-sass' ),
      del       = require( 'del' ),
      gutil     = require( 'gulp-util' ),
      jshint    = require( 'gulp-jshint' ),
      wpPot     = require('gulp-wp-pot');

/**
 * Admin scripts.
 */
gulp.task( 'compileAdminScripts', () => {
	return gulp.src( ['assets/src/js/admin/*.js'] )
		.pipe( jshint() )
		.pipe( jshint.reporter( 'default' ) )
		.pipe( babel(
			{ 'presets': ['env'] }
		) )
		.on( 'error', gutil.log )
		.pipe( rename( { prefix: 'brm-' } ) )
		.pipe( gulp.dest( 'assets/dist/js/admin' ) );
} );

gulp.task( 'minifyAdminScripts', ['compileAdminScripts'], () => {
	return gulp.src( [
		'assets/dist/js/admin/*.js',
		'!assets/dist/js/admin/*.min.js',
	] )
		.pipe( uglify() )
		.pipe( rename( { suffix: '.min' } ) )
		.pipe( gulp.dest( 'assets/dist/js/admin' ) );
} );

/**
 * Frontend scripts.
 */
gulp.task( 'compileFrontendScripts', () => {
	return gulp.src( ['assets/src/js/public/*.js'] )
		.pipe( jshint() )
		.pipe( jshint.reporter( 'default' ) )
		.pipe( babel(
			{ 'presets': ['env'] }
		) )
		.on( 'error', gutil.log )
		.pipe( rename( { prefix: 'brm-' } ) )
		.pipe( gulp.dest( 'assets/dist/js/public' ) );
} );

gulp.task( 'minifyFrontendScripts', ['compileFrontendScripts'], () => {
	return gulp.src( [
		'assets/dist/js/public/*.js',
		'!assets/dist/js/public/*.min.js',
	] )
		.pipe( uglify() )
		.pipe( rename( { suffix: '.min' } ) )
		.pipe( gulp.dest( 'assets/dist/js/public' ) );
} );

/**
 * Admin styles.
 */
gulp.task( 'compileAdminSass', () => {
	return gulp.src( [
		'assets/src/scss/admin/main.scss'
	] )
		.pipe( sass() )
		.on( 'error', gutil.log )
		.pipe( rename( 'brm-admin-styles.css' ) )
		.pipe( gulp.dest( 'assets/dist/css/admin' ) );
} );

gulp.task( 'minifyAdminCSS', ['compileAdminSass'], () => {
	return gulp.src( [
		'assets/dist/css/admin/brm-admin-styles.css'
	] )
		.pipe( uglifycss( {
			uglyComments: true
		} ) )
		.pipe( rename( 'brm-admin-styles.min.css' ) )
		.pipe( gulp.dest( 'assets/dist/css/admin' ) );
} );

/**
 * Frontend styles.
 */
gulp.task( 'compileFrontendSass', () => {
	return gulp.src( [
		'assets/src/scss/public/*.scss',
	] )
		.pipe( sass() )
		.on( 'error', gutil.log )
		.pipe( rename( { prefix: 'brm-' } ) )
		.pipe( gulp.dest( 'assets/dist/css/public' ) );
} );

gulp.task( 'minifyFrontendCSS', ['compileFrontendSass'], () => {
	return gulp.src( [
		'assets/dist/css/public/*.css',
		'!assets/dist/css/public/*.min.css',
	] )
		.pipe( uglifycss( {
			uglyComments: true
		} ) )
		.pipe( rename( { suffix: '.min' } ) )
		.pipe( gulp.dest( 'assets/dist/css/public' ) );
} );

/**
 * Translation.
 */
gulp.task( 'makePOT', () => {
	return gulp.src(
		'**/*.php'
	)
	.pipe( wpPot(
		{
			domain: 'best-restaurant-menu',
			package: 'Best Restaurant Menu'
		}
	) )
	.pipe( gulp.dest( 'languages/best-restaurant-menu.pot' ) );
} );

/**
 * Main tasks.
 */
gulp.task( 'clean', () => {
	del(
		[
			'assets/dist/css/admin/**/*.css',
			'assets/dist/css/public/**/*.css',
			'assets/dist/js/admin/**/*.js',
			'assets/dist/js/public/**/*.js',
			'languages/*.pot',
		]
	);
} );

gulp.task( 'watch', () => {
	gulp.watch( 'assets/src/scss/admin/**/*.scss', ['minifyAdminCSS'] );
	gulp.watch( 'assets/src/js/admin/**/*.js', ['minifyAdminScripts'] );
	gulp.watch( 'assets/src/scss/public/**/*.scss', ['minifyFrontendCSS'] );
	gulp.watch( 'assets/src/js/public/**/*.js', ['minifyFrontendScripts'] );
} );

gulp.task( 'build', ['minifyAdminScripts', 'minifyAdminCSS', 'minifyFrontendScripts', 'minifyFrontendCSS', 'makePOT'] );

gulp.task( 'default', ['build'] );