<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\BccSubmission;
use App\Models\BccTeam;
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
        $name = Auth::user()->name;

        $ktm_url = $request->file('ktm_url');
        $ktm_path = $ktm_url->storeAs('public/bcc/'.str_replace(' ','_',$name), str_replace(' ','_',$ktm_url->getClientOriginalName()));
        
        $ss_follow_url = $request->file('ss_follow_url');
        $ss_follow_path = $ss_follow_url->storeAs('public/bcc/'.str_replace(' ','_',$name), str_replace(' ','_',$ss_follow_url->getClientOriginalName()));
        
        $ss_poster_url = $request->file('ss_poster_url');
        $ss_poster_path = $ss_poster_url->storeAs('public/bcc/'.str_replace(' ','_',$name), str_replace(' ','_',$ss_poster_url->getClientOriginalName()));
        
        $payment_url = $request->file('payment_url');
        $payment_path = $payment_url->storeAs('public/bcc/'.str_replace(' ','_',$name), str_replace(' ','_',$payment_url->getClientOriginalName()));
        

        $bcc_user = BccUser::create([
            'user_id'=> $id,
            'team_id'=>$request->team_id,
            'papper_url'=>$request->papper_url,
            'stream'=>$request->stream,
            'ktm_url'=>$ktm_path,
            'ss_follow_url'=>$ss_follow_path,
            'ss_poster_url'=>$ss_poster_path,
            'payment_url'=>$payment_path,
        ]);

        $get = config('app.url').Storage::url($ktm_path);
        $bcc_user->ktm_url = $get;
        $get = config('app.url').Storage::url($ss_follow_path);
        $bcc_user->ss_follow_url = $get;
        $get = config('app.url').Storage::url($ss_poster_path);
        $bcc_user->ss_poster_url = $get;
        $get = config('app.url').Storage::url($payment_path);
        $bcc_user->payment_url = $get;

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

        $name = Auth::user()->name;

        $ktm_url = $request->file('ktm_url');
        $ktm_path = $ktm_url->storeAs('public/bcc/'.str_replace(' ','_',$name), str_replace(' ','_',$ktm_url->getClientOriginalName()));
        
        $ss_follow_url = $request->file('ss_follow_url');
        $ss_follow_path = $ss_follow_url->storeAs('public/bcc/'.str_replace(' ','_',$name), str_replace(' ','_',$ss_follow_url->getClientOriginalName()));
        
        $ss_poster_url = $request->file('ss_poster_url');
        $ss_poster_path = $ss_poster_url->storeAs('public/bcc/'.str_replace(' ','_',$name), str_replace(' ','_',$ss_poster_url->getClientOriginalName()));
        
        $payment_url = $request->file('payment_url');
        $payment_path = $payment_url->storeAs('public/bcc/'.str_replace(' ','_',$name), str_replace(' ','_',$payment_url->getClientOriginalName()));
        

        $edit = BccUser::with('user')->where('user_id',Auth::user()->id)->first();

        if (!$edit) {
            return ResponseFormatter::error(
                null,
                'Data not found',
                404
            );
        }

        $edit->update([
            'ktm_url'=>$ktm_path,
            'ss_follow_url'=>$ss_follow_path,
            'ss_poster_url'=>$ss_poster_path,
            'payment_url'=>$payment_path,
        ]);

        $get = config('app.url').Storage::url($ktm_path);
        $edit->ktm_url = $get;
        $get = config('app.url').Storage::url($ss_follow_path);
        $edit->ss_follow_url = $get;
        $get = config('app.url').Storage::url($ss_poster_path);
        $edit->ss_poster_url = $get;
        $get = config('app.url').Storage::url($payment_path);
        $edit->payment_url = $get;
        
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
            'user_id'=>'required',
            'status'=>'required|in:ACTIVE,INACTIVE',
            'approve_ktm'=>'required|in:WAITING,REJECTED,ACCEPTED',
            'approve_follow'=>'required|in:WAITING,REJECTED,ACCEPTED',
            'approve_poster'=>'required|in:WAITING,REJECTED,ACCEPTED',
            'approve_payment'=>'required|in:WAITING,REJECTED,ACCEPTED',
        ]);
        $edit = BccUser::with('user')->where('user_id',$request->user_id)->first();

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

    function submitUser(Request $request) {
        try {
            $request->validate([
            'papper_url'=>'required',
        ]);
        $id = Auth::user()->id;
        $submit = BccUser::with('user')->where('user_id',$id)->first();
        $name = Auth::user()->name;
 
        $papper_url = $request->file('papper_url');
        $papper_path = $papper_url->storeAs('public/bcc/'.str_replace(' ','_',$name), str_replace(' ','_',$papper_url->getClientOriginalName()));

        if (!$submit) {
            return ResponseFormatter::error(
                null,
                'Data not found',
                404
            );
        }

        $submit->update([
            'papper_url'=>$papper_path,
        ]);
        $get = config('app.url').Storage::url($papper_path);
        $submit->papper_url = $get;
        return ResponseFormatter::success(
            $submit,
            'Submit papper success'
        );
        } catch (ValidationException $error) {
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0],    
            ], 
                'Submit papper failed', 
                500,
            );
        }
    }

    function submitTeam(Request $request) {
        try {
            $request->validate([
                'url'=>'required',
                'round'=>'required',
        ]);
        $bcc_user = BccUser::with('user')->where('user_id',Auth::user()->id)->first();
        $user_team_id = $bcc_user->team_id;

        $team_name = BccTeam::with('users')->where('id', $user_team_id)->find('team_name');

        
        $url = $request->file('url');
        $url_path = $url->storeAs('public/bcc/'.str_replace(' ','_',$team_name), str_replace(' ','_',$url->getClientOriginalName()));
    
        $submit =  BccSubmission::create([
            'team_id' => $user_team_id,
            'url'=>$url_path,
            'round'=>$request->round,
        ]);

        $get = config('app.url').Storage::url($url_path);
        $submit->url = $get;

        return ResponseFormatter::success(
            $submit,
            'Submit papper success'
        );
        } catch (ValidationException $error) {
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0],    
            ], 
                'Submit papper failed', 
                500,
            );
        }
    }

    function createTeam(Request $request) {
        try {
            $request->validate([
                'team_name'=>['required', 'string', 'unique:bcc_teams,team_name'],
                'payment_url'=>'required',
        ]);


        $id = Auth::user()->id;
        
        $payment_url = $request->file('payment_url');
        $payment_url_path = $payment_url->storeAs('public/bcc/'.str_replace(' ','_',$request->team_name), str_replace(' ','_',$payment_url->getClientOriginalName()));
    
        $create =  BccTeam::create([
            'team_name' => $request->team_name,
            'leader_id'=>$id,
            'payment_url'=>$payment_url_path,
        ]);

        $user = BccUser::with('user')->where('user_id',Auth::user()->id)->first();
        $user->update([
            'team_id'=>$create->id
        ]);
        $get = config('app.url').Storage::url($payment_url_path);
        $create->payment_url = $get;
        return ResponseFormatter::success(
            $create,
            'Create Team success'
        );
        } catch (ValidationException $error) {
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0],    
            ], 
                'Create Team failed', 
                500,
            );
        }
    }
}
