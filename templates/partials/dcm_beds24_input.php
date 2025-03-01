<?php

use DCM\Utils;

$propId = $template1 = '';

if (isset($data)) {
    extract($data);
}
?>
<div class="form-wrapper dcm__form-wrapper" data-prop_id="<?php echo $propId ?>">
    <div class='full-input'>
        <label for='name'> Template 1</label>
        <textarea name="" id="" cols="30" rows="10" class="dcm_beds24_input"><?php echo $template1 ?></textarea>
    </div>
</div>
<button class="dcm__button dcm__save-beds24-property-button">
    <div class="text">Save</div>
    <div class="loader"></div>
</button>
