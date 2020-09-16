<?php 
namespace UltimateMember\Http;
use Illuminate\Http\Request;
use Respect\Validation\Validator as Validator;
use UltimateMember\Http\DataQuery as UM_DataQuery;
 

class DataController
{

    /**
     * Handle Get Rquest
     */
    public function handleGet( \WP_REST_Request $request ){

        
       $id = $request->get_param('id');
       $data = apply_filters("um_data_query_get__{$id}", json_decode( $request->get_param('data') ), new Validator  );

       if( isset( $data['has_errors'] ) && $data['has_errors'] > 0 ){
           return $data;
       }

       $result = new UM_DataQuery( );
       $result->insert( $data  );
       

       return $result->getResults();


    }

    /**
     * Handle Post Rquest
     */
    public function handlePost( \WP_REST_Request $request ){
        
        $id = $request->get_param('id');
        $data = apply_filters("um_data_query_post__{$id}", json_decode( $request->get_param('data') ), new Validator );
        
        if( isset( $data['has_errors'] ) && $data['has_errors'] > 0 ){
            return $data;
        }
 
        $result = new UM_DataQuery;
        return $result->insert( $data  );
        
 
        return $result->getIDs();

 
    }

    /**
     * Handle Put Rquest
     */
    public function handlePut( \WP_REST_Request $request ){

        $id = $request->get_param('id');
        $data = apply_filters("um_data_query_put__{$id}", json_decode( $request->get_param('data') ), new Validator  );

        if( isset( $data['has_errors'] ) ){
            return $data;
        }

        return $data;


    }

    /**
     * Handle Patch Rquest
     */
    public function handlePatch( \WP_REST_Request $request ){

        $id = $request->get_param('id');
        $data = apply_filters("um_data_query_patch__{$id}", json_decode( $request->get_param('data') ), new Validator  );

        if( isset( $data['has_errors'] ) ){
            return $data;
        }

        return $data;


    }

    /**
     * Handle Delete Rquest
     */
    public function handleDelete( \WP_REST_Request $request ){

        $id = $request->get_param('id');
        $data = apply_filters("um_data_query_delete__{$id}", json_decode( $request->get_param('data') ), new Validator  );

        if( isset( $data['has_errors'] ) ){
            return $data;
        }
        
        return $data;


    }

}