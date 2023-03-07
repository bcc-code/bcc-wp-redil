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


    //$rules  = get_post_meta ( $group->ID, 'redis_group_ruleset' );
    $json   = '
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
            },
            {
                "relation"  : "and",
                "conditions":
                [
                    {
                        "key"     : "age",
                        "compare" : ">",
                        "value"   : 12
                    },
                    {
                        "relation": "and",
                        "key"     : "age",
                        "compare" : "<",
                        "value"   : 36
                    }
                ]
            }
        ]
    }';

    $object = json_decode( $json, true );

    // // if ( $user['churchName'] == 'Grenland' && ( $user['age'] > 12 && $user['age'] < 36 ) )

    // $comparer = '';

    // foreach ( $object as $key => $rules )
    // {
    //     foreach ( $rules as $rule )
    //     {
    //         if ( $rule['relation'] ) 
    //         {
    //             switch ( $rule['relation'] )
    //             {
    //                 case 'and':
    //                     $comparer .= ' && ';
    //                     break;

    //                 case 'or':
    //                     $comparer .= ' || ';
    //                     break;
    //             }
    //         }

    //         $comparer .= '(';

    //         foreach ( $rule['conditions'] as $condition )
    //         {
    //             if ( $condition['relation'] )
    //             {
    //                 switch ( $condition['relation'] )
    //                 {
    //                     case 'and':
    //                         $comparer .= ' && ';
    //                         break;

    //                     case 'or':
    //                         $comparer .= ' || ';
    //                         break;
    //                 }
    //             }

    //             $quote     = is_numeric( $condition['value'] ) ? '' : '"';
    //             $comparer .= '$user->' . $condition['key'] . ' ' . $condition['compare'] . ' ' . $quote . $condition['value'] . $quote;
    //         }

    //         $comparer .= ')';
    //     }

    //     $comparer .= ';';
    // }

    $user   = Redil_User::get_bcc_user();
    $access = 'Denied';

    // if ( eval( $comparer ) ) 
    // {
    //     $access = 'Granted';
    // }
    // $group = array(
    //     'post_title'  => 'bcc grenland ungdom',
    //     'post_status' => 'publish',
    //     'post_type'   => 'redil_group'

    // );

    //wp_insert_post( $group );

    
    $args = array(
        'numberposts' => -1,
        'post_type'   => 'redil_group'
    );

    $groups = get_posts( $args );

    //add_post_meta( $groups[0]->ID, 'redis_group_ruleset', $object, true );

    $rules  = get_post_meta ( $groups[0]->ID, 'redis_group_ruleset', true );
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<h1>Redil dashboard</h1>

<p>
    <?php echo 'Found ' . count($groups) . ' groups'; ?>
</p>
<p>
    <?php echo json_encode( $rules ); ?>
</p>