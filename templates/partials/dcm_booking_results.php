<?php
$bookings = [];

if (isset($data)) {
    extract($data);
}

if (empty($bookings)) {
    echo '<p style="color: red;">Cannot find any reservation.</p>';
    return;
}
?>

<div class="dcm__booking-results">
    <?php foreach ($bookings as $booking): ?>
        <div class="booking-item">
            <h3>Booking ID: <?php echo esc_html($booking['booking_id']); ?></h3>
            <h3>Check-in
                Url: <?php echo '<a href="' . esc_url($booking['checkin_link']) . '">Click Here</a>'; ?></h3>

            <p><strong>Full Name:</strong> <?php echo esc_html($booking['full_name']); ?></p>
            <p><strong>Phone:</strong> <?php echo esc_html($booking['phone']); ?></p>
            <p><strong>Check In:</strong> <?php echo esc_html($booking['checkin']); ?></p>
            <p><strong>Check Out:</strong> <?php echo esc_html($booking['checkout']); ?></p>
            <p><strong>Reservation Number:</strong> <?php echo esc_html($booking['reservation_number']); ?></p>
        </div>
        <hr>
    <?php endforeach; ?>
</div>