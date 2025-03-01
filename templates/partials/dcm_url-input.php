<?php

use DCM\Utils;

$post_id = $url = '';

if (isset($data)) {
    extract($data);
}
?>

<div class="form-wrapper dcm__form-wrapper" data-page_id="<?php echo $post_id ?>">
    <div class='full-input'>
        <label for='name' class="ClickToCopyUrl">Click to copy url</label>
        <input type='text' name='name' class="dcm_url-input " data-shortcode="<?php echo $url ?>"
               value="<?php echo $url ?>" readonly>
    </div>
</div>
<?php echo Utils::getTemplate('partials' . DIRECTORY_SEPARATOR . 'dcm_update-url-button'); ?>
