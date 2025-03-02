<?php


namespace DCM;


class App
{
    public function __construct()
    {
        register_activation_hook(DCM_PLUGIN_PATH . 'door-codes-manager.php', [$this, 'plugin_activation']);
        add_shortcode('door_code', [$this, 'door_code']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('admin_menu', [$this, 'dcm_admin_menu']);
        add_action('save_post_page', [$this, 'save_door_codes']);
        Ajax::register();
        Beds24::register();
        Shortcode::register();
    }

    static function run()
    {
        new self();
    }

    function plugin_activation()
    {
        $this->create_dcm_database_table();
        $this->create_bookings_database_table();
    }

    function create_dcm_database_table()
    {
        global $wpdb;
        $tablename = $wpdb->prefix . "doorcodes";
        $charset_collate = $wpdb->get_charset_collate();

        if ($wpdb->get_var("show tables like '$tablename'") != $tablename) {
            $sql = "CREATE TABLE $tablename (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          page_id int(11) NOT NULL ,
          shortcode varchar(999) NOT NULL,
          code varchar(22) NOT NULL,
          PRIMARY KEY  (id)
        ) $charset_collate;";
            require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }

    function create_bookings_database_table()
    {
        global $wpdb;
        $tablename = $wpdb->prefix . "beds24_bookings";
        $charset_collate = $wpdb->get_charset_collate();

        if ($wpdb->get_var("show tables like '$tablename'") != $tablename) {
            $sql = "CREATE TABLE $tablename (
                booking_id INT PRIMARY KEY,
                firstname VARCHAR(255) NOT NULL,
                lastname VARCHAR(255) NOT NULL,
                phone VARCHAR(15),
                checkin DATE NOT NULL,
                checkout DATE NOT NULL,
                reservation_number VARCHAR(255) NULL UNIQUE,
                checkin_link TEXT,
                full_name VARCHAR(255) NOT NULL,
                blocked BOOLEAN DEFAULT FALSE,
            ) $charset_collate;";
            require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }

    function door_code($atts)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'doorcodes';
        $id = get_the_ID();
        $default_atts = ['name' => ''];
        $attributes = shortcode_atts($default_atts, $atts);
        $code = $wpdb->get_var($wpdb->prepare("SELECT code from " . $table_name . " WHERE page_id = %d AND shortcode = %s", [$id, $attributes['name']]));
        return '<strong>' . $code . '</strong>';
    }

    function dcm_admin_menu()
    {
        add_menu_page('Door Codes Manager', 'Door Codes Manager', 'manage_options', 'door_codes_manager', ['DCM\Utils', 'dcm_admin_page'], 'dashicons-admin-multisite', 6);
        add_submenu_page('door_codes_manager', 'Beds24 Properties', 'Beds24 Properties', 'manage_options', 'door_codes_manager_beds24', ['DCM\Utils', 'dcm_beds24_page']);
        add_submenu_page('door_codes_manager', 'Beds24 Bookings', 'Beds24 Bookings', 'manage_options', 'door_codes_manager_beds24_bookings', ['DCM\Utils', 'dcm_beds24_bookings_page']);
        add_submenu_page('door_codes_manager', 'Blocked Guests', 'Blocked Guests', 'manage_options', 'door_codes_manager_blocked_guests', ['DCM\Utils', 'dcm_blocked_guests_page']);
//        add_options_page('DCM Settings', 'DCM Settings', 'manage_options', 'dcm-settings' );
    }

    function enqueue_admin_scripts()
    {
        wp_enqueue_style('DCM_admin_styles', DCM_PLUGIN_URL . 'assets/css/main.css?t=' . time());
        wp_enqueue_script('DCM_admin_script', DCM_PLUGIN_URL . 'assets/js/main.js?t=' . time(), ['jquery']);
        wp_localize_script('DCM_admin_script', 'dcm',
            [
                'ajaxurl' => admin_url('admin-ajax.php'),
            ]
        );
    }

    function enqueue_scripts()
    {
        wp_enqueue_style('DCM_styles', DCM_PLUGIN_URL . 'assets/css/style.css?t=' . time());
        wp_enqueue_script('DCM_script', DCM_PLUGIN_URL . 'assets/js/front.js?t=' . time(), ['jquery']);
        wp_localize_script('DCM_script', 'dcm',
            [
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('dcm_search_nonce')
            ]
        );
    }


    function save_door_codes($page_id)
    {

        $post = get_post($page_id);
        $doors = Utils::get_doors($post->post_content);
        if (empty($doors)) return;
        $need_to_create = Utils::check_door_differences($page_id, $doors);
        foreach ($need_to_create as $door):
            Utils::insert_dcm_shortcodes($page_id, $door, "");
        endforeach;

    }

}