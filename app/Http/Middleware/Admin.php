<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Auth;


use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /*if( Auth::check() ){
            if ( Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Sub Admin') ) {
                return $next($request);
            }else if ( Auth::user()->hasRole('user')  ) {
                return redirect(route('home'));
            }
        }*/

        if( Auth::check() ){
            // if user is not admin take him to his dashboard

            $appRoles = ['Registered', 'Client', 'Care Giver',  'Nurse',  'Nurse Assistant', 'Physiotherapist', 'Doctor'];

            $roles = Auth::user()->getRoleNames();

            $isAdmin = false;
            foreach ($roles as $role){
                if (!in_array($role, $appRoles)){
                    $isAdmin =  true;
                    break;
                }
            }

            if ($isAdmin && !empty($roles)) {
                return $next($request);
            }
            else{
                return response()->json(
                    [
                        'success' => false,
                        'message' => "Unauthorized credentials"
                    ], Response::HTTP_UNAUTHORIZED);
            }
        }

        abort(404);  // for other user throw 404 error
    }
}
