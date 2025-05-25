<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Arr;
use Illuminate\Auth\AuthenticationException;
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function unauthenticated($request, AuthenticationException $exception){
       // if ($request->expectsJson()) {
           // $response = [
           //     'message' => $exception->getMessage()
           // ];
           $response = [
               'success'    => false,
               'message'   => "Session Expired. Please Login to continue.",
           ];
           return response()->json($response, 401);
       // }
        /*$guard = Arr::get($exception->guards(),0);
        $paths = explode('/',$request->getPathInfo());
        switch ($guard) {
            default:
                if(array_search("admin",$paths)){
                    $login = '';
                }else{
                    $login = 'home';
                }              
                break;
        }
       return redirect()->guest(route($login));*/
    }
}
