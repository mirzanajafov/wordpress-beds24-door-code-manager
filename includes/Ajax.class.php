<?php

namespace DCM;

use DCM\Beds24;

class Ajax
{
    public function __construct()
    {
        add_action('wp_ajax_dcm_saveDoorCodeInputs', [$this, 'saveDoorCodes']);
        add_action('wp_ajax_dcm_getDoorCodeInputs', [$this, 'dcmGetDoorCodeInputs']);
        add_action('wp_ajax_dcm_getRoomUrl', [$this, 'dcm_getRoomUrl']);
        add_action('wp_ajax_dcm_updatePageUrl', [$this, 'dcm_updatePageUrl']);
        add_action('wp_ajax_dcm_getBeds24PropertyInfo', [$this, 'dcm_getBeds24PropertyInfo']);
        add_action('wp_ajax_dcm_updateBeds24PropTemplate', [$this, 'dcm_updateBeds24PropTemplate']);
        add_action('wp_ajax_dcm_getBookingByCriteria', [$this, 'dcm_getBookingByCriteria']);
        add_action('wp_ajax_nopriv_dcm_getBookingByCriteria', [$this, 'dcm_getBookingByCriteria']);
        add_action('wp_ajax_dcm_blockGuest', [$this, 'dcm_blockGuest']);
    }

    static function register()
    {
        new self();
    }

    function saveDoorCodes()
    {
        global $wpdb;
        $tablename = $wpdb->prefix . 'doorcodes';

        $form_data = json_decode(stripslashes(sanitize_text_field($_POST['data'])));
        $page_id = $form_data->page_id;

        foreach ($form_data->codes as $shortcode => $code):
            $wpdb->update($tablename, [
                'code' => $code,
            ],
                [
                    'page_id' => $page_id,
                    'shortcode' => $shortcode
                ]
            );
        endforeach;
        $this->dcm_getRoomUrl($page_id);
        wp_die();
    }

    function dcmGetDoorCodeInputs()
    {
        global $wpdb;
        $post_id = $_GET['post_id'];
        $codes = $wpdb->get_results("SELECT * from wp_doorcodes WHERE page_id = " . $post_id);
        echo Utils::getTemplate('partials' . DIRECTORY_SEPARATOR . 'dcm_input', ['post_id' => $post_id, 'codes' => $codes]);
        wp_die();
    }

    function dcm_getRoomUrl($id = 0)
    {
        $post_id = isset($_GET['post_id']) ? $_GET['post_id'] : $id;

        $url = get_the_permalink($post_id);
        echo Utils::getTemplate('partials' . DIRECTORY_SEPARATOR . 'dcm_url-input', ['post_id' => $post_id, 'url' => $url]);
        wp_die();
    }

    function dcm_updatePageUrl()
    {
        $new_slug = Utils::generateRandomSlug(15);
        $page_id = $_POST['page_id'];
        $old_url = $_POST['old_url'];

        wp_update_post([
            'ID' => $page_id,
            'post_name' => $new_slug
        ]);
        $new_url = DCM_SITE_URL . '/' . $new_slug;

        Beds24::update_booking_checkin_url($old_url, $new_url);
        Beds24::updateRoomTemplate($page_id, $new_url);
        echo $new_url;
        wp_die();
    }

    function dcm_getBeds24PropertyInfo()
    {
        $prop_id = $_GET['propId'];

        $template1 = Beds24::getPropertyTemplate($prop_id);

        echo Utils::getTemplate('partials' . DIRECTORY_SEPARATOR . 'dcm_beds24_input', ['propId' => $prop_id, 'template1' => $template1]);
        wp_die();
    }

    function dcm_updateBeds24PropTemplate()
    {
        $form_data = json_decode(stripslashes(sanitize_text_field($_POST['data'])));
        $prop_id = $form_data->propId;
        $template = $form_data->template;

        Beds24::updatePropertyTemplate($prop_id, $template);

        echo Utils::getTemplate('partials' . DIRECTORY_SEPARATOR . 'dcm_beds24_input', ['propId' => $prop_id, 'template1' => $template]);
        wp_die();


    }

    function dcm_getBookingByCriteria()
    {
        check_ajax_referer('dcm_search_nonce', 'security');
        $key = isset($_GET['searchBy']) ? sanitize_text_field($_GET['searchBy']) : '';
        $value = isset($_GET['searchValue']) ? sanitize_text_field($_GET['searchValue']) : '';

        if (empty($key) || empty($value)) {
            wp_send_json_error(['message' => 'Key and value are required']);
        }

        $results = Beds24::get_booking_by_criteria($key, $value);

        if (is_string($results)) {
            wp_send_json_error(['message' => $results]);
        }
        $html = Utils::getTemplate('partials' . DIRECTORY_SEPARATOR . 'dcm_booking_results', ['bookings' => $results]);
        wp_send_json_success(['html' => $html]);

        wp_die();
    }

    function dcm_blockGuest()
    {
        $data = json_decode(stripslashes(sanitize_text_field($_POST['data'])), ARRAY_A);
        $booking_id = $data['bookingId'];
        $blocked = $data['blocked'];
        $result = Beds24::block_guest($booking_id, $blocked ? 0 : 1);

        wp_send_json_success(['result' => $result]);

        wp_die();
    }


}