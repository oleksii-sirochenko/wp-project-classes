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
	 * @param string $file_name
	 * @param string $path_from_template
	 * @param array $args
	 *
	 * @return string|bool
	 */
	function get_template( $file_name, $path_from_template, array $args = array() ) {
		$path_from_template = rtrim( $this->dir_path . '/' . $this->template_path . '/' . $path_from_template, '/\\' );
		$path               = $path_from_template . '/' . $file_name;

		if ( file_exists( $path ) ) {
			extract( $args );
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

	/**
	 * @param string $file_name
	 * @param string $path_from_template
	 * @param array $args
	 *
	 * @return string|bool
	 */
	function get_template_as_string( $file_name, $path_from_template, array $args = array() ) {
		ob_start();
		$this->get_template( $file_name, $path_from_template, $args );

		return ob_get_clean();
	}
}