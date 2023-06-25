<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Ceremony;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CeremonyController extends Controller
{
    function all(Request $request){
        $user_id = $request->input('user_id');

        if($user_id){
            $cer_user = Ceremony::with('user')->where('user_id',$user_id)->first();

            if($cer_user){
                return ResponseFormatter::success(
                    $cer_user,
                    'Data peserta berhasil diambil' 
                );
            }
            else{
                return ResponseFormatter::error(
                    null,
                    'Data tidak ada',
                    404
                );
            }
        }
        $cer_user = Ceremony::with('user');
        return ResponseFormatter::success(
            $cer_user->get(),
            'Data peserta berhasil diambil' 
        );
    }

    function register(Request $request){
        try{
            $request->validate([
                'ss_poster_url'=>'required'
            ]);

            $posterFile = $request->file('ss_poster_url');
            $posterPath = $posterFile->storeAs('public/ceremony/'.str_replace(' ','_',Auth::user()->name), str_replace(' ','_',$posterFile->getClientOriginalName()));

            $id = Auth::id();
            $cer_user = Ceremony::create([
                'user_id' => $id,
                'ss_poster_url' => $posterPath
            ]);
            return ResponseFormatter::success(
                $cer_user,
                'Create Ceremony User successfully'
            );
        }catch(ValidationException $error){
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0], 
            ],
              'Create ceremony user failed',
              500,  
            );
        }
    }

    function userEdit(Request $request){
        try {
            $request->validate([
            'ss_poster_url'=>'required'
            ]);

            $edit = Ceremony::with('user')->where('user_id',Auth::id())->first();

            if (!$edit) {
                return ResponseFormatter::error(
                    null,
                    'Data not found',
                    404
                );
            }
            $posterFile = $request->file('ss_poster_url');
            $posterPath = $posterFile->storeAs('public/poster', 'poster_'.uniqid().'.'.$posterFile->extension());


            $edit->update([
                'ss_poster_url'=>$posterPath
            ]);

            return ResponseFormatter::success(
                $edit,
                'Edit Ceremony User success'
            );
        } catch (ValidationException $error) {
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0],    
            ], 
                'Edit Ceremony User failed', 
                500,
            );
        }
    }

    function adminEdit(Request $request){
        try {
            $request->validate([
            'approve_poster'=>'required |in:REJECTED,WAITING,ACCEPTED'
            ]);

            $edit = Ceremony::with('user')->where('user_id',Auth::id())->first();

            if (!$edit) {
                return ResponseFormatter::error(
                    null,
                    'Data not found',
                    404
                );
            }

            $edit->update([
                'approve_poster'=>$request->approve_poster
            ]);

            return ResponseFormatter::success(
                $edit,
                'Edit Ceremony Admin success'
            );
        } catch (ValidationException $error) {
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0],    
            ], 
                'Edit Ceremony Admin failed', 
                500,
            );
        }
    }
}
