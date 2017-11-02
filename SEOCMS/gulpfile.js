var gulp = require('gulp'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    rename = require('gulp-rename'),
    sass = require('gulp-ruby-sass');

var DEST = 'public/';

gulp.task('scripts', function() {
    return gulp.src([
        'resources/assets/js/helpers/*.js',
        'resources/assets/js/*.js',
      ])
      .pipe(concat('custom.js'))
      .pipe(gulp.dest(DEST+'/js'))
      .pipe(rename({suffix: '.min'}))
      .pipe(uglify())
      .pipe(gulp.dest(DEST+'/js'));
});

// TODO: Maybe we can simplify how sass compile the minify and unminify version
var compileSASS = function (filename, options) {
  return sass('resources/assets/scss/*.scss', options)
        .pipe(concat(filename))
        .pipe(gulp.dest(DEST+'/css'));
};

gulp.task('sass', function() {
    return compileSASS('custom.css', {});
});

gulp.task('sass-minify', function() {
    var options = {style: 'compressed'};
    return compileSASS('custom.min.css', options);
});


gulp.task('watch', function() {
  // Watch .js files
  gulp.watch('resources/assets/js/*.js', ['scripts']);
  // Watch .scss files
  gulp.watch('resources/assets/scss/*.scss', ['sass', 'sass-minify']);
});

// Default Task
gulp.task('default', ['scripts', 'sass', 'sass-minify']);
