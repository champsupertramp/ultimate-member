<?php 
namespace UltimateMember\Forms;

class Enqueue{

    function __construct(){

    }

    function inline_script( $handle, $data, $position = 'after' ){

        wp_localize_script( $handle, $data, $position );

    }



}