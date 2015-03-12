module.exports = function (grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        bowercopy: {
            options: {
                srcPrefix: 'bower_components'
            },
            scripts: {
                files: {
                    'web/assets/js/jquery.js': 'jquery/dist/jquery.js',
                    'web/assets/js/bootstrap.js': 'bootstrap/dist/js/bootstrap.js',
                    'web/assets/js/jquery.minicolors.min.js': 'jquery-minicolors/jquery.minicolors.min.js'
                }
            },
            stylesheets: {
                files: {
                    'web/assets/css/bootstrap.css': 'bootstrap/dist/css/bootstrap.css',
                    'web/assets/css/font-awesome.css': 'font-awesome/css/font-awesome.css',
                    'web/assets/css/jquery.minicolors.css': 'jquery-minicolors/jquery.minicolors.css',
                }
            },
            fonts: {
                files: {
                    'web/assets/fonts': 'font-awesome/fonts'
                }
            },
            pulpy: {
                files: {
                    'web/apps/pulpy': 'pulpy'
                }
            }
        },
        copy: {
            images: {
                expand: true,
                cwd: 'src/Baikal/ViewComponentsBundle/Resources/public/img',
                src: '*',
                dest: 'web/assets/images/'
            }
        },
        concat: {
            options: {
                stripBanners: true
            },
            css: {
                src: [
                    'web/assets/css/bootstrap.css',
                    'web/assets/css/font-awesome.css',
                    'src/Baikal/FrontendBundle/Resources/public/css/*.css'
                ],
                dest: 'web/assets/dist/bundled.css'
            },
            js : {
                src : [
                    'web/assets/js/jquery.js',
                    'web/assets/js/jquery.minicolors.min.js',
                    'web/assets/js/bootstrap.js'
                ],
                dest: 'web/assets/dist/bundled.js'
            }
        },
        cssmin : {
            bundled:{
                src: 'web/assets/dist/bundled.css',
                dest: 'web/assets/dist/bundled.min.css'
            }
        },
        uglify : {
            js: {
                files: {
                    'web/assets/dist/bundled.min.js': ['web/assets/dist/bundled.js'],
                    'web/assets/dist/pulpy.min.js': ['web/apps/pulpy/dist/assets/main.js']
                }
            }
        },
        // gzip assets 1-to-1 for production
        compress: {
            js: {
                options: { mode: 'gzip' },
                expand: true,
                cwd: 'web/assets/dist/',
                src: ['bundled.min.js'],
                dest: 'web/assets/dist/',
                ext: '.min.js.gz'
            },
            pulpy: {
                options: { mode: 'gzip' },
                expand: true,
                cwd: 'web/assets/dist/',
                src: ['pulpy.min.js'],
                dest: 'web/assets/dist/',
                ext: '.min.js.gz'
            },
            css: {
                options: { mode: 'gzip' },
                expand: true,
                cwd: 'web/assets/dist/',
                src: ['bundled.min.css'],
                dest: 'web/assets/dist/',
                ext: '.min.css.gz'
            }
        }
    });

    grunt.loadNpmTasks('grunt-bowercopy');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-compress');

    grunt.registerTask('default', ['bowercopy', 'copy', 'concat', 'cssmin', 'uglify', 'compress']);
};