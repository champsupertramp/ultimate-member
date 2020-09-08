<?php 
namespace UltimateMember\Collections;

class Users{

    /**
     * Includes related data to the user
     */
    function user_related( $arr_keys = [], $collection = [] ){

        $user_ids = $collection->pluck('user_id');
        
        $arr_user = [];
        foreach( $user_ids as $uid ){

            um_fetch_user( $uid );
            $arr_data = [];

            foreach( $arr_keys as $key ){

                if( $key == 'avatar' ) 
                    $arr_data[ $key ] = $this->avatar( $uid ); 
                else 

                $arr_data[ $key ] = um_user( $key );
            }
            $arr_user[  ] = $arr_data;
        }

        return $arr_user;
    }

    function avatar( $uid ){

        return um_get_avatar_url( get_avatar( $uid, 40 ) );
    }
}