<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    public function get_service(Request $request)
    {
        
        $service_plan = DB::table('services')
                            ->get();
        return response()->json(['success' => true, 'message' => $service_plan]);
    }

    public function get_application_histories(Request $request)
    {
        if(isset($_GET['applicant_id']) && !empty($_GET['applicant_id'])){
            $Id = $_GET['applicant_id'];
            $where = 'applicant_id';
        } 

        if(isset($_GET['client_id']) && !empty($_GET['client_id'])){
            $Id = $_GET['client_id'];
            $where = 'client_id';
        } 

        // if(isset($_GET['id']) && !empty($_GET['id'])){
        //     $Id = $_GET['id'];
        //     $where = 'id';
        // }
       
        if(!$Id) {
            return response()->json([
                'success' => true, 
                'message' => []]);  
        }

        $serviceApplication = DB::table('service_application')->where($where, $Id)->get();
        if(!$serviceApplication) {
            return response()->json([
                'success' => true, 
                'message' => []]);            
        } else {
            foreach($serviceApplication as $row) {
                $services = DB::table('services')->where('plan_id', $row->plan_id)->first();
                $user = DB::table('users')->where('id', $row->client_id)->first();
                $applicant = DB::table('users')->where('id', $row->applicant_id)->first();
        
                $row->plan = $services;
                $row->client = $user;
                $row->applicant = $applicant;
            }
            
            return response()->json(['success' => true, 'message' => $serviceApplication]);
        }
    }
    public function get_application_history(Request $request)
    {
        if(isset($_GET['applicant_id']) && !empty($_GET['applicant_id'])){
            $Id = $_GET['applicant_id'];
            $where = 'applicant_id';
        } 

        if(isset($_GET['client_id']) && !empty($_GET['client_id'])){
            $Id = $_GET['client_id'];
            $where = 'client_id';
        } 

        if(isset($_GET['id']) && !empty($_GET['id'])){
            $Id = $_GET['id'];
            $where = 'id';
        }
       
        if(!$Id) {
            return response()->json([
                'success' => false, 
                'message' => 'This application dose not exist']);  
        }

        $serviceApplication = DB::table('service_application')->where($where, $Id)->first();
        
        if(!$serviceApplication) {
            return response()->json([
                'success' => false, 
                'message' => 'This application does not exist']);            
        } else {
            $services = DB::table('services')->where('plan_id', $serviceApplication->plan_id)->first();
            $user = DB::table('users')->where('id', $serviceApplication->client_id)->first();
            $applicant = DB::table('users')->where('id', $serviceApplication->applicant_id)->first();
    
            $serviceApplication->plan = $services;
            $serviceApplication->client = $user;
            $serviceApplication->applicant = $applicant;
            
            return response()->json(['success' => true, 'message' => $serviceApplication]);
        }
    }

    public function apply_for_service(Request $request)
    {
        $input = $request->all();
        $dt = date('Y-m-d H:i:s');

        $validator = Validator::make($input, [
            'plan_id' => 'required',
            'client_id' => 'required',
            'applicant_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'message' => $validator->errors()->first()
            ], 404);
        }
        $input['created_at'] = $dt;
        DB::table('service_application')->insert($input);
        
        return response()->json(['success' => true, 'message' => "Your application was successful!"]);
    }
}