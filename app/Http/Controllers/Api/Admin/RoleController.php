<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Api\BaseController;
use Lang;
use Auth;
use Validator;
use Carbon\Carbon;


class RoleController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $response = array();
    public function index()
    {
//        if (! Gate::allows('role_manage')) {
//            return abort(401);
//        }
        $this->response = Role::with('permissions')->get();
        return $this->sendResponse([],$this->response,1);
    }


    public function edit(Request $request){
       // dd($request->id);
        $this->response['permissions'] = Permission::get()->pluck('name', 'name');
        $this->response['role']= Role::with('permissions')->find($request->id);

        return $this->sendResponse([],$this->response,1);
    }


    public function store(Request $request){

        $messages = [
            'name.required' => 'Name Field Required',
        ];

        $validator = Validator::make($request->all(), [
            'name'         => 'required',
        ],$messages);
        if ($validator->fails()) {
           return $this->sendError($validator->errors()->first(), [],0);
        }

        $permissions = $request->input('permission') ? $request->input('permission') : [];
        if(!empty($permissions)){
            foreach ($permissions as $key => $permission) {
               $permissions_ = Permission::where('name',$permission)->first();
               if(empty($permissions_))
                return $this->sendError(" permission ".$permission." not found.", [],0);
            }
        }


        try {

           $role = Role::create($request->except('permission'));
           $permissions = $request->input('permission') ? $request->input('permission') : [];

           $role->givePermissionTo($permissions);
           //$this->response['role'] = Role::with('permissions')->where('id',$role->id)->first();
           $this->response = Role::with('permissions')->where('id',$role->id)->first();
           return $this->sendResponse([],$this->response,1);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [],0);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role){
//        if (! Gate::allows('role_manage')) {
//            return abort(401);
//        }

        $role->load('permissions');

        return view('admin.roles.show', compact('role'));
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $messages = [
            'name.required' => 'Name Field Required',
            'id.required' => 'Id Field Required',
        ];

        $validator = Validator::make($request->all(), [
            'name'         => 'required',
            'id'         => 'required',
        ],$messages);
        if ($validator->fails()) {
           return $this->sendError($validator->errors()->first(), [],0);
        }

        $permissions = $request->input('permission') ? $request->input('permission') : [];
        if(!empty($permissions)){
            foreach ($permissions as $key => $permission) {
               $permissions_ = Permission::where('name',$permission)->first();
               if(empty($permissions_))
                return $this->sendError(" permission ".$permission." not found.", [],0);
            }
        }
        $role = Role::find($request->id);
        try {
            $role->update($request->except('permission'));
            $permissions = $request->input('permission') ? $request->input('permission') : [];
            $role->syncPermissions($permissions);

            //$this->response['role'] = Role::with('permissions')->where('id',$role->id)->first();
            //$this->response['message'] = "Added Successfully";
            $this->response= Role::with('permissions')->where('id',$role->id)->first();
            return $this->sendResponse([],$this->response,1);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [],0);
        }



        return redirect()->route('admin.roles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {

        if (! Gate::allows('role_manage')) {
            return abort(401);
        }

       $messages = [
           'id.required' => 'Id Field Required',
       ];

       $validator = Validator::make($request->all(), [
           'id'         => 'required',
       ],$messages);
       if ($validator->fails()) {
          return $this->sendError($validator->errors()->first(), [],0);
       }


       try {
            $role = Role::findOrFail($request->id);
           $role->delete();
           $this->response = "Deleted Successfully";
           //$this->response['roles'] = Role::with('permission')->get();
           return $this->sendResponse([],$this->response,1);
       } catch (\Exception $e) {
           return $this->sendError($e->getMessage(), [],0);
       }
    }
}
