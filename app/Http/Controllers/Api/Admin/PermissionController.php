<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Api\BaseController;
use Lang;
use Auth;
use Validator;
use Carbon\Carbon;


class PermissionController extends BaseController
{
    
    protected $response = array();
    public function index(){

        if (! Gate::allows('permission_manage')) {
            return abort(401);
        }
        $this->response = Permission::all();
        return $this->sendResponse([],$this->response,1);

        
    }
    
    public function store(Request $request){

        
        $messages = [
            'name.required' => 'Name Field Required',
        ];

        $validator = Validator::make($request->all(), [
            'name'         => 'required|unique:permissions,name',
        ],$messages);
        if ($validator->fails()) {
           return $this->sendError($validator->errors()->first(), [],0);
        }
        try {
            $permission = Permission::create($request->all());
            //$this->response['permissions'] = Permission::all();
           // $this->response['message'] = "Added Successfully";
            $this->response = $permission;
            return $this->sendResponse([],$this->response,1);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [],0);           
        }
        
    }

    public function update(request $request){
        
        $messages = [
            'name.required' => 'Name Field Required',
            'id.required' => 'Id Field Required',
        ];

        $validator = Validator::make($request->all(), [
            'name'         => 'required|unique:permissions,name,'.$request->id,
            'id'         => 'required',
        ],$messages);
        if ($validator->fails()) {
           return $this->sendError($validator->errors()->first(), [],0);
        }
        
        $permission = Permission::find($request->id);    
        try {
            $permission->update($request->all());
            //$this->response['permissions'] = Permission::all();
            //$this->response['message'] = "Added Successfully";
            $this->response = $permission;
            return $this->sendResponse([],$this->response,1);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [],0);           
        }
        
    }
    
    public function destroy(request $request){
        
        $messages = [            
            'id.required' => 'Id Field Required',
        ];

        $validator = Validator::make($request->all(), [
            'id'         => 'required',
        ],$messages);
        if ($validator->fails()) {
           return $this->sendError($validator->errors()->first(), [],0);
        }

        $permission = Permission::find($request->id);
        try {           
            $permission->delete();
            //$this->response['permissions'] = Permission::all();
            $this->response = "Deleted Successfully";
            return $this->sendResponse([],$this->response,1);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [],0);           
        }        
    }
}
