<?php
$post_id = $title = $url = '';

if (isset($data)) {
    extract($data);
}
?>
<tr class="dcm__table-page" data-post_id="<?php echo $post_id ?>">
    <td><?php echo $post_id ?></td>
    <td><?php echo $title ?></td>
    <td>
        <button class="dcm__button dcm__view-button" data-post_id="<?php echo $post_id ?>"> View Url</button>
    </td>
</tr>