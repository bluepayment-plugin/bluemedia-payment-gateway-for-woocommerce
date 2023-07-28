"use strict";

const autoprefixer = require('gulp-autoprefixer');
const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
sass.compiler = require('node-sass');
const uglify = require('gulp-uglify');

gulp.task('sass', function () {
	return gulp.src('app/scss/*.scss')
  .pipe(sass().on('error', sass.logError))
  .pipe(autoprefixer())
  .pipe(gulp.dest('assets/css'))
});

gulp.task('scripts', function() {
	return gulp.src('app/js/*.js')
  .pipe(uglify())
  .pipe(gulp.dest('assets/js'));
});

gulp.task(
  "watch",
  gulp.series('sass', 'scripts', function() {
    gulp.watch('app/scss/**/*.scss', gulp.series('sass'));
    gulp.watch("app/js/*.js", gulp.series("scripts"));
  })
);

gulp.task('default', gulp.series('sass', 'scripts'));