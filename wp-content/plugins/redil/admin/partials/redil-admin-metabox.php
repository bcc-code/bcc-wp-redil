<?php

$args = array(
    'numberposts' => -1,
    'post_type'   => 'redil_group'
);

$groups   = get_posts( $args );
$group_id = get_post_meta( get_the_ID(), 'redil_group_id', true );

?>
<style>
    select.redil-select {
        width: 100%
    }
</style>

<p class="howto">Target groups</p>

<div>
    <select name="redil_group_id" id="redil_group_id" class="redil-select">
        <option value="0">Everyone</option>
        <?php
            foreach ( $groups as $group ) {
                ?>
                <option value="<?php echo $group->ID ?>" <?php echo $group->ID == $group_id ? 'selected' : '' ?>><?php echo $group->post_title ?></option>
                <?php
            }
        ?>
    </select>
</div>