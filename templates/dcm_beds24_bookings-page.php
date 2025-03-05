<?php

use DCM\Beds24;

$bookings = $total_pages = [];
$current_page = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
if (isset($data)) {
    extract($data);
}

$search_query = '';

if (!empty($_GET['search'])) {
    $search_query = sanitize_text_field($_GET['search']);
    $limit = 30;
    $bookings = Beds24::get_bookings_by_search($search_query, $current_page, $limit);
    $total_bookings = Beds24::get_total_bookings(null, $search_query);
    $total_pages = ceil($total_bookings / $limit);
}
?>

<div class="wrap dcm">
    <header class="dcm_beds24_bookings_header">
        <h2>Beds24 Bookings</h2>
        <form method="GET" class="dcm__search-form">
            <input type="hidden" name="page" value="door_codes_manager_beds24_bookings">
            <input type="text" name="search" value="<?php echo esc_attr($search_query); ?>"
                   placeholder="Search bookings...">
            <button type="submit" class="button-primary">Search</button>
        </form>
    </header>

    <section class="dcm__table-section responsive-table-wrapper">
        <table class="responsive-table">
            <thead>
            <tr>
                <th>Booking ID</th>
                <th>Full Name</th>
                <th>Phone</th>
                <th>Check in Date</th>
                <th>Check Out Date</th>
                <th>Reservation Number</th>
                <th>Actions</th>
            </tr>

            </thead>
            <tbody>
            <?php
            if (empty($bookings)) {
                echo '<tr><td colspan="7">No bookings found.</td></tr>';
            } else {
                foreach ($bookings as $booking) {
                    echo '<tr>';
                    echo '<td>' . esc_html($booking['booking_id']) . '</td>';
                    echo '<td>' . esc_html($booking['full_name']) . '</td>';
                    echo '<td>' . esc_html($booking['phone']) . '</td>';
                    echo '<td>' . esc_html($booking['checkin']) . '</td>';
                    echo '<td>' . esc_html($booking['checkout']) . '</td>';
                    echo '<td>' . esc_html($booking['reservation_number']) . '</td>';
                    echo '<td> <button style="font-size: inherit;width: 80%;
    max-height: 40px;
    justify-content: center;" class="dcm__button dcm__guest-blocker" data-blocked="' . esc_html($booking['blocked']) . '" data-id="' . esc_html($booking['booking_id']) . '">
                        ' . ($booking['blocked'] ? "Unblock" : "Block") . '
                    </button> </td>';
                    echo '</tr>';
                }
            }
            ?>
            </tbody>
        </table>
        <?php
        if ($total_pages > 1) {
            echo '<div class="tablenav">';
            echo '<div class="tablenav-pages">';


            if ($current_page > 1) {
                $prev_page = $current_page - 1;
                $prev_url = add_query_arg('paged', $prev_page);
                echo '<a class="prev-page" href="' . esc_url($prev_url) . '">&laquo; Previous</a>';
            }


            for ($i = 1; $i <= $total_pages; $i++) {
                $class = ($i === $current_page) ? 'class="current"' : '';
                $url = add_query_arg('paged', $i);
                echo '<a ' . $class . ' href="' . esc_url($url) . '">' . $i . '</a>';
            }


            if ($current_page < $total_pages) {
                $next_page = $current_page + 1;
                $next_url = add_query_arg('paged', $next_page);
                echo '<a class="next-page" href="' . esc_url($next_url) . '">Next &raquo;</a>';
            }

            echo '</div>';
            echo '</div>';
        }
        ?>
    </section>


</div>