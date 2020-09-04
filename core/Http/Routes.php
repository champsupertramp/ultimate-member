<?php 
namespace UltimateMember\Http;
class Routes{

    function __construct(){

        
        add_action( 'rest_api_init', function () {

            // Single User
            register_rest_route( 'um-core/api/v1', 'users/(?P<id>\d+)', array(
                'methods' => 'GET',
                'callback' => array("UltimateMember\\Http\\UserController", "single" ),
                'permission_callback' => array("UltimateMember\\Http\\Validate", "nonce" ),
                'args' => [
                    'id'  => [
                        'required' => true,
                    ],
                ],
            ) );
            
            // Multiple Users 
            register_rest_route( 'um-core/api/v1', 'users/', array(
                'methods' => 'GET',
                'callback' => array("UltimateMember\\Http\\UserController", "multiple" ),
                'permission_callback' => array("UltimateMember\\Http\\Validate", "nonce" ),
            ) );
            
        } );            
    }

}