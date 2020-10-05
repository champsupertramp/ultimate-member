<?php 
namespace UltimateMember\Http;

use UltimateMember\Database\Manager as Capsule;

class DataQueryInsert
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
    public function insert( $data = [] ){
       
        if( isset( $data['main'] ) ){

            /**
             * Database Capsule
             */             
            $capsule = new Capsule();
            $this->DB = $capsule->query();

            // Primary Table
            $primary_table_name = $data['main']['table'];

            // Primary Columns
            $primary_table_columns = $this->sanitize( $data['main']['columns'] );
           
            // Insert Primary
            $this->ids = $this->DB::table( $primary_table_name )->insertGetId($primary_table_columns);

            // Return a promise data
            if( isset( $data['main']['promise'] ) ){
                if( ! empty( $this->ids ) ){
                    $this->results[ ] = ['promise' => $data['main']['promise']['success'] ];
                }else{
                    $this->results[ ] = ['promise' => $data['main']['promise']['error'] ];
                }
            }

            // Insert Related Columns/Data
            $this->relations( $this->ids,  $data['main'] );

            return $this->getResults();
        }

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
     * Relations
     */
    public function relations( $primary_id, $dataColumns  ){

        if( ! isset(  $dataColumns['relations'] ) ) return null;

        $arr_returns = [];
        $relation_ids = [];

        foreach( $dataColumns['relations'] as $key => $relation ){

            if( 'post' == $key ){

                // Create post object
                $the_post = array(
                    'post_title'    => wp_strip_all_tags( $relation['title'] ),
                    'post_status'   => ! isset( $relation['status'] ) ? 'publish': $relation['status'],
                    'post_author'   => ! isset( $relation['author'] ) ? get_current_user_id(): $relation['author'],
                    'post_type'     => ! isset( $relation['post_type'] ) ? 'post': $relation['post_type'], 
                );
                
                // Insert the post into the database
                $last_insert_id = wp_insert_post( $the_post );

                $arr_returns['post_ids'][ $the_post['post_type'] ]['post_id'] = $last_insert_id;
                $arr_returns['post_ids'][ $the_post['post_type'] ]['permalink'] = get_the_permalink( $last_insert_id );

                if( isset( $relation['metas'] ) ){
                    foreach( $relation['metas'] as $meta_key => $meta_value ){

                        // Use previous post object ID
                        if( is_array( $meta_value ) && array_keys( $meta_value )[0] == "main" ){
                            $meta_value = intval( $primary_id );
                        }

                        update_post_meta( $last_insert_id, $meta_key, $meta_value );
                    }
                }

                if( isset( $relation['category'] ) ){

                    if( is_array( $relation['category'] ) ){
                        /*
                        * If this was coming from the database or another source, we would need to make sure
                        * these were integers:
                        */
                        $cat_ids = array_map( 'intval', $relation['category'] );
                        $cat_ids = array_unique( $cat_ids );
                    
                        wp_set_post_terms( $last_insert_id, $cat_ids,  $relation['category']['taxonomy'] );
                    }else{
                        wp_set_post_terms( $last_insert_id, intval($relation['category']),  $relation['category']['taxonomy'] );
                    
                    }

                }

                if( isset( $relation['tags'] ) ){

                    wp_set_object_terms( $last_insert_id, $relation['tags']['tags'], $relation['tags']['taxonomy'] );
                
                }

                $this->results = $arr_returns;

            }else{
                
                if( isset( $relation['multi_columns'] ) ){
                   
                    foreach( $relation['multi_columns'] as $k => $v ){
                            
                            $arr_multi_columns = [];

                            foreach(  $v as $v_id  ){
                            
                                $arr_multi_columns[ $k ] = $v_id;
                                
                                foreach( $relation['columns'] as $kk => $v ){
                                    $arr_multi_columns[ $kk ] = $v;
                                }

                                foreach( $relation['inverse'] as $kk => $v ){
                                    if( $kk == 'event_id' ){
                                        $v = $primary_id;
                                    }
                                    $arr_multi_columns[ $kk ] = $v;
                                }
                                $this->DB::table( $relation['table'] )->insert( $arr_multi_columns );
                            }

                    }
 

                }else{
                    $id = $this->DB::table( $relation['table'] )->insertGetId( $relation['columns'] );
                    
                    $relation_ids[ $key ] = $id;
                
                    if( isset( $relation['inverse'] ) ){
                        foreach( $relation['inverse'] as $k => $v ){
                            if( $k == 'event_id' ){
                                $v = $primary_id;
                            }
                            $this->DB::table( $relation['table'] )->where( 'id', $id )->update( [ $k => $v ] );
                        }
                    }

                }

                
            }

             $this->results = array_merge(  ['success' => true], $this->results , $relation_ids );
           

        }

        if( ! empty( $relation_ids ) ){
          $this->updatePrimaryRelationship( $primary_id, $relation_ids, $dataColumns );
        }
    }

    /**
     * Update relationship
     */
    public function updatePrimaryRelationship( $primary_id, $relation_ids, $dataColumns ){

        $table = $dataColumns['table'];

        foreach( $relation_ids as $column_key => $values ){
            $this->DB::table( $table )->where( 'id', $primary_id )->update( [ $column_key => $values ] );
        }
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