<?php

namespace UltimateMember\Files;

if ( ! defined( 'ABSPATH' ) ) exit;

use UltimateMember\Files\FileSystem as UM_FileSystem;

/**
 * Class User_Events_Enqueue
 * @package UltimateMember\Files
 */
class Uploader {

    public $fileName = null;

    function __construct(){
        

    }

    function progress(){
        
        return [
           'filename'  => $this->fileName
        ];
    }

    /**
     * Process upload
     */
    function process(\WP_REST_Request $request ){
        
        $baseDir = $request->get_param('baseDir');
        $prefix = $request->get_param('prefix');
        
        $user_id = apply_filters("um_upload_{$prefix}__user_id", get_current_user_id() );
        
        $fs = new UM_FileSystem();
        $fs->setUserID( $user_id );
        $fs->setBaseDir( $baseDir  );
        $fs->setFilePrefix( $prefix );

        return $response = $fs->process();
      
       
         
    }

}