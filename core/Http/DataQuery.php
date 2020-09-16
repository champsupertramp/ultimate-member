<?php 
namespace UltimateMember\Http;

use UltimateMember\Database\Manager as Capsule;

class DataQuery
{
     
    private $ids = [];

    private $results = null;

    public function insert( $data = [] ){
       
        if( isset( $data['main'] ) ){

            /**
             * Database Capsule
             */             
            $capsule = new Capsule();
            $DB = $capsule->query();

            // Primary Table
            $primary_table_name = $data['main']['table'];

            // Primary Columns
            $primary_table_columns = $this->sanitize( $data['main']['columns'] );
           
            $this->ids = $DB::table( $primary_table_name )->insertGetId($primary_table_columns);

            $this->relations( $this->ids,  $data['main']['relations'] );

            return $this->getResults();
        }

    }

    public function getIDs(){

        return $this->ids;
    }

    public function getResults(){

        return $this->results;
    }

    public function relations( $main_id, $dataColumns  ){

        $arr_returns = [];
        if( isset( $dataColumns['post'] ) ){
            // Create post object
            $the_post = array(
                'post_title'    => wp_strip_all_tags( $dataColumns['post']['title'] ),
                'post_status'   => ! isset( $dataColumns['post']['status'] ) ? 'publish': $dataColumns['post']['status'],
                'post_author'   => ! isset( $dataColumns['post']['author'] ) ? get_current_user_id(): $dataColumns['post']['author'],
                'post_type'     => ! isset( $dataColumns['post']['post_type'] ) ? 'post': $dataColumns['post']['post_type'], 
            );
            
            // Insert the post into the database
            $last_insert_id = wp_insert_post( $the_post );

            $arr_returns['post_ids'][ $the_post['post_type'] ]['post_id'] = $last_insert_id;
            $arr_returns['post_ids'][ $the_post['post_type'] ]['permalink'] = get_the_permalink( $last_insert_id );

            if( isset( $dataColumns['post']['metas'] ) ){
                foreach( $dataColumns['post']['metas'] as $meta_key => $meta_value ){

                    // Use previous post object ID
                    if( is_array( $meta_value ) && array_keys( $meta_value )[0] == "main" ){
                        $meta_value = intval( $main_id );
                    }

                    update_post_meta( $last_insert_id, $meta_key, $meta_value );
                }
            }

            if( isset( $dataColumns['post']['category'] ) ){

                if( is_array( $dataColumns['post']['category'] ) ){
                    /*
                    * If this was coming from the database or another source, we would need to make sure
                    * these were integers:
                    */
                    $cat_ids = array_map( 'intval', $dataColumns['post']['category'] );
                    $cat_ids = array_unique( $cat_ids );
                
                    wp_set_post_terms( $last_insert_id, $cat_ids,  $dataColumns['post']['category']['taxonomy'] );
                }else{
                    wp_set_post_terms( $last_insert_id, intval($dataColumns['post']['category']),  $dataColumns['post']['category']['taxonomy'] );
                
                }

            }

            if( isset( $dataColumns['post']['tags'] ) ){

                wp_set_object_terms( $last_insert_id, $dataColumns['post']['tags']['tags'], $dataColumns['post']['tags']['taxonomy'] );
               
            }

            $this->results = $arr_returns;

        }
    }

    public function sanitize( $dataColumns ){

        foreach( $dataColumns as $column => $value ){

            if( is_array( $value ) || is_object( $value ) ){
                $dataColumns[ $column ] = json_encode( $value );
            }

        }
        return $dataColumns;
    }

    

}