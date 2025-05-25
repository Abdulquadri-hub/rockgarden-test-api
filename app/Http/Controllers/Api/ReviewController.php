<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreReviewRequest;
use App\Models\Review;
use App\Models\Client;
use App\Models\Employee;
use App\Models\NotificationSettings;
use App\Http\Controllers\Api\TwilioSMSController;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class ReviewController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
     
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'client_id' => 'required',
            'staff_id' => 'required',
            'comment' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);
        
        if($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid review information'
            ], Response::HTTP_BAD_REQUEST);
        }
        
        $staff = Employee::where("id", $request->staff_id)->with(['user'])->first();
        if(empty($staff)){
            return response()->json([
                'success' => false,
                'message' => 'Staff not found'
            ], Response::HTTP_NOT_FOUND);
        }
        
        $client = Client::where("id", $request->client_id)->with(['user'])->first();
        if(empty($client)){
            return response()->json([
                'success' => false,
                'message' => 'Client not found'
            ], Response::HTTP_NOT_FOUND);
        }
        
        $reviewData = [
            'client_id' => $client->id,
            'staff_id' => $staff->id,
            'staff_name' => $staff->user->first_name . " " . $staff->user->last_name,
            'comment' => $request->comment,
            'rating' => $request->rating,
        ];

        $matchThese = [
            'client_id' => $request->client_id,
            'staff_id'=> $request->staff_id 
        ];
        
        // Notify the staff
        // $staff->notify(new NewReviewNotification($review));
        
        $review = Review::updateOrCreate($matchThese, $reviewData);
        
        $averageRating = $review->avg('rating');

        return response()->json([
            'message' => 'Review created successfully',
            'data' => $review,
            'average_rating' => $averageRating,
        ], Response::HTTP_OK);
    }
    
}
