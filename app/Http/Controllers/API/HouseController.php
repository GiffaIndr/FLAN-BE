<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Http\Resources\HouseResource;
use Illuminate\Support\Facades\Validator;
use App\Models\House;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
class HouseController extends BaseController
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function store(Request $request){
        // dd($request->all());
        // Log::info('store method reached');
        $validator = Validator::make($request->all(), [
            'name' => 'required',
             'family_name' => 'required',
            'description' => 'required',
            'address' => 'required',
        ]);

        if($validator->fails()) {
            Log::info('Validation failed', $validator->errors()->toArray());
            return response()->json($validator->errors(), 422);
        }
        try{
            $house = House::create([
                'name' => $request->name,
                'family_name' => $request->family_name,
                'description' => $request->description,
                'address' => $request->address
            ]);
            Log::info('House created', $house->toArray());
            return response()->json([
                'message' => 'house created successfully',
                'house' => $house
            ]);  
        }catch (\Exception $e) {
            Log::error('Error in created hose', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'create  failed'], 500); 
        }
        }

        public function getHouse(){
            $house = House::all();
            return response()->json($house, 200);
        }

        public function update(Request $request, $id){
            // dd($request->all());
        // Log::info('store method reached');
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'family_name' => 'required',
            'description' => 'required',
            'address' => 'required',
        ]);

        if($validator->fails()) {
            Log::info('Validation failed', $validator->errors()->toArray());
            return response()->json($validator->errors(), 422);
        }
        try{
            $house = House::where('id', $id)->update([
                'name' => $request->name,
                'family_name' => $request->family_name,
                'description' => $request->description,
                'address' => $request->address
            ]);
            // Log::info('House created', $house->toArray());
            return response()->json([
                'message' => 'house update successfully',
                'house' => $house
            ]);  
        }catch (\Exception $e) {
            Log::error('Error in update house', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'create  failed'], 500); 
        } 
        }

        public function delete($id){
            $delete = House::find($id)->delete();
            return response()->json([
                'message' => 'house deleted successfully',
                'delete' => $delete
                ]);
        }
    }
    