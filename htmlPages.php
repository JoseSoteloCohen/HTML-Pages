<?php

if (!class_exists('htmlPages')) {
    class htmlPages
    {
        private $html_pages_options;
        public function __construct()
        {
            //hooks
            add_action('init', array($this, 'html_page_custom_post_type')); //creates the custom post type
            add_action('admin_init', array($this, 'html_pages_admin_init')); //creates the fields to use the settings page
            add_action('admin_menu', array($this, 'htmlPagesAdmin')); // Loads the function that creates de sub-menus
            add_filter('single_template', array($this, 'html_page_template')); // Loads the function that overrides the theme template when loading landing pages
            add_action('wp', array($this, 'contentHook')); // Loads the function that overrides the WordPress content with the landing page HTML
            add_action( 'current_screen', array($this,'get_current_screen'));
        }
        //Remove Visual editor
        function get_current_screen() {
            $current_screen = get_current_screen();
            if ( $current_screen->post_type == 'html_page' ) {
                add_filter( 'user_can_richedit', '__return_false' );
            }
        }
        // Adds the menu pages as sub-menus of the custom post type.
        public function htmlPagesAdmin()
        {
            //SETTINGS PAGE
            add_submenu_page('edit.php?post_type=html_page', 'Settings', 'Settings', 'manage_options', 'html-pages-settings', [$this, 'html_pages_admin_page'] );
            }
        // HTML of the landing page settings admin page
        public function html_pages_admin_page()
        {
            // Set class property
            $this->options = get_option( 'html_pages_options' );
            ?>
            <div class="wrap">
                <h2>HTML Pages Settings</h2>
                <form method="post" action="options.php">
                    <?php
                    // This prints out all hidden setting fields
                    settings_fields( 'html_pages_main_options_group' );
                    do_settings_sections( 'html_pages_settings_admin_page' );
                    submit_button();
                    ?>
                </form>
            </div>
            <?php
        }
        // Creates the API Key setting page fields and texts
        function html_pages_admin_init(){
            register_setting(
                'html_pages_main_options_group', // Option group
                'html_pages_options', // Option name
                array( $this, 'sanitize' ) // Sanitize
            );
            add_settings_section(
                    'html_pages_url_section', // ID
                    'URL Settings', // Title
                    array( $this, 'html_pages_print_url_section_info' ), // Callback
                    'html_pages_settings_admin_page' // Page
            );
            add_settings_section(
                    'html_pages_permalink_tutorial_section', // ID
                    'How to implement the changes:', // Title
                    array( $this, 'html_pages_permalink_tutorial_section_info' ), // Callback
                    'html_pages_settings_admin_page' // Page
            );
            add_settings_field(
                    'html_pages_url',
                    'The URL pattern for your landing pages',
                    array( $this, 'html_pages_url_field_callback' ),
                    'html_pages_settings_admin_page',
                    'html_pages_url_section'
            );
        }
        public function sanitize( $input )
        {
            $new_input = array();

            if (isset($input['html_pages_url']))
                $new_input['html_pages_url'] = sanitize_text_field($input['html_pages_url']);
            return $new_input;
        }
        // SECTION INFORMATION CALLBACKS
        public function html_pages_print_url_section_info()
        {
            print 'Here you can configure the settings for the URLs of your HTML pages';
        }
        public function html_pages_permalink_tutorial_section_info()
        {
            print 'After you save here, you need to go to: 1. Settings -> 2. Permalinks -> 3. Without doing anything else, just click the button Save Changes and that is it';
        }
        //FIELDS CALLBACKS
        public function html_pages_url_field_callback()
        {
            printf(
                '<input type="text" id="html_pages_url" placeholder="Examples: landing or promotions" name="html_pages_options[html_pages_url]" value="%s" />',
                isset( $this->options['html_pages_url'] ) ? esc_attr( $this->options['html_pages_url']) : ''
            );
        }
        // Here we decide if the content is going to be replaced from the one of the landing or not, so we check if the custom post type matches
        public function contentHook(){
            if ('html_page' === get_post_type() AND is_singular()) {
                add_filter('the_content', array( $this, "htmlPageContent" ));
            }
        }
        // Here we get the content of the imported landing page, then render the body of the same
        function htmlPageContent($content)
        {
            global $post;

            if ('html_page' === $post->post_type) {
                $content = $post->post_content;
                return $content;
            }
        }
        // Here we overwrite the template of the theme, if it's the landing custom post type then it will use our template
        public function html_page_template($template) {
            global $post;

            if ( 'html_page' === $post->post_type && file_exists( plugin_dir_path( __FILE__ ) . 'views/single-html-page.php')) {
                return plugin_dir_path( __FILE__ ) . 'views/single-html-page.php';
            }
            return $template;
        }
        // Register the Landing page custom post type
        public function html_page_custom_post_type() {
            $html_page_path = 'l';
            $html_pages_options = get_option('html_pages_options');
            if(isset($html_pages_options['html_pages_url'])){
                $html_page_path = urlencode($html_pages_options['html_pages_url']);
            }

            $args = array (
                'label' => esc_html__( 'HTML Pages', 'html_page' ),
                'labels' => array(
                    'menu_name' => esc_html__( 'HTML Pages', 'html_page' ),
                    'name_admin_bar' => esc_html__( 'HTML Page', 'html_page' ),
                    'add_new' => esc_html__( 'Add new', 'html_page' ),
                    'add_new_item' => esc_html__( 'Add new HTML page', 'html_page' ),
                    'new_item' => esc_html__( 'New HTML page', 'html_page' ),
                    'edit_item' => esc_html__( 'Edit HTML page', 'html_page' ),
                    'view_item' => esc_html__( 'View HTML page', 'html_page' ),
                    'update_item' => esc_html__( 'Update HTML page', 'html_page' ),
                    'all_items' => esc_html__( 'All HTML Pages', 'html_page' ),
                    'search_items' => esc_html__( 'Search HTML pages', 'html_page' ),
                    'parent_item_colon' => esc_html__( 'Parent HTML page', 'html_page' ),
                    'not_found' => esc_html__( 'No HTML pages found', 'html_page' ),
                    'not_found_in_trash' => esc_html__( 'No HTML page found in Trash', 'html_page' ),
                    'name' => esc_html__( 'HTML Pages', 'html_page' ),
                    'singular_name' => esc_html__( 'HTML Page', 'html_page' ),
                ),
                'public' => true,
                'description' => 'HTML pages',
                'exclude_from_search' => false,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_nav_menus' => true,
                'show_in_menu' => true,
                'show_in_admin_bar' => true,
                'show_in_rest' => false,
                'menu_position' => 20,
                'menu_icon' => plugins_url('assets/img/html-pages-icon.png', __FILE__),
                'capability_type' => 'page',
                'hierarchical' => false,
                'has_archive' => false,
                'query_var' => false,
                'can_export' => true,
                'rewrite_no_front' => false,
                'supports' => array(
                    'title',
                    'editor',
                    'thumbnail',
                    'excerpt',
                    'revisions',
                    'author'
                ),
                'rewrite' => array('slug' => $html_page_path,'with_front' => false),
            );
            register_post_type( 'html_page', $args );
        }
    }
}