<?php

use DCM\Utils;

$pages = get_pages();
?>

<div class="wrap dcm">
    <h2>Door Codes Manager</h2>
    <section class="dcm__table-section">
        <div class="dcm__table-content">
            <div class="dcm__table-wrapper">
                <?php
                echo Utils::getTemplate('partials' . DIRECTORY_SEPARATOR . 'dcm_table', ['pages' => $pages]);
                ?>
            </div>
        </div>
        <!-- modal -->
        <?php
        echo Utils::getTemplate('partials' . DIRECTORY_SEPARATOR . 'dcm_modal', ['pages' => $pages]);
        ?>
    </section>

</div>