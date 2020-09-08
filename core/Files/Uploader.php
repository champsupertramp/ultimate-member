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


    function process(){
        
        $fs = new UM_FileSystem();
        $fs->setBaseDir( "um-user-events" );
        $fs->setUserID( get_current_user_id() );
        $fs->setFilePrefix( "events_photo" );
        return $response = $fs->process();
      
       
         
    }

}