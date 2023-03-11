<?php

/**
 * 
 */

class Redil_Comparer {
    static function match($meta, $user) {
        if ( $user == null || $meta == null) {
            return false;
        }

        $ruleset = json_decode( $meta, true );
        $access  = false;

        foreach ( $ruleset['rules'] as $count => $rule )
        {
            $intermediate = true;
        
            foreach ( $rule['conditions'] as $index => $condition )
            {
                $operator = $condition['compare'];
                $key      = $condition['key'];
                $value    = $condition['value'];
        
                switch ( $operator )
                {
                    case '==':
                        $match = ( $user->{$key} == $value );
                        break;
        
                    case '>':
                        $match = ( $user->{$key} >  $value );
                        break;
        
                    case '<':
                        $match = ( $user->{$key} <  $value );
                        break;
        
                    default:
                        // Handle unsupported operators
                        break;
                }
        
                if ( $index == 0 )
                {
                    $intermediate = $match;
                }
        
                if ( $index > 0 && isset( $condition['relation'] ) )
                {
                    if ( $condition['relation'] == 'and' )
                    {
                        $intermediate = $match && $intermediate;
                    }
                    else
                    {
                        $intermediate = $match || $intermediate;
                    }
                }
        
                if ($count == 0) 
                {
                    $access = $match;
                }
            }
        
            if ( isset( $rule['relation'] ) )
            {
                if ( $rule['relation'] == 'or' )
                {
                    $access = $intermediate || $access;
                }
                else
                {
                    $access = $intermediate && $access;
                }
            }
        }

        return $access;
    }
}