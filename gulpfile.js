var gulp = require('gulp');
var shell = require('gulp-shell');
var prompt = require('gulp-prompt');

var rsync = function(env) {
    if (env == 'sync') {
        dest = '';
    } else {
        dest = 'n';
    }

    var cmd = "rsync --exclude-from='rsync_exclude' -e 'ssh -p 333' --delete-after -razv"+dest+" . alezalec@alezale.com:/home/alezalec/web/feed/";
    gulp.start(
        shell.task(cmd)
    );
}

gulp.task('rsync', function () {
    return gulp.src('')
    .pipe(prompt.prompt(
        [{
            type: 'input',
            message: 'sync or test?',
            name: 'confirm',
            default: 'test'
        }],
        function(response){
            return rsync(response.confirm);
        }
    ));
});

gulp.task('default', [ 'rsync' ]);