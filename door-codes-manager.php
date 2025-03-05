<?php
/**
 * Plugin Name: Door Codes Manager
 * Plugin URI:
 * Description: Door Codes Manager.
 * Version: 2.8.3
 * License: GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Author: Mirza
 **/

use DCM\App;


defined('ABSPATH') or die('You cant access this');

define('DCM_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('DCM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('DCM_SITE_URL', get_site_url());

spl_autoload_register(function ($class_name) {
    if (false !== strpos($class_name, 'DCM\\')) {
        $class_file = str_replace('DCM\\', '', $class_name) . '.class.php';
        $class_file = str_replace('\\', DIRECTORY_SEPARATOR, $class_file);
        $class_file_path = DCM_PLUGIN_PATH . 'includes' . DIRECTORY_SEPARATOR . $class_file;
        if (file_exists($class_file_path)) {
            require_once($class_file_path);
        }
    }
});

App::run();