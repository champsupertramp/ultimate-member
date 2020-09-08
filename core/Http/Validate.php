<?php 
namespace UltimateMember\Http;

class Validate{
    
    function nonce( $request ){
        
        $token = $request->get_header('X-WP-Nonce');
        
        if( wp_verify_nonce( $token, "wp_rest" ) ) return true;
        
        return false;
    }
}