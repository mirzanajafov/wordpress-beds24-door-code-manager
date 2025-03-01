<?php

use DCM\Utils;

$post_id = $codes = '';

if (isset($data)) {
    extract($data);
}
?>

<div class="form-wrapper dcm__form-wrapper" data-page_id="<?php echo $post_id ?>">
    <?php foreach ($codes as $code): ?>
        <div class='full-input'>
            <label for='name'><?php echo $code->shortcode ?></label>
            <input type='text' name='name' class="dcm_input " data-shortcode="<?php echo $code->shortcode ?>"
                   value="<?php echo $code->code ?>">
        </div>
    <?php endforeach; ?>
</div>
<?php echo Utils::getTemplate('partials' . DIRECTORY_SEPARATOR . 'dcm_save-button'); ?>
