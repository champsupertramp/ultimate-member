<?php 
namespace UltimateMember\Database;

use Illuminate\Support\Facades\Schema; 
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;


 class Manager
{
    protected $capsule = null;

    public function __construct(){
        global $wpdb;

        $this->capsule = new Capsule();

        $args = [
            'driver'    => 'mysql',
            'host'      => DB_HOST,
            'database'  => DB_NAME,
            'username'  => DB_USER,
            'password'  => DB_PASSWORD,
            'charset'   => DB_CHARSET,
            'prefix'    => $wpdb->prefix 
         ];

        if( defined("DB_COLLATE") && DB_COLLATE ){
            $args['collate'] = DB_COLLATE;
        }

        
        $this->capsule->addConnection( $args );

        $this->capsule->setEventDispatcher(new Dispatcher(new Container));

        // Make this Eloquent instance available globally via static methods... (optional)
        $this->capsule->setAsGlobal();

        // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
        $this->capsule->bootEloquent();

    }

    public function query(){
        return $this->capsule;
    }


}