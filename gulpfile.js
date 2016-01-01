var gulp = require('gulp');
var zip = require('gulp-zip');

gulp.task('zip', function () {
    return gulp.src(['newrelic_apm/**/*'], {base: "."})
        .pipe(zip('newrelic_apm.zip'))
        .pipe(gulp.dest('./build'));
});

gulp.task('default', ['zip']);