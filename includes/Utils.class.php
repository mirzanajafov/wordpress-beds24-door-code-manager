<?php

namespace DCM;


class Utils
{
    static function renderTemplate($name, $data = [], $once = false)
    {
        $file = DCM_PLUGIN_PATH . 'templates' . DIRECTORY_SEPARATOR . $name . '.php';
        if ($once) {
            require_once($file);
        } else {
            require($file);
        }
    }

    static function getTemplate($name, $data = [], $once = false)
    {
        ob_start();
        self::renderTemplate($name, $data, $once);
        return ob_get_clean();
    }

    static function getAsset($name)
    {
        return DCM_PLUGIN_URL . 'assets/' . $name;
    }

    static function get_doors($content)
    {
        $result = [];
        $pattern = get_shortcode_regex(['door_code']);
        if (preg_match_all('/' . $pattern . '/s', $content, $matches)) {
            if (isset($matches[0])) {
                foreach ($matches[0] as $shortcode) {
                    $value = str_replace(['[door_code name="', '"]'], '', $shortcode);
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    static function dcm_admin_page()
    {
        echo Utils::getTemplate('dcm_admin-page');
    }

    static function dcm_beds24_page()
    {
        echo Utils::getTemplate('dcm_beds24-page');
    }

    static function dcm_beds24_bookings_page()
    {
        $limit = 30;
        $current_page = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $bookings = Beds24::get_recent_bookings($current_page, $limit);
        $total_bookings = Beds24::get_total_bookings();
        $total_pages = ceil($total_bookings / $limit);

        echo Utils::getTemplate('dcm_beds24_bookings-page', ['bookings' => $bookings, 'total_pages' => $total_pages, 'current_page' => $current_page]);
    }

    static function dcm_blocked_guests_page()
    {
        $limit = 30;
        $current_page = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
        $blocked_guests = Beds24::get_recent_bookings($current_page, $limit, true);
        $total_bookings = Beds24::get_total_bookings(true);
        $total_pages = ceil($total_bookings / $limit);
        echo Utils::getTemplate('dcm_blocked_guests-page', ['bookings' => $blocked_guests, 'total_pages' => $total_pages, 'current_page' => $current_page]);
    }

    static function dcm_blocked_settings_page()
    {
        ?>
        <div class="wrap">
            <h1>My Plugin Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('dcm_options_group');
                do_settings_sections('dcm-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }


    static function insert_dcm_shortcodes($page_id, $shortcode, $code)
    {
        global $wpdb;
        $tablename = $wpdb->prefix . 'doorcodes';
        $sql = $wpdb->prepare('INSERT INTO ' . $tablename . ' (
            page_id, shortcode, code 
         )
        VALUES
            (%d, %s, %s)', [$page_id, $shortcode, $code]);

        $wpdb->query($sql);
    }

    static function check_door_differences($page_id, $doors)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'doorcodes';
        $doors_on_db = $wpdb->get_results($wpdb->prepare('SELECT * from ' . $table_name . ' WHERE page_id=%d', [$page_id]), ARRAY_A);
        if (empty($doors_on_db)) return $doors;
        $differences = [];
        $need_to_create = [];
        $door_list = array_column($doors_on_db, 'shortcode');

        foreach ($doors_on_db as $door_on_db) {
            if (!in_array($door_on_db["shortcode"], $doors)) {
                $differences[] = $door_on_db["id"];
            }
        }

        foreach ($doors as $door) {
            if (!in_array($door, $door_list)) {
                $need_to_create[] = $door;
            }
        }

        if (empty($differences)) {
            return $need_to_create;
        }
        $ids = implode("','", $differences);
        $wpdb->query("DELETE FROM " . $table_name . " WHERE id IN (" . $ids . ")");
        return $need_to_create;
    }

    static function generateRandomSlug($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}