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


            // Multiple File Uploader 
            register_rest_route( 'um-core/api/v1', 'files/upload', array(
                'methods' => 'GET, POST, PUT, PATCH, DELETE',
                'callback' => array("UltimateMember\\Files\\Uploader", "process" ),
                'permission_callback' => array("UltimateMember\\Http\\Validate", "nonce" ),
                'args' => [
                    'resumableChunkNumber',
                    'resumableChunkSize',
                    'resumableCurrentChunkSize',
                    'resumableTotalSize',
                    'resumableType',
                    'resumableIdentifier',
                    'resumableFilename',
                    'resumableRelativePath',
                    'resumableTotalChunks',
                    'uuid_filename',
                    'baseDir'

                ],
            ) );
            
            /**
             * Data Query: SELECT, UPDATE, DELETE, INSERT
             */
            register_rest_route( 'um-core/api/v1', 'collections/', array(
                'methods' => 'GET',
                'callback' => array("UltimateMember\\Http\\DataController", "handleGet" ),
                'permission_callback' => array("UltimateMember\\Http\\Validate", "nonce" ),
            ) );

            register_rest_route( 'um-core/api/v1', 'collections/', array(
                'methods' => 'POST',
                'callback' => array("UltimateMember\\Http\\DataController", "handlePost" ),
                'permission_callback' => array("UltimateMember\\Http\\Validate", "nonce" ),
            ) );

            register_rest_route( 'um-core/api/v1', 'collections/', array(
                'methods' => 'PUT',
                'callback' => array("UltimateMember\\Http\\DataController", "handlePut" ),
                'permission_callback' => array("UltimateMember\\Http\\Validate", "nonce" ),
            ) );

            register_rest_route( 'um-core/api/v1', 'collections/', array(
                'methods' => 'PATCH',
                'callback' => array("UltimateMember\\Http\\DataController", "handlePatch" ),
                'permission_callback' => array("UltimateMember\\Http\\Validate", "nonce" ),
            ) );

            register_rest_route( 'um-core/api/v1', 'collections/', array(
                'methods' => "DELETE",
                'callback' => array("UltimateMember\\Http\\DataController", "handleDelete" ),
                 'permission_callback' => array("UltimateMember\\Http\\Validate", "nonce" ),
            ) );
            
        } );            
    }

}