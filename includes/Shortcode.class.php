<?php

namespace DCM;

class Shortcode
{
    public function __construct()
    {
        add_shortcode('dcm_reservation_info', [$this, 'reservation_info_form']);
    }

    static function register()
    {
        new self();
    }

    function reservation_info_form()
    {
        return Utils::getTemplate('shortcodes/reservation_info-form');
    }

}