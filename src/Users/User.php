<?php 
namespace UltimateMember\Users;

class User extends \Illuminate\Database\Eloquent\Model {


    protected $primaryKey = 'ID';

   /**
     * Get the comments for the blog post.
     */
    public function user_meta()
    {
        return $this->hasMany('UltimateMember\Users\Usermeta', 'user_id','ID');
    }
}