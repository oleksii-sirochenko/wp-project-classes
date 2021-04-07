# wp-project-classes
A bunch of useful classes for Wordpress related development. These classes aren't related to the last versions of Wordpress and therefore it may be used with any latest version.

Required PHP version >= 7.2

Version 1.10.0

### 2021 todo list:
-[x] Framework name.
-[x] Domain.
-[x] Change log.
-[ ] Settings pages management improvement.
-[ ] Compose files for quick start.
-[ ] Cover all code with meaningful comments.
-[ ] Add return hints where possible.
-[ ] Create site on WP.
-[ ] Start documentation and examples.
-[ ] Bump PHP version >= 7.4

With a help of these classes you can lie solid foundation for your WP related project. Classes are suitable for both theme and plugin development.

## Core classes for ground up development are:
**class-autoloader.php**: Autoloader + Class mapper (https://github.com/alex-sirochenko/php-classes-mapper) - loads php classes
<br>**class-reg.php**: Register (the main pole) - does class initialization, grouping and provides global namespaced access as final singleton.
<br>**class-scripts-loader.php**: Scripts loader - place where all your assets (scripts, styles) are being attached for front and admin side
<br>**class-template-loader.php**: Template loader - loads PHP files with HTML temlates and PHP logic insertion
<br>**class-ajax.php**: AJAX handler and its related classes - provide helpful logic to register ajax handlers and attach config data for front and admin side javascript files.

## Helper classes
**class-plugin-starter.php**: Plugin starter boilerplate
<br>**class-header.php**: Empty starter class for common region
<br>**class-footer.php**: Empty starter class for common region
<br>**class-nav-menus.php**: Handles nav menues registration
<br>**class-sidebars.php**: Handles sidebars registration
<br>**class-theme-setup**: Theme setup related logic goes here
<br>**class-rewrite-rules.php**: Simple code for registering rewrite rules
<br>**metaboxes**: Group of classes that help in developing metaboxes
<br>**settings pages**: Group of classes that help in developing single or tabbed settings pages
<br>**class-transient-cache.php**: Caches PHP templates with the help of WP Transient API
<br>**class-hooked-objects-storage.php**: Storage for your objects that have been attached to apply_filters, do_action. It will help to find object link to use them in remove_filter, remove_action functions. It is more required for projects like custom theme or plugin rather than customer's site project.
<br>**class-required-pages.php**: Creates required pages on plugin or theme activation with shortcode as a content and adds custom prefix text label for title in pages list view in admin side.
<br>**shortcodes**: Group of classes to register and manage logic scoped into shortcode.

## Other places
**constants.php**: Provides project namespaced constants. 


Copyright (c) Alex Sirochenko https://github.com/alex-sirochenko/
