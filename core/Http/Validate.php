<?php 
namespace UltimateMember\Http;

class Validate{
    
    function nonce( $request ){
        
        $token = $request->get_header('X-UM-CSRF-Token');
        
        if( wp_verify_nonce( $token, "um-user-events-global-nonce" ) ) return true;
        
        
        return false;
    }
}