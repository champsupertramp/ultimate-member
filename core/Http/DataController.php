<?php 
namespace UltimateMember\Http;
use Illuminate\Http\Request;
use Respect\Validation\Validator as Validator;
use UltimateMember\Http\DataQueryInsert as UM_DataQueryInsert;
use UltimateMember\Http\DataQueryGet as UM_DataQueryGet;
 

class DataController
{

    /**
     * Handle Get Rquest
     */
    public function handleGet( \WP_REST_Request $request ){

        
       $id = $request->get_param('id');
       $data = apply_filters("um_data_query_get__{$id}", json_decode( $request->get_param('data') ), new Validator  );

       return $data;


    }

    /**
     * Handle Post Rquest
     */
    public function handlePost( \WP_REST_Request $request ){
        
        $id = $request->get_param('id');
        $data = apply_filters("um_data_query_post__{$id}", json_decode( $request->get_param('data') ), new Validator );
        
        if( isset( $data['has_errors'] ) && $data['has_errors'] > 0 ){
           // unset( $data['main'] );
            return $data;
        }
 
        $result = new UM_DataQueryInsert;
        
        return $result->insert( $data  );
        
 
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