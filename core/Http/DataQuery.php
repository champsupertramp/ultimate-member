<?php 
namespace UltimateMember\Http;

use UltimateMember\Database\Manager as Capsule;

class DataQuery
{
     
    private $ids = [];

    private $results = null;

    private $DB = null;

    /**
     * Get DB query connection
     */
    public function init(){
        /**
         * Database Capsule
         */             
        $capsule = new Capsule();
        return $capsule->query();

        

    }
}