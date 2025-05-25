<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlaggedTask;
use App\Models\Employee;
use Carbon\Carbon;
use Auth;

class FlaggedTaskController extends Controller
{
    public function index(Request $request)
    {
        
        try {

        $query = FlaggedTask::with(['staff.user', 'resolver']);
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('task_type')) {
            $query->where('task_type', $request->task_type);
        }
        
        if ($request->has('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        }
        
        $sortField = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);
        
        $flaggedTasks = $query->paginate(15);
        
        return response()->json([
            'success' => true,
            'data' => $flaggedTasks,
            'message' => 'Flagged tasks retrieved successfully'
        ]);

        } catch (\Exception $e) {
             return response()->json([
            'success' => false,
            'message' => 'Flagged tasks retrieved failed:'. $e->getMessage()
            ]); 
        }
    }
    
    public function show(Request $request)
    {
        try{
            $flaggedTask =  FlaggedTask::find($request->id);
            if(!$flaggedTask){
              return response()->json([
                'success' => false,
                'message' => 'Flagged task not found'
              ]);    
            }
            
            return response()->json([
                'success' => true,
               'data' => $flaggedTask->load(['staff.user', 'resolver']),
               'message' => 'Flagged task retrieved successfully'
            ]);
        }catch(\Exception $e){
             return response()->json([
               'success' => false,
               'message' => 'Flagged tasks retrieved failed:'. $e->getMessage()
            ]);         
        }

    }

    public function resolve(Request $request, FlaggedTask $flaggedTask)
    {
        
        $request->validate([
            'resolution_notes' => 'nullable|string|max:1000',
        ]);
        
        $flaggedTask->status = 'resolved';
        $flaggedTask->resolved_at = Carbon::now();
        $flaggedTask->resolved_by = Auth::id();
        $flaggedTask->description .= "\n\nResolution notes: " . ($request->resolution_notes ?? 'Task resolved by manager');
        $flaggedTask->save();
        
        return response()->json([
            'data' => $flaggedTask,
            'message' => 'Task has been marked as resolved'
        ]);
    }
    
    public function stats()
    {
        
        $stats = [
            'pending_count' => FlaggedTask::where('status', 'pending')->count(),
            'resolved_today' => FlaggedTask::where('status', 'resolved')
                                         ->whereDate('resolved_at', Carbon::today())
                                         ->count(),
            'created_today' => FlaggedTask::whereDate('created_at', Carbon::today())->count(),
            'chart_issues' => FlaggedTask::where('task_type', 'charting')
                                        ->where('status', 'pending')
                                        ->count(),
            'attendance_issues' => FlaggedTask::where('task_type', 'attendance')
                                             ->where('status', 'pending')
                                             ->count(),
        ];
        
        return response()->json($stats);
    }
    
    public function getStaff()
    {
        $staff = Employee::all();
        
        return response()->json([
            'data' => $staff,
            'message' => 'Staff list retrieved successfully'
        ]);
    }
}