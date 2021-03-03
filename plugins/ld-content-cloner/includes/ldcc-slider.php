<?php
if (false === ($slider_data = get_transient('_ldcc_slider_data'))) {
    $slider_data_json = wp_remote_get(
        'https://wisdmlabs.com/products-thumbs/promotions/ldcc/ldcc.json',
        array(
            'user-agent' => 'LDCC Slider'
        )
    );

    if (!is_wp_error($slider_data_json)) {
        $slider_data = json_decode(wp_remote_retrieve_body($slider_data_json));

        if ($slider_data) {
            set_transient('_ldcc_slider_data', $slider_data, 72 * HOUR_IN_SECONDS);
        }
    }
}
    $slider_content = isset($slider_loc) && isset($slider_data->$slider_loc) ? $slider_data->$slider_loc : array();
    $user_id = get_current_user_id();
    if (!current_user_can('manage_options')) {
        $slider_content = '';
    }
if (!empty($slider_content)) {
?>
<div id="myCarousel" class="carousel slide" data-ride="carousel">
<!-- Wrapper for slides -->
<div class="carousel-inner">
    <?php
    foreach ($slider_content as $index => $data) {
        ?>
            <div class="item
<?php
if ($index == 0) {
    echo 'active';
}
?>
            ">
                <a href="<?php echo $data->link;?>" target="_blank">
                    <img src="<?php echo $data->image;?>" width="100%"
                    alt="<?php echo $data->title;?>">
                </a>
                </div>
            <?php
    }
    ?>
    </div>

    <!-- Left and right controls -->
    <a class="left carousel-control" href="#myCarousel" data-slide="prev" style = "width:2% !important">
    <span class="glyphicon glyphicon-chevron-left"></span>
    <span class="sr-only">Previous</span>
    </a>
    <a class="right carousel-control" href="#myCarousel" data-slide="next" style = "width:2% !important">
    <span class="glyphicon glyphicon-chevron-right"></span>
    <span class="sr-only">Next</span>
    </a>
</div>
<?php
}
?>
