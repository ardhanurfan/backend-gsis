<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\BccUser;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BccController extends Controller
{
    function all(Request $request) {
        $user_id = $request->input('user_id');
        
        if ($user_id){
            $bcc_user = BccUser::with('user')->where('user_id',$user_id)->first();
            if ($bcc_user) {
                return ResponseFormatter::success(
                    $bcc_user,
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
        $bcc_user = BccUser::with('user');
        return ResponseFormatter::success(
            $bcc_user->get(),
            'Data peserta berhasil diambil' 
        );
    }

    function register(Request $request) {
        try {
            $request->validate([
            'stream'=>'required|in:ART,BUSINESS,TECHNOLOGY',
            'ktm_url'=>'required',
            'ss_follow_url'=>'required',
            'ss_poster_url'=>'required',
            'payment_url'=>'required',
        ]);

        $id = Auth::id();

        $bcc_user = BccUser::create([
            'user_id'=> $id,
            'team_id'=>$request->team_id,
            'papper_url'=>$request->papper_url,
            'stream'=>$request->stream,
            'ktm_url'=>$request->ktm_url,
            'ss_follow_url'=>$request->ss_follow_url,
            'ss_poster_url'=>$request->ss_poster_url,
            'payment_url'=>$request->payment_url,
            'approve_ktm'=>$request->approve_ktm,
            'approve_follow'=>$request->approve_follow,
            'approve_poster'=>$request->approve_poster,
            'approve_payment'=>$request->approve_payment,
        ]);
        return ResponseFormatter::success(
                $bcc_user,
                'Create Bcc User successfully'
            );
        } catch (ValidationException $error) {
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0],    
            ], 
                'Create Bcc User failed', 
                500,
            );
        }
    }

    function editFromUser(Request $request) {
        try {
            $request->validate([
            'ktm_url'=>'required',
            'ss_follow_url'=>'required',
            'ss_poster_url'=>'required',
            'payment_url'=>'required',
        ]);

        $edit = BccUser::with('user')->where('user_id',Auth::user()->id)->first();

        if (!$edit) {
            return ResponseFormatter::error(
                null,
                'Data not found',
                404
            );
        }

        $edit->update([
            'ktm_url'=>$request->ktm_url,
            'ss_follow_url'=>$request->ss_follow_url,
            'ss_poster_url'=>$request->ss_poster_url,
            'payment_url'=>$request->payment_url,
        ]);

        return ResponseFormatter::success(
            $edit,
            'Edit Bcc User success'
        );

        } catch (ValidationException $error) {
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0],    
            ], 
                'Edit Bcc User failed', 
                500,
            );
        }
    }

    function editFromAdmin(Request $request) {
        try {
            $request->validate([
            'status'=>'required',
            'approve_ktm'=>'required',
            'approve_follow'=>'required',
            'approve_poster'=>'required',
            'approve_payment'=>'required',
        ]);

        $edit = BccUser::with('user')->where('user_id',Auth::user()->id)->first();

        if (!$edit) {
            return ResponseFormatter::error(
                null,
                'Data not found',
                404
            );
        }

        $edit->update([
            'status'=>$request->status,
            'approve_ktm'=>$request->approve_ktm,
            'approve_follow'=>$request->approve_follow,
            'approve_poster'=>$request->approve_poster,
            'approve_payment'=>$request->approve_payment,
        ]);

        return ResponseFormatter::success(
            $edit,
            'Edit Bcc User success'
        );

        } catch (ValidationException $error) {
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0],    
            ], 
                'Edit Bcc User failed', 
                500,
            );
        }
    }

    function createTeam() {
        
    }
}
