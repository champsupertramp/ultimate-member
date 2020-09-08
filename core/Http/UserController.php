<?php 
namespace UltimateMember\Http;

use UltimateMember\Users\User;
use UltimateMember\Users\Usermeta;
use UltimateMember\Collections\Users as UM_Users_Collection;

class UserController
{

    /**
     * Get Single User
     */
    function single( \WP_REST_Request $request   ){
        
        $id = $request->get_param('id');

        $user = User::where("ID", "=", $id )->first();
        
        return $user;
      
    }

    /**
     * Get Multiple User
     */
    function multiple( \WP_REST_Request $request   ){
       
        $id = $request->get_param('keyword');
        $includes = $request->get_param('includes');
        $callback_name = $request->get_param("callback_name");
 
        $users = Usermeta::where("meta_value", "LIKE", $id .'%' )
            ->groupBy("user_id")
            ->limit(20)
            ->get('user_id');

        $arr_includes = ['display_name','avatar','ID'];
        if( $includes ){
            $arr_includes = explode(",", $includes );
        }
        
        $user_collect = new UM_Users_Collection();
        $users = $user_collect->user_related( $arr_includes, $users );
        
        if( $callback_name ){
            $users = [ $callback_name => $users ];
        }

        return $users;
    
    }

   

}
