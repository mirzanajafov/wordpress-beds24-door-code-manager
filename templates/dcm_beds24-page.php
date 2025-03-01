<?php

use DCM\Utils;

$body = [
    'authentication' => ['apiKey' => "api123Kkey345pas"]
];
$args = [
    'headers' => ['Content-Type' => 'application/json'],
    'body' => json_encode($body),
    'data_format' => 'body',
];


$response = wp_remote_post('https://api.beds24.com/json/getProperties', $args);
$responseBody = wp_remote_retrieve_body($response);
$result = json_decode($responseBody);
$properties = $result->getProperties;
?>

<div class="wrap dcm">
    <h2>Beds 24</h2>
    <div class="dcm__beds24__list_section">
        <div class="dcm__beds24__list_content">
            <div class="dcm__beds24__list_wrapper form-wrapper ">
                <?php
                foreach ($properties as $property) :?>
                    <div class="dcm__beds24__list_property" data-prop_id="<?php echo $property->propId ?>">
                        <h4><?php echo $property->name ?></h4>
                    </div>
                <?php
                endforeach;
                ?>
            </div>
        </div>
    </div>
    <?php
    echo Utils::getTemplate('partials' . DIRECTORY_SEPARATOR . 'dcm_modal');
    ?>
</div>



