<?php

namespace your\space;

/**
 * Creates required pages for theme or plugin and adds text labels for each created page in list of pages in admin side.
 *
 * @note Create_pages_on_activation method has to be invoked during theme or plugin activation to run creating pages
 *       logic once.
 * @note Edit set_pages_map method to register custom pages with shortcodes in content.
 */
class Required_Pages {
    
    /**
     * Option key that collects data about pages that were created by this instance.
     *
     * @var string
     */
    protected $option = 'your_space_required_pages';
    
    /**
     * Registered array of pages.
     *
     * @var array
     */
    protected $pages = array();
    
    public function __construct() {
        $this->set_pages_map();
    }
    
    /**
     * Attaches methods to hooks.
     */
    public function hooks() {
        add_filter( 'display_post_states', array( $this, 'add_post_state_to_plugin_pages' ), 10, 2 );
    }
    
    /**
     * Sets predefined pages that should be created during theme or plugin activation hook.
     */
    protected function set_pages_map() {
        $this->pages = array(
            array(
                'title'     => 'Page title',
                'slug'      => 'shortcode-page-slug',
                'shortcode' => '[shortcode]',
            ),
        );
    }
    
    /**
     * Checks and creates predefined pages during theme or plugin activation.
     */
    public function create_pages_on_activation() {
        $pages = get_option( $this->option, array() );
        
        if ( empty( $pages ) ) {
            foreach ( $this->pages as $page ) {
                $page_data                   = $this->create_page( $page );
                $pages[ $page_data['slug'] ] = $page_data['id'];
            }
        } else {
            foreach ( $this->pages as $page ) {
                if ( ! isset( $pages[ $page['slug'] ] ) || ! $this->is_page_exists( $pages[ $page['slug'] ] ) ) {
                    $page_data                   = $this->create_page( $page );
                    $pages[ $page_data['slug'] ] = $page_data['id'];
                } elseif ( isset( $page['shortcode'] ) && ! empty( $page['shortcode'] ) ) {
                    $post              = get_post( $pages[ $page['slug'] ] );
                    $shortcode_pattern = preg_quote( $page['shortcode'], '/' );
                    if ( ! preg_match( '/' . $shortcode_pattern . '/', $post->post_content ) ) {
                        $post_args = array(
                            'ID'           => $post->ID,
                            'post_content' => $page['shortcode'] . ' ' . $post->post_content,
                        );
                        wp_update_post( $post_args );
                    }
                }
            }
        }
        
        update_option( $this->option, $pages );
    }
    
    /**
     * Checks if page exists.
     *
     * @param $post_id
     *
     * @return bool
     */
    protected function is_page_exists( $post_id ) {
        return ! empty( get_post( $post_id ) );
    }
    
    /**
     * Creates page with possible shortcode as a content.
     *
     * @param $page
     *
     * @return array
     */
    protected function create_page( $page ) {
        $args = array(
            'post_type'   => 'page',
            'post_status' => 'publish',
            'post_title'  => $page['title'],
            'post_name'   => $page['slug'],
        );
        
        if ( isset( $page['shortcode'] ) && ! empty( $page['shortcode'] ) ) {
            $args['post_content'] = $page['shortcode'];
        }
        
        $post_id = wp_insert_post( $args );
        
        return array(
            'slug' => $page['slug'],
            'id'   => $post_id,
        );
    }
    
    /**
     * Searches for page id in created pages.
     *
     * @param $slug
     *
     * @return mixed
     */
    public function get_page_id_by_slug( $slug ) {
        $pages = get_option( $this->option, array() );
        if ( isset( $pages[ $slug ] ) ) {
            return $pages[ $slug ];
        }
    }
    
    /**
     * Returns slug for created page by page id.
     *
     * @param $page_id
     *
     * @return int|string
     */
    public function get_slug_by_page_id( $page_id ) {
        $pages = get_option( $this->option, array() );
        foreach ( $pages as $slug => $id ) {
            if ( $page_id == $id ) {
                return $slug;
            }
        }
    }
    
    /**
     * Checks if provided post id is from registered pages.
     *
     * @param $post_id
     *
     * @return bool
     */
    public function is_registered_page( $post_id ) {
        return in_array( $post_id, array_values( get_option( $this->option, array() ) ) );
    }
    
    /**
     * Returns page title by id.
     *
     * @param $post_id
     *
     * @return mixed
     */
    public function get_page_title_by_id( $post_id ) {
        $pages = get_option( $this->option, array() );
        foreach ( $pages as $slug => $id ) {
            if ( $id == $post_id ) {
                foreach ( $this->pages as $page ) {
                    if ( $page['slug'] == $slug ) {
                        return $page['title'];
                    }
                }
            }
        }
    }
    
    /**
     * Returns page id searched by shortcode. Provide full shortcode signature as it was registered.
     *
     * @param string $shortcode
     *
     * @return int|mixed
     */
    public function get_page_id_by_shortcode( $shortcode ) {
        foreach ( $this->pages as $page ) {
            if ( $page['shortcode'] === $shortcode ) {
                return $this->get_page_id_by_slug( $page['slug'] );
            }
        }
        
        return 0;
    }
    
    /**
     * Adds custom label to mark pages that were created by this class.
     *
     * @param array    $post_states
     * @param \WP_Post $post
     *
     * @return array
     */
    public function add_post_state_to_plugin_pages( array $post_states, \WP_Post $post ) {
        if ( $this->is_registered_page( $post->ID ) ) {
            $post_states[] = __( 'Your prefix', 'domain' ) . ': ' . $this->get_page_title_by_id( $post->ID );
        }
        
        return $post_states;
    }
}