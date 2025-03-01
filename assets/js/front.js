jQuery(document).ready(function ($) {
    const modal = $('#searchReservationModal');
    const modalMessage = $('#reservationModalMessage');


    $('#searchReservationButton').on('click', function () {

        const modal = $('#searchReservationModal');
        const modalMessage = $('#reservationModalMessage');
        const searchBy = $('#reservation_info_searchBy').val();
        const searchValue = $('input[name="reservation_info_search_value"]').val();


        if (!searchValue) {
            alert('Please Fill inputs!');
            return;
        }

        $.ajax({
            url: dcm.ajaxurl,
            type: 'GET',
            data: {
                action: 'dcm_getBookingByCriteria',
                searchBy: searchBy,
                searchValue: searchValue,
                security: dcm.nonce
            },
            beforeSend: function () {
                modalMessage.html(`
                    <div class="spinner">
                        <div class="spinner-inner"></div>
                    </div>
                `);
                modal.addClass('active');
            },
            success: function (response) {
                if (response.success) {

                    let bookings = response.data.html;

                    modalMessage.html(bookings);
                } else {
                    modalMessage.html(`<p style="color: red;">${response.data.message}</p>`);
                }
            },
            error: function () {
                resultsContainer.html('<p style="color: red;">Bir hata oluştu. Lütfen tekrar deneyiniz.</p>');
            }
        });


    });


    $('#closeReservationModal').on('click', function () {
        modal.removeClass('active');
    });


    $(window).on('click', function (e) {
        if ($(e.target).is(modal)) {
            modal.fadeOut();
        }
    });
});