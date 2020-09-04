<?php 
namespace UltimateMember\Http;

use UltimateMember\Users\User;
use UltimateMember\Users\Usermeta;

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

      $users = Usermeta::where("meta_value", "LIKE", $id .'%' )
            ->groupBy("user_id")
            ->get('user_id');

           

        return $users;
    
    }

}
