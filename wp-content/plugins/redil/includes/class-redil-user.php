<?php

class Redil_User {
    public $churchName;
    public $birthdate;
    public $age;

    static function get_bcc_user() {
        if ( isset ( $_COOKIE['oidc_token_id'] ) )
        {
            $token_id = $_COOKIE['oidc_token_id'];
            $token    = get_transient( 'oidc_id_token_' . $token_id );
            $parts    = explode('.', $token );
            $json     = base64_decode(str_replace(array( '-', '_' ), array( '+', '/' ), $parts[ 1 ]));
            $data     = json_decode($json, true);
            $user     = new Redil_User();
            $today    = date("Y-m-d");

            $user->churchName = $data[ 'https://login.bcc.no/claims/churchName' ];
            $user->birthdate  = $data[ 'birthdate' ];
            $user->age        = date_diff( date_create( $user->birthdate ), date_create( $today ) )->format( '%y' );

            return $user;
        }

        return null;
    }
}