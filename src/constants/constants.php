<?php

namespace your\space;

/**
 * FILE WITH NAMESPACED CONSTANTS DECLARATIONS.
 */

/**
 * Constant to identify current version of the project.
 */
define( __NAMESPACE__ . '\VERSION', '1.0.0' );

/**
 * THESE CONSTANTS SHOULD BE CHOSEN ACCORDINGLY TO YOUR PROJECT TYPE:
 * PATH, URL.
 *
 * WHEN YOU ARE READY YOU SHOULD REMOVE OTHER DEFINITIONS TO KEEP FILE CLEAR.
 */

/**
 *          Definition for plugin:
 */
/**
 * Constant to the PATH of plugin folder.
 *
 * @var string PATH
 */
//define( __NAMESPACE__ . '\PATH', Plugin_Starter::PATH );
/**
 * Constant to the URL of plugin folder.
 *
 * @var string URL
 */
//define( __NAMESPACE__ . '\URL', Plugin_Starter::plugin_url() );
/**
 *          # Definition for plugin.
 */


/**
 *          Definition for theme:
 */
/**
 * Constant to the PATH of theme folder.
 *
 * @var string PATH
 */
//define( __NAMESPACE__ . '\PATH', get_template_directory() );
/**
 * Constant to the URL of theme folder.
 *
 * @var string URL
 */
//define( __NAMESPACE__ . '\URL', get_template_directory_uri() );
/**
 *          #Definition for theme.
 */


/**
 *          Definition for child theme:
 */
/**
 * Constant to the PATH of child theme folder.
 *
 * @var string PATH
 */
//define( __NAMESPACE__ . '\PATH', get_stylesheet_directory() );
/**
 * Constant to the URL of child theme folder.
 *
 * @var string URL
 */
//define( __NAMESPACE__ . '\URL', get_stylesheet_directory_uri() );
/**
 *          #Definition for child theme.
 */


/**
 * Constant to use when enqueueing frontend assets.
 *
 * @var string ASSETS_URL
 */
define( __NAMESPACE__ . '\ASSETS_URL', URL . '/assets' );

/**
 * Constant to use in scripts and styles url. When SCRIPT_DEBUG is true your assets will be enqueued as normal files,
 * but when you are not debugging your assets will be enqueued as minified versions.
 */
define( __NAMESPACE__ . '\MIN', SCRIPT_DEBUG ? '' : '.min' );

/**
 * Constant identifies that current site works in localhost environment and not in production. To use this constant
 * set is_localhost variable in apache config. This constant for those situations when you can't rely on WP_DEBUG
 * because of the test server and you should have identification of your development environment.
 */
define( __NAMESPACE__ . '\IS_LOCALHOST', getenv( 'is_localhost' ) === 'true' );