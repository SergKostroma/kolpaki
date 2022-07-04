var gulp = require('gulp'),
    watch = require('gulp-watch'),
    postcss = require('gulp-postcss'),
    autoprefixer = require('autoprefixer'),
    sass = require('gulp-sass'),
    sourcemaps = require('gulp-sourcemaps'),
    cssnano = require('gulp-cssnano'),
    /*        imagemin = require('gulp-imagemin'),*/
    pngquant = require('imagemin-pngquant'),
    uglify = require('gulp-uglify'),
    rename = require('gulp-rename');
var path = {
  build: {//Тут мы укажем куда складывать готовые после сборки файлы
    // js: '_js/',
    css: 'public/css/',
    /*    img: '_images/',*/
  },
  src: {//Пути откуда брать исходники
    // js: ['_js/**/*.js', '!_js/**/*.min.js', '!_js/**/*-min.js', "!_js/js/**/*"], //В стилях и скриптах нам понадобятся только main файлы
    style: ["resources/scss/*.scss", "resources/scss/**/*.scss"],
    /*    img: '_images/src/!**!/!*.*' //Синтаксис img/!**!/!*.* означает - взять все файлы всех расширений из папки и из вложенных каталогов*/
  },
  watch: {//Тут мы укажем, за изменением каких файлов мы хотим наблюдать
    // js: ['_js/**/*.js', '!_js/**/*.min.js', '!_js/**/*-min.js', "!_js/js/**/*"],
    style: ["resources/scss/*.scss", "resources/scss/**/*.scss"],
  },
  clean: './build'
};
// gulp.task('js:build', function () {
//   gulp.src(path.src.js)
//       .pipe(sourcemaps.init()) //Инициализируем sourcemap
//       .pipe(uglify()).on("error", function () {}) //Сожмем наш js
//       .pipe(rename(function (path) {
//         if (path.extname === '.js') {
//           path.basename += '.min';
//         }
//       }))
//       //          .pipe(sourcemaps.write()) //Пропишем карты
//       .pipe(gulp.dest(path.build.js)) //Выплюнем готовый файл в build
// });

gulp.task('style:build', function () {
  return gulp.src(path.src.style)
      .pipe(sass()).on("error", sass.logError)
      .pipe(postcss([autoprefixer({browsers: ['>0%']})]))
      .pipe(cssnano({autoprefixer: false, convertValues: false, zindex: false, reduceIdents: false}))
      .pipe(rename(function (path) {
        if (path.extname === '.css') {
          path.basename += '.min';
        }
      }))
      .pipe(gulp.dest(path.build.css));
});
gulp.task('build', [
  // 'js:build',
  'style:build',
//  'image:build',
]);

gulp.task('watch', function () {
  watch(path.watch.style, function (event, cb) {
    gulp.start('style:build');
  });
//   watch(path.watch.js, function (event, cb) {/*тут уже в параметре массив*/
//     gulp.start('js:build');
//   });
//  watch([path.watch.img], function (event, cb) {
//    gulp.start('image:build');
//  });

});

gulp.task('default', ['build', 'watch']); 