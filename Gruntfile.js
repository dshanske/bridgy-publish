module.exports = function(grunt) {
  // Project configuration.
  grunt.initConfig({
    wp_deploy: {
        deploy: { 
            options: {
                plugin_slug: 'bridgy-publish',
                svn_user: 'dshanske',  
                build_dir: 'build/trunk' //relative path to your build directory
                
            },
        }
    },
 copy: {
           main: {
               options: {
                   mode: true
               },
               src: [
                   '**',
                   '!node_modules/**',
                   '!build/**',
                   '!.git/**',
                   '!Gruntfile.js',
                   '!package.json',
                   '!.gitignore'
               ],
               dest: 'build/trunk/'
           }
       },

    wp_readme_to_markdown: {
      target: {
        files: {
          'readme.md': 'readme.txt'
        }
      }
     },
   makepot: {
        target: {
            options: {
		mainFile: 'bridgy-publish.php', // Main project file.
                domainPath: '/languages',                   // Where to save the POT file.
                potFilename: 'semantics.pot',
                type: 'wp-plugin',                // Type of project (wp-plugin or wp-theme).
            exclude: [
                'build/.*'
            ], 
               updateTimestamp: true             // Whether the POT-Creation-Date should be updated without other changes.
            	}
            }
      },
  });

  grunt.loadNpmTasks('grunt-wp-readme-to-markdown');
  grunt.loadNpmTasks( 'grunt-wp-i18n' );
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-wp-deploy');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks( 'grunt-contrib-clean' );
  grunt.loadNpmTasks( 'grunt-git' );
  // Default task(s).
  grunt.registerTask('default', ['wp_readme_to_markdown', 'makepot']);

};
