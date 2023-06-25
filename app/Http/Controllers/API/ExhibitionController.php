<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Exhibition;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ExhibitionController extends Controller
{
    function all(Request $request) {
        $user_id = $request->input('user_id');
        
        if ($user_id){
            $exhibition_user = Exhibition::with('user')->where('user_id',$user_id)->first();
            if ($exhibition_user) {
                return ResponseFormatter::success(
                    $exhibition_user,
                    'Data peserta berhasil diambil' 
                );
            }else{
                return ResponseFormatter::error(
                    null,
                    'Data peserta tidak ada',
                    404
                );
            }
        }
        $exhibition_user = Exhibition::with('user');
        return ResponseFormatter::success(
            $exhibition_user->get(),
            'Data peserta berhasil diambil' 
        );
    } 

    function register(Request $request) {
        try {
            $request->validate([              
                'category' => ['required', 'string'],                
                'description' => ['required', 'string'],                
                'year' => ['required', 'string'],                
                'width' => ['required', 'string'],
                'height' => ['required', 'string'],                
            ]);

        
        $id = Auth::id();

        $exhibition_user = Exhibition::create([
            'user_id' => $id,
            'category' => $request->category,
            'description' => $request->description,
            'year' => $request->year,
            'size' => $request->width.' x '.$request->height,
            'instagram' => $request->instagram,
            'twitter' => $request->twitter,
            'youtube' => $request->youtube,
        ]);
        return ResponseFormatter::success(
                $exhibition_user,
                'Create Exhibition User successfully'
            );
        } catch (ValidationException $error) {
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0],    
            ], 
                'Create Exhibition User failed', 
                500,
            );
        }
    }

    function editFromUser(Request $request){
        try{

            $request->validate([              
                'category' => ['required', 'string'],                
                'description' => ['required', 'string'],                
                'year' => ['required', 'string'],                
                'width' => ['required', 'string'],
                'height' => ['required', 'string'],                
            ]);

            $id = Auth::id();

            $edit = Exhibition::with('user')->where('user_id',$id)->first();

            if (!$edit) {
                return ResponseFormatter::error(
                    null,
                    'Data not found',
                    404
                );
            }

            $edit->update([
                'category' => $request->category,
                'description' => $request->description,
                'year' => $request->year,
                'size' => $request->width.' x '.$request->height,
                'instagram' => $request->instagram,
                'twitter' => $request->twitter,
                'youtube' => $request->youtube,
            ]);

            return ResponseFormatter::success(
                $edit,
                'Edit Bcc User success'
            );

        }catch(ValidationException $error){
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0],    
            ], 
                'Edit Exhibition User failed', 
                500,
            );
        }
    }
    //
}
