<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Process\Process;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CronJobController extends Controller
{
   public function start_cron(Request $request){
       $repeat = $request->input('repeat');
 // Stop cron if running
        $this->stop_cron();

        // Check if the cron should stop
        if (Cache::has('stop-invoice-cron')) {
            Cache::forget('invoice-cron-running');
            Cache::forget('stop-invoice-cron');
            return 'Invoice cron job stopped!';
        }

        // Set a flag to indicate that the cron is running
        Cache::put('invoice-cron-enabled', true);
        Cache::put('invoice-cron-repeat', $repeat);

        return response()->json([
            'success' => true,
            'message' => 'Cron started successfully'
        ], Response::HTTP_OK);
        
             
       
   }
   
   public function stop_cron(){
       
        \Artisan::call('optimize:clear');
       
       Cache::put('invoice-cron-enabled', false);
        // clear the cache
   

        return response()->json([
            'success' => true,
            'message' => 'Cron job deleted successfully'
        ]);
   }
   
   public function status_cron()
{
    if (Cache::get('invoice-cron-enabled', false)) {
        return \response()->json([
                'success' => true,
                'message' => array('running'=> Cache::get('invoice-cron-enabled'), 'repeat'=> Cache::get('invoice-cron-repeat'))
            ]
            , Response::HTTP_OK);
    } else {
        return \response()->json([
                'success' => true,
                'message' => array('running'=> Cache::get('invoice-cron-enabled'), 'repeat'=> Cache::get('invoice-cron-repeat'))
            ]
            , Response::HTTP_OK);
    }
}
}
