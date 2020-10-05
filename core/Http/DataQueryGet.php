<?php 
namespace UltimateMember\Http;

use UltimateMember\Database\Manager as Capsule;

class DataQueryGet
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

    /**
     * Insert Query
     */
    public function query( $data = [] ){
       
    }

    /**
     * Get returned IDs from query
     */
    public function getIDs(){

        return $this->ids;
    }

    /**
     * Get Results from Query
     */
    public function getResults(){

        return $this->results;
    }


    /**
     * Sanitize and Format columns and values
     */
    public function sanitize( $dataColumns ){

        foreach( $dataColumns as $column => $value ){

            if( is_array( $value ) || is_object( $value ) ){
                $dataColumns[ $column ] = json_encode( $value );
            }

        }
        return $dataColumns;
    }

    

}