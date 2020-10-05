<?php 
namespace UltimateMember\Collections;

class Users{

    protected static $user_id = null;

    function __construct(){
        $this->user_id = get_current_user_id();
    }

    function id(){
        if( ! self::$user_id )  self::$user_id = get_current_user_id();
       return self::$user_id;
    }

    function getData( $key ){

        um_fetch_user( self::$user_id );

        return um_user( $key );
    }

    /**
     * Includes related data to the user
     */
    function user_related( $arr_keys = [], $collection = [] ){

        $user_ids = $collection->pluck('user_id');
        
        $arr_user = [];

        $current_user_id = get_current_user_id();
        foreach( $user_ids as $uid ){

            um_fetch_user( $uid );
            $arr_data = [];

           
            foreach( $arr_keys as $key ){

                if( $key == 'avatar' ) 
                    $arr_data[ $key ] = $this->avatar( $uid ); 
                else 

                $arr_data[ $key ] = um_user( $key );
            }

            $arr_data['owner'] = $current_user_id == $uid ?:false;
           
            $arr_user[  ] = $arr_data;
        }

        return $arr_user;
    }

    function avatar( $uid = '' ){
        if( empty( $uid ) ){
            $uid = self::$user_id;
        }

        return um_get_avatar_url( get_avatar( $uid, 40 ) );
    }
}