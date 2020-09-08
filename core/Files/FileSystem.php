<?php

namespace UltimateMember\Files;

if ( ! defined( 'ABSPATH' ) ) exit;

require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
use WP_Filesystem_Base as WPFBase; 
use WP_Filesystem_Direct as WPFDirect; 

/**
 * Class FileSystem
 * @package UltimateMember\Files
 */
class FileSystem {

    public $isCompleted = false;

    public $fileName = null;

    private $base_dir = "";

    private $user_id = null;

    private $file_prefix = "um_";

    /**
     * Start Upload Processing
     */
    function process(){
        
        $user_id = $this->getUserID();
        $temp_dir = UM()->uploader()->get_core_temp_dir()  . DIRECTORY_SEPARATOR .  $this->getBaseCustomDir() . DIRECTORY_SEPARATOR;
        $custom_dir = UM()->uploader()->get_upload_base_dir() . $this->getBaseCustomDir() . DIRECTORY_SEPARATOR;
       
        if ( ! is_dir( $temp_dir ) ) {
            wp_mkdir_p( $temp_dir );
        }

        if ( ! is_dir( $custom_dir ) ) {
            wp_mkdir_p( $custom_dir );
        }
         
        //check if request is GET and the requested chunk exists or not. this makes testChunks work
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            if(!(isset($_GET['resumableIdentifier']) && trim($_GET['resumableIdentifier'])!='')){
                $_GET['resumableIdentifier']='';
            }
            $temp_dir .= $_GET['resumableIdentifier'];
            if(!(isset($_GET['resumableFilename']) && trim($_GET['resumableFilename'])!='')){
                $_GET['resumableFilename']='';
            }
            if(!(isset($_GET['resumableChunkNumber']) && trim($_GET['resumableChunkNumber'])!='')){
                $_GET['resumableChunkNumber']='';
            }
            $chunk_file = $temp_dir . $_GET['resumableFilename'].'.part'.$_GET['resumableChunkNumber'];

            if ( file_exists( $chunk_file ) ) {
                return new \WP_REST_Response("Okay", 200);
            } else {
                return new \WP_REST_Response("Not Found", 204);
            }
        }


        // loop through files and move the chunks to a temporarily created directory
        if ( ! empty( $_FILES ) ) {
            
            foreach ($_FILES as $file) {

                // check the error status
                if ($file['error'] != 0) {
                    continue;
                }

                // init the destination file (format <filename.ext>.part<#chunk>
                // the file is stored in a temporary directory
                if(isset($_REQUEST['resumableIdentifier']) && trim($_REQUEST['resumableIdentifier'])!=''){
                    $temp_dir .=  $_REQUEST['resumableIdentifier'] . DIRECTORY_SEPARATOR;
                    
                    $temp_dir_unused = rtrim($temp_dir, '/').'_UNUSED';

                    if ( ! is_dir(  $temp_dir ) && ! is_dir( $temp_dir_unused ) ) {
                        wp_mkdir_p(  $temp_dir );
                    }
                }

                $uuid           = $_REQUEST['uuid_filename'];
                $image_type     = wp_check_filetype($_REQUEST['resumableFilename'] );
                $ext            = strtolower( trim( $image_type['ext'], ' \/.' ) );
                $fileName       = "{$this->getFilePrefix()}_{$uuid}.{$ext}";
                $_REQUEST['newFileName'] = UM()->uploader()->get_upload_base_url() . $this->getBaseCustomDir() . DIRECTORY_SEPARATOR . $fileName;
               
                // wp-content/uploads/ultimatemember/tmp/um-user-events/<temp dir destination>/
                $dest_file = $temp_dir . DIRECTORY_SEPARATOR .$_REQUEST['resumableFilename'] .'.part'. $_REQUEST['resumableChunkNumber'];
                  
                // move the temporary file
                if ( ! @move_uploaded_file($file['tmp_name'], $dest_file) ) {
 
                }else{
                 
                    // check if all the parts present, and create the final destination file
                   return $this->createFileFromChunks($temp_dir, $fileName,$_REQUEST['resumableFilename'], $_REQUEST['resumableChunkSize'], $_POST['resumableTotalSize'],$_POST['resumableTotalChunks'], $custom_dir , $dest_file, $_REQUEST['newFileName']);
                }
            }
        }
    }

    /**
     * Set Base Directory
     * @since 1.0
     */
    function setBaseDir( $base_dir ){

        $this->base_dir = $base_dir;
    }

    /**
     * Get Base Directory
     * @since 1.0
     */
    function getBaseCustomDir(){

        return $this->base_dir;
    }

    /**
     * Set Uloader's User ID
     * @since 1.0
     */
    function setUserID( $user_id ){
    
       $this->user_id = $user_id;

    }

    /**
     * Get Uploader's User ID
     * @since 1.0
     */
    function getUserID(){
    
        return $this->user_id;
 
    }

    /**
     * Set File's Prefix
     * @since 1.0
     */
    function setFilePrefix( $file_prefix ){

        $this->file_prefix = $file_prefix;

    }

    /**
     * Get File's Prefix
     * @since 1.0
     */
    function getFilePrefix( ){

       return $this->file_prefix;

    }

    /**
     * Check if upload is completed
     * @since 1.0
     */
    function isUploadComplete(){

        return $this->isCompleted;
    }

    /**
     *
     * Check if all the parts exist, and 
     * gather all the parts of the file together
     * @param string $temp_dir - the temporary directory holding all the parts of the file
     * @param string $fileName - the original file name
     * @param string $chunkSize - each chunk size (in bytes)
     * @param string $totalSize - original file size (in bytes)
     */
    function createFileFromChunks( $temp_dir, $fileName, $oldFileName, $chunkSize, $totalSize, $total_files, $custom_dir, $dest_file, $final_file_url ) {

        // count all the parts of this file
        $total_files_on_server_size = 0;
        $temp_total = 0;
        foreach( @scandir($temp_dir , 1 ) as $file) {
           
            if( is_dir( $file ) ) continue;

           $tempfilesize = @filesize( $temp_dir . $file );
           $total_files_on_server_size += $temp_total + $tempfilesize;
        }
        
       
        // check that all the parts are present
        // If the Size of all the chunks on the server is equal to the size of the file uploaded.
        if ($total_files_on_server_size >= $totalSize) {
        // create the final destination file 
            $this->fileName =  $custom_dir . $fileName;
             if (($fp = fopen( $custom_dir . $fileName, 'w')) !== false) {
                for ( $i = 1; $i <= $total_files; $i++ ) {
                    fwrite($fp, file_get_contents( $temp_dir . DIRECTORY_SEPARATOR . $oldFileName . ".part". $i ) );
                }
                fclose($fp);

            } else {
                
                return new \WP_REST_Response("chunk_file:". $chunk_file , 204);

            }

            $temp_dir_unused = rtrim($temp_dir, '/').'_UNUSED';

            // rename the temporary directory (to avoid access from other 
            // concurrent chunks uploads) and than delete it
            if( ! is_dir( $temp_dir_unused ) ){
                //rename( $temp_dir, $temp_dir_unused );
                $wpfd = new WPFDirect(FALSE);
                $wpfd->rmdir($temp_dir, true);
                
                $this->isCompleted = true;
                
                return new \WP_REST_Response([
                    "url" => $final_file_url,
                    "completed" => true,
                ] , 200);

            }

           

        }
      

    }


}