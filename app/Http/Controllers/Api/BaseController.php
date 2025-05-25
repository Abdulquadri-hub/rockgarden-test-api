<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;

class BaseController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($result, $message = "Success", $code = 200,$custom_response = array(),$token = '',$extra_data = array(),$extra_key = array())
    {   
        
        $data = $result;
        $check = (array)$result;

        if(isset($check['current_page']) && $check['current_page']) {
            
            unset($result['first_page_url']);
            unset($result['last_page_url']);
            unset($result['links']);
            unset($result['next_page_url']);
            unset($result['path']);
            unset($result['prev_page_url']);

            // $extra_data
            $data = array(
                'records' => ($custom_response) ? $custom_response : $result['data'],
                'page' => $result['current_page'],
                'page_size' => $result['per_page'],
                'skip_records' => ($result['from']) ? $result['from']-1 : $result['total'],
                'total_records' => $result['total'],
                'max_page_num' => $result['last_page'],
                'is_have_more_records' => ($result['current_page'] < $result['last_page']) ? 'Y' : 'N',
            );

            $paginate_keys = array('current_page'=>'','data'=>'','from'=>'','last_page'=>'','per_page'=>'','to'=>'','total'=>'');
            $array_diff_keys = array_diff_key($result,$paginate_keys);
            foreach($array_diff_keys as $key => $value) {
                $data[$key] = $value;
            }
            // dd($data);
            if($extra_data) {
                $data = array_merge($extra_data,$data);
            }
        }

        
        if(empty($data)){
            $data = null;
        }
        
    	$response = [
            'status'    => $code,
            'success'   => true,
            'data'      => $data,
            'message'   => $message,
        ];
        
        if($token) {
            $response['token'] = $token;
        }
        if($extra_key) {
            $extra_array = array_keys($extra_key);
            foreach($extra_array as $key => $value) {
                $response[$value] = $extra_key[$value];
            }
        }

        return response()->json($response, 200);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error, $errorMessages = [], $code = 0)
    {   
        if(empty($errorMessages)){
            $errorMessages = null;
        }
    	$response = [
            'status'    => $code,
            'success'   => false,
            'data'      => $errorMessages,
            'message'   => $error,
        ];

        return response()->json($response, 200);
    }

    function get_address($latitude,$longitude)
    {

        $endpoint = "https://maps.googleapis.com/maps/api/geocode/json";
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $endpoint, ['query' => [
            'latlng' => "$latitude,$longitude", 
            'key' => "J6a4JYkB0JpYbJOa4bXqugdzzknhRw",
        ]]);

        // dd(json_decode($response->getBody(), true));
        $output = json_decode($response->getBody(), true);

        return $output;
    }
}