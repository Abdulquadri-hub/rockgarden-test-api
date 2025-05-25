<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FAQ;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use function response;

class FAQController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        return \response()->json([
                'success' => true,
                'message' => FAQ::orderBy('question','ASC')->get()
            ]
            , Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource paged
     *
     * @return JsonResponse
     */
    public function indexPaged(Request $request)
    {
        return \response()->json([
                'success' => true,
                'message' => FAQ::orderBy('question','ASC')->paginate((int) $request->get('limit'))
            ]
            , Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {

        try {
            $validator = Validator::make($request->all(),[
                'question' => 'required|string',
                'answer' => 'required|string',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid FAQ question or answer.'
                ], 400);
            }
            // Create new FAQ
            $faq = new FAQ();
            $faq->question = $request->get('question');
            $faq->answer = $request->get('answer');
            $faq->save();
            return response()->json([
                'success' => true,
                'message' => $faq->id
            ], Response::HTTP_OK);
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ], Response::HTTP_CONFLICT
            );
        }
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request)
    {
        $FAQ = FAQ::where('id', $request->get('id'))->first();
        return \response()->json([
                'success' => true,
                'message' => $FAQ
            ]
            ,
            Response::HTTP_OK);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        try {
            // validate request data
            $validator = Validator::make($request->all(),[
                'id' => 'required|integer',
                'question' => 'required|string',
                'answer' => 'required|string',
            ]);

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => "Invalid FAQ update information's."
                ], Response::HTTP_BAD_REQUEST);
            }

            $faq = FAQ::where('id', $request->get('id'))->firstOrFail();
            if(empty($faq)){
                return response()->json([
                    'success' => false,
                    'message' => 'FAQ not found'
                ], Response::HTTP_NOT_FOUND);
            }
            // Replace existing name
            $faq->question = $request->get('question');
            $faq->answer = $request->get('answer');
            $faq->save();
            $faq->save();

            return response()->json([
                'success' => true,
                'message' => $faq
            ], Response::HTTP_OK);
        }catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => $e->getMessage()
                ], Response::HTTP_CONFLICT
            );
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id' => 'required|integer'
        ]);

        if($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'id not found'
            ], Response::HTTP_BAD_REQUEST);
        }

        FAQ::where('id', $request->get('id'))->delete();

        return response()->json([
            'success' => true,
            'message' => "FAQ successfully deleted"
        ], Response::HTTP_OK);
    }
}
