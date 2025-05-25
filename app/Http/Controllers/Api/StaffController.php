<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function response;

class StaffController extends Controller
{
    // public function get_staff_assignments(Request $request)
    // {
    //     $token= $request->bearerToken();
    //     $service_plan = DB::table('services')
    //                         ->get();
    //     return response()->json(['success' => true, 'message' => $service_plan]);
    // }

    public function get_staff_assignments(Request $request)
    {
        if(isset($_GET['application_id']) && !empty($_GET['application_id'])){
            $Id = $_GET['application_id'];
            $where = 'application_id';
        }

        if(isset($_GET['staff_id']) && !empty($_GET['staff_id'])){
            $Id = $_GET['staff_id'];
            $where = 'staff_id';
        }

        if(!$Id) {
            return response()->json([
                'success' => false,
                'message' => 'This staff dose not exist']);
        }

        $staffAssignments = DB::table('staff_assignments')->where($where, $Id)->get();
        if(!$staffAssignments) {
            return response()->json([
                'success' => false,
                'message' => 'This application dose not exist']);
        } else {
            foreach($staffAssignments as $row) {

                $serviceApplication = DB::table('service_application')->where('id', $row->service_application_id)->first();
                $staffUser = DB::table('users')->where('id', $row->staff_id)->first();
                $user = DB::table('users')->where('id', $serviceApplication->client_id)->first();
                $row->service_application = $serviceApplication;
                $row->staff = $staffUser;
                $row->client = $user;
            }

            return response()->json(['success' => true, 'message' => $staffAssignments]);
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
                'message' => 'This application dose not exist']);
        } else {
            $services = DB::table('services')->where('plan_id', $serviceApplication->plan_id)->first();
            $user = DB::table('users')->where('id', $serviceApplication->client_id)->first();

            $serviceApplication->plan = $services;
            $serviceApplication->client = $user;

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
                'message' => 'Required field cannot be blank.'
            ], 404);
        }
        $input['created_at'] = $dt;
        DB::table('service_application')->insert($input);

        return response()->json(['success' => true, 'message' => "Your submistion successful!"]);
    }
}
