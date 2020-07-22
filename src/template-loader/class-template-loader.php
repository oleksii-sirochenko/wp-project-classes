<?php

namespace your\space;

/**
 * Custom template loader that loads files from templates folder. There is two main methods, one is rendering on
 * invocation, another is buffering rendered template and returns it as a string. Buffered templates are strings which
 * you can return as a result, provide into another template as an argument to render it on the certain place under
 * certain conditions. Also it should be used in caching logic.
 *
 * This class is very important in development of clean and maintainable codebase. It helps easily separate logic which
 * calculates and provides arguments for certain views.
 */
class Template_Loader {
    
    /**
     * Path to directory relatively to root directory from plugin or theme.
     *
     * @var null
     */
    protected $dir_path;
    
    /**
     * Path to template from dir_path.
     *
     * @var string
     */
    protected $template_path = 'includes/templates';
    
    /**
     * Template_Loader constructor.
     *
     * @param string $dir_path
     * @param string $template_path
     */
    function __construct( $dir_path, $template_path = '' ) {
        $this->dir_path = rtrim( $dir_path, '/\\' );
        
        if ( ! empty( $template_path ) ) {
            $this->template_path = rtrim( $template_path, '/\\' );
        }
    }
    
    /**
     * Includes file that should be HTML template and populates provided arguments in the current scope of variables.
     * In case when template is not found and WP_DEBUG is true renders short debug information.
     *
     * @param string $file_name
     * @param string $path_from_template
     * @param array  $args
     *
     * @return string
     */
    function get_template( $file_name, $path_from_template, array $args = array() ) {
        $path_from_template = rtrim( $this->dir_path . '/' . $this->template_path . '/' . $path_from_template, '/\\' );
        $path               = $path_from_template . '/' . $file_name;
        
        if ( file_exists( $path ) ) {
            extract( $args );
            include $path;
        } else {
            if ( WP_DEBUG ) {
                ?>
                <div style="font-size:20px; color: red; background-color:white; padding: 0 5px;">Template was not found:
                    <strong><?php echo $path; ?></strong>
                </div>
                <?php
            }
        }
    }
    
    /**
     * Returns string which is a buffer of rendered HTML template with populated arguments. Important when you need to
     * return template as string instead of rendering it right away.
     *
     * @param string $file_name
     * @param string $path_from_template
     * @param array  $args
     *
     * @return string
     */
    function get_template_as_string( $file_name, $path_from_template, array $args = array() ) {
        ob_start();
        $this->get_template( $file_name, $path_from_template, $args );
        
        return ob_get_clean();
    }
}