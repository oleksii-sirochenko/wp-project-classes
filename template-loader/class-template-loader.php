<?php


namespace your\space;

/**
 * custom template loader that loads files from templates folder
 */
class Template_Loader {
	/**
	 * path to directory relatively to root directory from plugin or theme
	 * @var null
	 */
	protected $dir_path;
	/**
	 * path to template from dir_path
	 * @var string
	 */
	protected $template_path = 'includes/templates/';

	function __construct( $dir_path = null, $template_path = null ) {
		if ( ! is_null( $dir_path ) ) {
			$this->dir_path = trailingslashit( $dir_path );
		}
		if ( ! is_null( $template_path ) ) {
			$this->template_path = trailingslashit( $template_path );
		}
	}

	/**
	 * @param string $file_name
	 * @param string $path_from_template - without trailing slash
	 * @param array $args
	 *
	 * @return string|bool
	 */
	function get_template( $file_name = '', $path_from_template = '', $args = null ) {
		if ( empty( $file_name ) ) {
			return false;
		}

		$path_from_template = trailingslashit( $this->dir_path . $this->template_path . $path_from_template );
		$path               = $path_from_template . $file_name;

		if ( is_array( $args ) ) {
			extract( $args );
		}

		if ( file_exists( $path ) ) {
			include $path;
		} else {
			if ( Reg::inst()->is_localhost() ) {
				?>
        <div style="font-size:20px; color: red; background-color:white; padding: 0 5px;">Template was not found:
          <strong><?php echo $path; ?></strong>
        </div>
				<?php
			}
		}
	}

	function get_template_as_string( $file_name = null, $path_from_template = null, $args = null ) {
		ob_start();
		$this->get_template( $file_name, $path_from_template, $args );

		return ob_get_clean();
	}
}