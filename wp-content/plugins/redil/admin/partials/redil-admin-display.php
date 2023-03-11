<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://redil.io
 * @since      1.0.0
 *
 * @package    Redil
 * @subpackage Redil/admin/partials
 */

//
// INSERT SOME DATA 
//
// $group = array(
//     'post_title'  => 'bcc grenland ungdom',
//     'post_status' => 'publish',
//     'post_type'   => 'redil_group'

// );

//wp_insert_post( $group );

$json    = '
{
    "rules":
    [
        {
            "conditions":
            [
                {
                    "key"    : "churchName",
                    "compare": "==",
                    "value"  : "Grenland"
                }
            ]
        }
    ]
}';

//update_post_meta( 1394, 'redil_group_ruleset', $json, true );
//
// END INSERT DATA
//

$args = array(
    'numberposts' => -1,
    'post_type'   => 'redil_group'
);

$groups = get_posts( $args );
$rules  = get_post_meta ( $groups[0]->ID, 'redil_group_ruleset' );

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<h1>Redil dashboard</h1>

<p>
    There are <?php echo count( $groups ); ?> defined groups
</p>

<p>
    <?php print_r( $rules ); ?>
</p>