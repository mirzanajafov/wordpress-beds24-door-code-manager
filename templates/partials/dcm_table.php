<?php

use DCM\Utils;

$pages = [];

if (isset($data)) {
    extract($data);
}
?>
<table class="dcm__table">
    <thead>
    <tr>
        <th><b>Page ID</b></th>
        <th><b>Title</b></th>
        <th><b>Url</b></th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($pages as $page_data) {
        $title = $page_data->post_title;
        $post_id = $page_data->ID;
        $content = $page_data->post_content;
        $shortcodes = Utils::get_doors($content);
        if ($shortcodes) {
            echo Utils::getTemplate('partials' . DIRECTORY_SEPARATOR . 'dcm_table-row', ['post_id' => $post_id, 'title' => $title]);
        }
    }
    ?>
    </tbody>
</table>
