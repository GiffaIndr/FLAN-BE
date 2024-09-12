<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Activity;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ActivityController extends BaseController
{
    public function activity(){
        $activity = Activity::all();
        return response()->json([
            'message' => 'successfully get all activity',
            'activity' => $activity
        ], 200);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'activity_name' => 'required',
            'done_time' => 'required',
            'description' => 'required'
            ]);
            if ($validator->fails()) {
                Log::info('validation error', $validator->errors()->toArray());
                return response()->json($validator->errors(), 422);
            }
            try{
                $activity = Activity::create([
                    'activity_name' => $request->activity_name,
                    'description' => $request->description,
                    'done_time' => $request->done_time
                ]);
                Log::info('Activity created successfully', $activity->toarry());
                return response()->json([
                    'message' => 'Activity created successfully',
                    'activity' => $activity
                ], 200);
            }catch (\Exception $e){
                Log::error('Error create activity', ['error' => $e->getMessage()]);
                return response()->json(['error' => 'create failed'], 500);
            }
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'activity_name' => 'required',
            'done_time' => 'required',
            'description' => 'required'
            ]);
            if ($validator->fails()) {
                Log::info('validation error', $validator->errors()->toArray());
                return response()->json($validator->errors(), 422);
            }
            try{
                $activity = Activity::where('id', $id)->update([
                    'activity_name' => $request->activity_name,
                    'description' => $request->description,
                    'done_time' => $request->done_time
                ]);
                return response()->json([
                    'message' => 'Activity created successfully',
                    'activity' => $activity
                ], 200);
            }catch (\Exception $e){
                Log::error('Error create activity', ['error' => $e->getMessage()]);
                return response()->json(['error' => 'create failed'], 500);
            }
    }

    public function delete($id){
        $delete = Activity::find($id)->delete();
        return response()->json([
            'message' => 'activiry deleted successfully',
            'delete' => $delete
        ], 200);
    }
}
