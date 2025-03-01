<?php

namespace DCM;

use WP_REST_Response;

class Beds24
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'register_webhook_endpoint'));
    }

    static function register()
    {
        new self();
    }


    function insert_or_update_booking($booking)
    {
        global $wpdb;
        $tablename = $wpdb->prefix . "beds24_bookings";

        // Checking booking_id in the db
        $existing_booking_id = $wpdb->get_var($wpdb->prepare(
            "SELECT booking_id FROM $tablename WHERE booking_id = %d", $booking["booking_id"]
        ));

        $first_name = $booking["first_name"];
        $last_name = $booking["last_name"];
        $phone = $booking["phone"];
        $check_in = $booking["check_in"];
        $check_out = $booking["check_out"];
        $reservation_number = $booking["reservation_number"];
        $checkin_link = $booking["check_in_link"];
        $full_name = $booking["first_name"] . ' ' . $booking["last_name"];

        if ($existing_booking_id) {
            // if booking_id exist then update
            $updated = $wpdb->update(
                $tablename,
                [
                    'firstname' => $first_name,
                    'lastname' => $last_name,
                    'phone' => $phone,
                    'checkin' => $check_in,
                    'checkout' => $check_out,
                    'reservation_number' => $reservation_number,
                    'checkin_link' => $checkin_link,
                    'full_name' => $full_name,
                ],
                ['booking_id' => $booking["booking_id"]],
                ['%s', '%s', '%s', '%s', '%s', '%s', '%s'],
                ['%d']
            );

            if ($updated !== false) {
                return "Booking ID {$booking["booking_id"]} updated.";
            } else {
                return "Something get wrong when updating Booking ID {$booking["booking_id"]}";
            }
        } else {
            $inserted = $wpdb->insert(
                $tablename,
                [
                    'booking_id' => $booking["booking_id"],
                    'firstname' => $first_name,
                    'lastname' => $last_name,
                    'phone' => $phone,
                    'checkin' => $check_in,
                    'checkout' => $check_out,
                    'reservation_number' => $reservation_number,
                    'checkin_link' => $checkin_link,
                    'full_name' => $full_name,
                ],
                ['%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s']
            );

            if ($inserted) {
                return "Booking ID {$booking["booking_id"]} inserted.";
            } else {
                return "Something get wrong when inserting Booking ID {$booking["booking_id"]} .";
            }
        }
    }

    static function get_booking_by_criteria($searchBy, $searchValue, $limit = 1)
    {
        global $wpdb;

        $key = isset($_GET['searchBy']) ? sanitize_text_field($_GET['searchBy']) : $searchBy;
        $value = isset($_GET['searchValue']) ? sanitize_text_field($_GET['searchValue']) : $searchValue;
        $tablename = esc_sql($wpdb->prefix . "beds24_bookings");

        $query = "SELECT * FROM $tablename WHERE";


        $placeholders = [];
        if ($key === 'full_name') {
            $query .= " full_name = %s";
            $placeholders[] = $value;
        } elseif ($key === 'reservation_number') {
            $query .= " reservation_number = %s";
            $placeholders[] = $value;
        } elseif ($key === 'phone') {
            $query .= " phone = %s";
            $placeholders[] = preg_replace('/[^\d]/', '', $value);
        }
        $query .= " AND checkout >= %s";
        $placeholders[] = date('Y-m-d');// Today

        $query .= " LIMIT %d";
        $placeholders[] = (int)$limit;

        $prepared_query = $wpdb->prepare($query, $placeholders);
        error_log($prepared_query);
        $results = $wpdb->get_results($prepared_query, ARRAY_A);

        if (empty($results)) {
            if ($key === 'phone') {
                return " We couldn’t find a reservation with that phone number: $value. Some booking sites include a country code, and some don’t. Try entering your number with or without 1. Example: 1-267-555-1212 or 267-555-1212.";
            } else {
                return "Cannot find reservations with: $key : $value ";
            }
        }

        return $results;
    }

    static function get_recent_bookings($page = 1, $limit = 10)
    {
        global $wpdb;
        $tablename = esc_sql($wpdb->prefix . "beds24_bookings");
        $offset = ($page - 1) * $limit;

        $query = $wpdb->prepare("
            SELECT * 
            FROM $tablename 
            ORDER BY checkin DESC 
            LIMIT %d OFFSET %d
        ", $limit, $offset);
        return $wpdb->get_results($query, ARRAY_A);
    }

    static function update_booking_checkin_url($old_url, $new_url)
    {
        global $wpdb;
        $tablename = $wpdb->prefix . "beds24_bookings";

        $updated = $wpdb->update(
            $tablename,
            [
                'checkin_link' => $new_url,
            ],
            ['checkin_link' => $old_url]
        );
        if ($updated) {
            return "$updated bookings updated.";
        } else {
            return "Something went wrong ";
        }
    }

    static function get_total_bookings()
    {
        global $wpdb;
        $tablename = esc_sql($wpdb->prefix . "beds24_bookings");
        $query = "SELECT COUNT(*) FROM $tablename";

        return $wpdb->get_var($query);
    }

    static function getPropertyTemplate($prop_id)
    {
        $apiKey = get_field("beds24_apiKey", 172);
        $propKey = get_field("prop_" . $prop_id, 172);

        if (!$apiKey || !$propKey) {
            return '';
        }

        $body = [
            'authentication' => [
                'apiKey' => $apiKey,
                'propKey' => $propKey
            ],
            'includeRooms' => false,
            'includeRoomUnits' => false,
            'includeAccountAccess' => false
        ];
        $args = [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode($body),
            'data_format' => 'body',
        ];


        $response = wp_remote_post('https://api.beds24.com/json/getProperty', $args);
        $responseBody = wp_remote_retrieve_body($response);
        $result = json_decode($responseBody);
        return $result->getProperty[0]->template1;
    }

    static function updatePropertyTemplate($prop_id, $template)
    {
        $apiKey = get_field("beds24_apiKey", 172);
        $propKey = get_field("prop_" . $prop_id, 172);

        if (!$apiKey || !$propKey) {
            return;
        }

        $body = [
            'authentication' => [
                'apiKey' => $apiKey,
                'propKey' => $propKey
            ],
            'setProperty' => [
                [
                    'action' => 'modify',
                    'template1' => $template
                ]
            ]
        ];

        $args = [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode($body),
            'data_format' => 'body',
        ];

        wp_remote_post('https://api.beds24.com/json/setProperty', $args);
    }

    static function setRoomTemplate($apiKey, $propKey, $room_id, $new_url)
    {
        $body = [
            'authentication' => [
                'apiKey' => $apiKey,
                'propKey' => $propKey
            ],
            'setProperty' => [
                [
                    'action' => 'modify',
                    'roomTypes' => [
                        [
                            'action' => 'modify',
                            'roomId' => $room_id,
                            'template1' => $new_url
                        ]
                    ],
                ]
            ]
        ];

        $args = [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode($body),
            'data_format' => 'body',
        ];

        wp_remote_post('https://api.beds24.com/json/setProperty', $args);
    }

    static function updateRoomTemplate($page_id, $new_url)
    {
        $room_properties = get_field('beds24_properties', $page_id);

        if (empty($room_properties)) {
            return;
        }
        foreach ($room_properties as $room_property) {
            $prop_id = $room_property['Beds24_prop_id'];
            $room_id = $room_property['Beds24_room_id'];

            $apiKey = get_field("beds24_apiKey", 172);
            $propKey = get_field("prop_" . $prop_id, 172);

            if (!$apiKey || !$propKey) {
                return;
            }

            self::setRoomTemplate($apiKey, $propKey, $room_id, $new_url);


        }


    }

    public function register_webhook_endpoint()
    {
        register_rest_route('beds24-to-sheets/v1', '/webhook', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_webhook'),
            'permission_callback' => '__return_true'
        ));

        register_rest_route('vida-call/v1', '/webhook', array(
            'methods' => 'POST',
            'callback' => array($this, 'handle_vida_webhook'),
            'permission_callback' => '__return_true'
        ));
    }

    private function process_booking_data($booking)
    {
        $booking_info = [
            'booking_id' => $booking['id'] ?? '',
            'first_name' => $booking['firstName'] ?? '',
            'last_name' => $booking['lastName'] ?? '',
            'phone' => !empty(preg_replace('/[^\d]/', '', $booking['phone'])) ? preg_replace('/[^\d]/', '', $booking['phone']) : (preg_replace('/[^\d]/', '', $booking['mobile']) ?? ''),
            'check_in' => $booking['arrival'] ?? '',
            'check_out' => $booking['departure'] ?? '',
            'reservation_number' => !empty($booking['apiReference']) ? $booking['apiReference'] : null,
            'check_in_link' => $this->get_check_in_url($booking['propertyId'], $booking['roomId'])
//            'check_in_link' => $booking['checkinurl']

        ];


        return $this->insert_or_update_booking($booking_info);
    }

    static function get_check_in_url($prop_id, $room_id)
    {
        $token = "rZYq9ubijFTCo3RGu4HAd2RsjE1fjIdrFAzRs0Dnww1hC7pMO0seBQgQ8Uwlklj7TmJCpJpvc+FQzLcBkF43TmhGftdPG1WYJ4uKQtCSsD1PB3yutLepaUkrdB52p9ccbL759GPto9xjaq6M23/9JQ==";

        $args = [
            'headers' => [
                'Content-Type' => 'application/json',
                'token' => $token
            ],
            'timeout' => 30
        ];

        $url = add_query_arg(
            [
                'id' => $prop_id,
                'roomId' => $room_id
            ],
            'https://beds24.com/api/v2/properties'
        );

        $response = wp_remote_get($url, $args);


        if (is_wp_error($response)) {
            error_log('Beds24 API Error: ' . $response->get_error_message());
            return null;
        } else {
            $responseBody = wp_remote_retrieve_body($response);

            $result = json_decode($responseBody);

            if (json_last_error() === JSON_ERROR_NONE && isset($result->data[0]->roomTypes[0]->templates->template1)) {
                return $result->data[0]->roomTypes[0]->templates->template1;
            } else {
                error_log('Beds24 API: Invalid JSON response or missing data');
                return null;
            }
        }
    }

    public function handle_webhook($request)
    {
        $data = $request->get_json_params();


        if (!isset($data['booking']['id'])) {
            return new WP_REST_Response('Invalid data: booking id is required', 400);
        }


        $result = $this->process_booking_data($data['booking']);
        if ($result) {
            return new WP_REST_Response($result, 200);
        } else {
            return new WP_REST_Response('Processing failed', 500);
        }
    }

    public function handle_vida_webhook($request)
    {
        $data = $request->get_json_params();

        $phone = $data['phone'];
        error_log('Beds24 API Request: ' . print_r($data, true));
        $result = $this->get_booking_by_criteria("phone", $phone);

        error_log('Beds24 result from db: ' . $result);


        if ($result) {
            return new WP_REST_Response($result, 200);
        } else {
            return new WP_REST_Response('Processing failed', 500);
        }
    }
}