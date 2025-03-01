<?php

use DCM\Utils;

$bookings = $total_pages = [];

if (isset($data)) {
    extract($data);
}
?>

<div class="wrap dcm">
    <h2>Beds24 Bookings</h2>
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
            </tr>

            </thead>
            <tbody>
            <?php
            if (empty($bookings)) {
                echo '<tr><td colspan="6">No bookings found.</td></tr>';
            } else {
                foreach ($bookings as $booking) {
                    echo '<tr>';
                    echo '<td>' . esc_html($booking['booking_id']) . '</td>';
                    echo '<td>' . esc_html($booking['full_name']) . '</td>';
                    echo '<td>' . esc_html($booking['phone']) . '</td>';
                    echo '<td>' . esc_html($booking['checkin']) . '</td>';
                    echo '<td>' . esc_html($booking['checkout']) . '</td>';
                    echo '<td>' . esc_html($booking['reservation_number']) . '</td>';
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