"use strict";

const gulp = require('gulp'),
    autoprefixer = require('gulp-autoprefixer'),
    less = require('gulp-less'),
    sass = require('gulp-sass'),
    concat = require('gulp-concat'),
    rigger = require('gulp-rigger'),
    cleanCSS = require('gulp-clean-css'),
    gcmq = require('gulp-group-css-media-queries'),
    sourcemaps = require('gulp-sourcemaps'),
    browserSync = require('browser-sync').create(),
    mainBowerFile = require('main-bower-files'),
    flatten = require('gulp-flatten'),
    imagemin = require('gulp-imagemin'),
    svgmin = require('gulp-svgmin'),
    svgSimbols = require('gulp-svg-symbols'),
    babel = require("gulp-babel"),
    uglify = require('gulp-uglify'),
    rename = require('gulp-rename');


//PATH
let config = {
    build: {
        css: './public/css',
        js: './public/js',
        libs: './public/libs',
        svg: './public/svg',
        svgSymbols: './public/svg/icons',
        img: './public/uploads/images/image',
        fonts: './public/fonts/'
    },
    src: {
        css: './resources/css/*.css',
        less: './resources/less/style.less',
        scss: './resources/sass/style.scss',
        js: './resources/js/js.js',
        libs: './resources/libs',
        svg: './resources/svg/*.svg',
        svgSymbols: './resources/svg/icons/*.svg',
        img: './resources/images/**/*.{jpg,png,gif}',
        fonts: './resources/fonts/**/*.*'
    },
    watch: {
        less: './resources/less/**/*.less',
        scss: './resources/sass/**/*.scss',
        js: './resources/js/**/*.js',
        img: './resources/img/**/*.*',
        fonts: './resources/fonts/**/*.*'
    },
};

// SERVER
gulp.task('server', reload => {
    browserSync.init({
        browser: "chrome",
        proxy: "http://127.0.0.1:9900/",
        notify: false,
        open: false
    });
    reload();
});

//LESS
gulp.task('less', () => {
    return gulp.src(config.src.less)
        .pipe(sourcemaps.init())
        .pipe(less())
        .pipe(gcmq())
        .pipe(autoprefixer({
            Browserslist: ['last 10 version'],
            cascade: false
        }))
        .pipe(sourcemaps.write())
        .pipe(concat("style.css"))
        .pipe(gulp.dest(config.build.css))
        .pipe(cleanCSS({
            level: 2
        }))
        .pipe(sourcemaps.write())
        .pipe(rename({suffix: '.min', prefix: ''}))
        .pipe(gulp.dest(config.build.css))
        .pipe(browserSync.stream());
});

//SASS
gulp.task('scss', () => {
    return gulp.src(config.src.scss)
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(gcmq())
        .pipe(autoprefixer({
            browsers: ['last 10 versions'],
            cascade: false
        }))
        .pipe(sourcemaps.write())
        .pipe(concat("style.css"))
        .pipe(gulp.dest(config.build.css))
        .pipe(cleanCSS({
            level: 2
        }))
        .pipe(sourcemaps.write())
        .pipe(rename({suffix: '.min', prefix: ''}))
        .pipe(gulp.dest(config.build.css))
        .pipe(browserSync.stream());
});

//LIBS
gulp.task('libs', () => {
    return gulp.src(mainBowerFile(
        {
            "overrides": {
                // "jquery": {
                //     "main": "dist/jquery.min.js"
                // },
                "svg4everybody": {
                    "main": "dist/svg4everybody.min.js"
                },
                "owl.carousel": {
                    "main": "dist/owl.carousel.min.js"
                }
            }
        }
    ), {base: config.src.libs})
        .pipe(flatten({includeParents: 1}))
        .pipe(gulp.dest(config.build.libs))
});

//JS
gulp.task('js', () => {
    return gulp.src(config.src.js)
        .pipe(sourcemaps.init())
        .pipe(babel({ "presets": ["@babel/preset-env"] }))
        .pipe(concat('js.js'))
        .pipe(gulp.dest(config.build.js))
        .pipe(uglify())
        .pipe(sourcemaps.write('.'))
        .pipe(rename({suffix: '.min', prefix: ''}))
        .pipe(gulp.dest(config.build.js))
        .pipe(browserSync.stream())
});

//IMG
gulp.task('img', () => {
    return gulp.src(config.src.img, {base: config.src})
        .pipe(imagemin({
            progressive: true,
            optimizationLevel: 5,
            interlaced: true
        }))
        .pipe(gulp.dest(config.build.img))
});

//SVG
gulp.task('svg', () => {
    return gulp.src(config.src.svg)
        .pipe(svgmin({
            plugins: [
                {removeEditorsNSData: true},
                {removeTitle: true}
            ]
        }))
        .pipe(gulp.dest(config.build.svg))
});

//SVG SYMBOLS
gulp.task('svgSymbols', () => {
    return gulp.src(config.src.svgSymbols)
        .pipe(svgmin({
            plugins: [
                {removeEditorsNSData: true},
                {removeTitle: true}
            ]
        }))
        .pipe(svgSimbols({
            title: '%f icon',
            svgAttrs: {
                class: 'svg-icon-lib'
            },
            templates: [
                'default-svg', 'default-css', 'default-demo'
            ]
        }))
        .pipe(gulp.dest(config.build.svgSymbols))
});

//FONTS
gulp.task('fonts', () => {
    return gulp.src(config.src.fonts)
        .pipe(rigger())
        .pipe(gulp.dest(config.build.fonts))
});

//WATCH
gulp.task('watchLess', () => {
    gulp.watch(config.watch.less, gulp.series('less'));
    gulp.watch(config.watch.js, gulp.series('js'));
});

//WATCH SASS
gulp.task('watchSass', () => {
    gulp.watch(config.watch.scss, gulp.series('scss'));
    gulp.watch(config.watch.js, gulp.series('js'));
});

//WATCH
gulp.task('watch', () => {
    gulp.watch(config.watch.less, gulp.series('less'));
    gulp.watch(config.watch.js, gulp.series('js'));
});

//WATCH BLADE
gulp.task('watchBlade', () =>{
    gulp.watch('./resources/views/**/*.blade.php').on('change', browserSync.reload);
});

//LARAVEL WATCH
gulp.task('laraWatchLess', gulp.parallel('watch', 'watchBlade', 'server'));

//WATCH BUILD
gulp.task('buildLess', gulp.parallel('watchLess', 'server'));
gulp.task('buildSass', gulp.parallel('watchSass', 'server'));
