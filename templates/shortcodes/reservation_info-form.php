<?php

use DCM\Utils;

?>
<div class="dcm__reservation-info-form-wrapper">


    <form class="dcm__form" id="reservation_info_form">
        <div>
            <label for="reservation_info">Search By:</label>
            <select name="reservation_info" id="reservation_info_searchBy">
                <option value="full_name">Full Name</option>
                <option value="phone">Phone</option>
                <option value="reservation_number">Reservation Number</option>
            </select>
        </div>
        <div>
            <input type="text" name="reservation_info_search_value">
        </div>


        <button class="dcm__button" id="searchReservationButton" type="button">Search</button>
    </form>

    <div id="searchReservationModal" class="dcm__modal">
        <div class="dcm__modal-content">

            <span class="dcm__modal-close" id="closeReservationModal">&times;</span>

          
            <h3>Search Result</h3>
            <p id="reservationModalMessage">Results will be displayed here...</p>
        </div>
    </div>
</div>