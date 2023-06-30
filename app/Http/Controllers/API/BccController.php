<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\BccSubmission;
use App\Models\BccTeam;
use Illuminate\Support\Facades\Auth;
use App\Models\BccUser;
use App\Models\User;
use Carbon\Exceptions\Exception as ExceptionsException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BccController extends Controller
{
    function all(Request $request) {
        $user_id = $request->input('user_id');
        
        if ($user_id){
            $bcc_user = BccUser::with('user')->where('user_id',$user_id)->first();
            return ResponseFormatter::success(
                $bcc_user,
                'Data peserta berhasil diambil' 
            );
        }
        $bcc_user = BccUser::with('user');
        return ResponseFormatter::success(
            $bcc_user->get(),
            'Data peserta berhasil diambil' 
        );
    }

    function allTeam(Request $request) {
        $team_id = $request->input('team_id');
        
        if ($team_id){
            $bcc_team = BccTeam::with('users')->where('id',$team_id)->first();
            if ($bcc_team) {
                return ResponseFormatter::success(
                    $bcc_team,
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
        $bcc_team = BccTeam::with('users');
        return ResponseFormatter::success(
            $bcc_team->get(),
            'Data peserta berhasil diambil' 
        );
    }

    function myTeam(Request $request){
        $id = Auth::id();
        $team_id = BccUser::where('user_id',$id)->first()->team_id;
        $bcc_team = BccTeam::with('users')->where('id',$team_id)->first();
        if ($bcc_team) {
            return ResponseFormatter::success(
                $bcc_team,
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
        $edit = BccUser::with('user')->where('user_id',Auth::user()->id)->first();

        try {
            if(!$edit) {
                return ResponseFormatter::error(
                    null,
                    'Data not found',
                    404
                );
            }

        $name = Auth::user()->name;

        $ktm_url = $request->file('ktm_url');
        if($ktm_url){
            unlink(public_path(str_replace(config('app.url'),'',$edit->ktm_url)));
            $ktm_path = $ktm_url->storeAs('public/bcc/'.str_replace(' ','_',$name), str_replace(' ','_',$ktm_url->getClientOriginalName()));
            $edit->update([
                'ktm_url'=>$ktm_path,
                'approve_ktm'=>'WAITING'
            ]);
        }

        $ss_follow_url = $request->file('ss_follow_url');
        if($ss_follow_url){
            unlink(public_path(str_replace(config('app.url'),'',$edit->ss_follow_url)));
            $ss_follow_path = $ss_follow_url->storeAs('public/bcc/'.str_replace(' ','_',$name), str_replace(' ','_',$ss_follow_url->getClientOriginalName()));
            $edit->update([
                'ss_follow_url'=>$ss_follow_path,
                'approve_follow'=>'WAITING'
            ]);
        }

        $ss_poster_url = $request->file('ss_poster_url');
        if($ss_poster_url){
            unlink(public_path(str_replace(config('app.url'),'',$edit->ss_poster_url)));
            $ss_poster_path = $ss_poster_url->storeAs('public/bcc/'.str_replace(' ','_',$name), str_replace(' ','_',$ss_poster_url->getClientOriginalName()));
            $edit->update([
                'ss_poster_url'=>$ss_poster_path,
                'approve_poster'=>'WAITING'
            ]);
        }
        
        $payment_url = $request->file('payment_url');
        if($payment_url){
            unlink(public_path(str_replace(config('app.url'),'',$edit->payment_url)));
            $payment_path = $payment_url->storeAs('public/bcc/'.str_replace(' ','_',$name), str_replace(' ','_',$payment_url->getClientOriginalName()));
            $edit->update([
                'payment_url'=>$payment_path,
                'approve_payment'=>'WAITING'
            ]);
        }

        $papper_url = $request->file('papper_url');
        if($papper_url){
            unlink(public_path(str_replace(config('app.url'),'',$edit->papper_url)));
            $papper_path = $papper_url->storeAs('public/bcc/'.str_replace(' ','_',$name), str_replace(' ','_',$papper_url->getClientOriginalName()));
            $edit->update([
                'papper_url'=>$papper_path
            ]);
        }
        
        return ResponseFormatter::success(
            $edit,
            'Edit Bcc User success'
        );

        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => $error    
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
            'status'=>'in:ACTIVE,INACTIVE',
            'approve_ktm'=>'in:WAITING,REJECTED,ACCEPTED',
            'approve_follow'=>'in:WAITING,REJECTED,ACCEPTED',
            'approve_poster'=>'in:WAITING,REJECTED,ACCEPTED',
            'approve_payment'=>'in:WAITING,REJECTED,ACCEPTED',
        ]);
        $edit = BccUser::with('user')->where('user_id',$request->user_id)->first();

        if (!$edit) {
            return ResponseFormatter::error(
                null,
                'Data not found',
                404
            );
        }

        if ($request->status) {
            $edit->update([
                'status'=>$request->status,
            ]);
        }
        if ($request->approve_ktm) {
            $edit->update([
                'approve_ktm'=>$request->approve_ktm,
            ]);
        }
        if ($request->approve_follow) {
            $edit->update([
                'approve_follow'=>$request->approve_follow,
            ]);
        }
        if ($request->approve_poster) {
            $edit->update([
                'approve_poster'=>$request->approve_poster,
            ]);
        }
        if ($request->approve_payment) {
            $edit->update([
                'approve_payment'=>$request->approve_payment,
            ]);
        }

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
                'email_user_1'=>'required|exists:users,email',
                'email_user_2'=>'required|exists:users,email'
        ]);


        $id = Auth::user()->id;
        
        $payment_url = $request->file('payment_url');
        $payment_url_path = $payment_url->storeAs('public/bcc/team/'.str_replace(' ','_',$request->team_name), str_replace(' ','_',$payment_url->getClientOriginalName()));
    
        $create =  BccTeam::create([
            'team_name' => $request->team_name,
            'leader_id'=>$id,
            'payment_url'=>$payment_url_path,
        ]);

        $user = BccUser::where('user_id',Auth::user()->id)->first();
        $user->update([
            'team_id'=>$create->id
        ]);

        $id = User::where('email', $request->email_user_1)->first()->id;
        $user = BccUser::where('user_id', $id)->first();
        $user->update([
            'team_id'=>$create->id,
        ]);

        $id = User::where('email', $request->email_user_2)->first()->id;
        $user = BccUser::where('user_id', $id)->first();
        $user->update([
            'team_id'=>$create->id,
        ]);

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

    function submitTeam(Request $request) {
        try {
            $request->validate([
                'url'=>'required',
                'round'=>'required',
        ]);
        $bcc_user = BccUser::with('user')->where('user_id',Auth::id())->first();
        $user_team_id = $bcc_user->team_id;

        $team_name = BccTeam::with('users')->where('id', $user_team_id)->team_name;

        
        $url = $request->file('url');
        $url_path = $url->storeAs('public/bcc/team/'.str_replace(' ','_',$team_name), str_replace(' ','_',$url->getClientOriginalName()));
    
        $submit =  BccSubmission::create([
            'team_id' => $user_team_id,
            'url'=>$url_path,
            'round'=>$request->round,
        ]);

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

    function editFromAdminTeam(Request $request) {
        try {
            $request->validate([
            'team_id'=>'required',
            'status'=>'in:ACTIVE,INACTIVE',
            'approve_payment'=>'in:WAITING,REJECTED,ACCEPTED',
        ]);
        $edit = BccTeam::with('users')->where('id',$request->team_id)->first();

        if (!$edit) {
            return ResponseFormatter::error(
                null,
                'Data not found',
                404
            );
        }

        if ($request->status) {
            $edit->update([
                'status'=>$request->status,
            ]);
        }
        if ($request->approve_payment) {
            $edit->update([
                'approve_payment'=>$request->approve_payment,
            ]);
        }

        return ResponseFormatter::success(
            $edit,
            'Edit Bcc Team success'
        );

        } catch (ValidationException $error) {
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0],    
            ], 
                'Edit Bcc Team failed', 
                500,
            );
        }
    }

    function editFromTeam(Request $request) {
        try {
            $bcc_user = BccUser::where('user_id',Auth::user()->id)->first();
            if(!$bcc_user) {
                return ResponseFormatter::error(
                    null,
                    'Data not found',
                    404
                );
            }

            $user_team_id = $bcc_user->team_id;
            $team = BccTeam::where('id', $user_team_id)->first();
            
            $payment_url = $request->file('payment_url');
            if($payment_url){
                unlink(public_path(str_replace(config('app.url'),'',$team->payment_url)));
                $payment_path = $payment_url->storeAs('public/bcc/team/'.str_replace(' ','_',$team->team_name), str_replace(' ','_',$payment_url->getClientOriginalName()));
                $team->update([
                    'payment_url'=>$payment_path,
                    'approve_payment'=>'WAITING'
                ]);
            }
            
            return ResponseFormatter::success(
                $team,
                'Edit Bcc Team success'
            );

        } catch (Exception $error) {
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => $error    
            ], 
                'Edit Bcc Team failed', 
                500,
            );
        }
    }

    function editSubmitTeam(Request $request) {
        try {
            $request->validate([
                'url'=>'required',
                'round'=>'required',
        ]);
        $bcc_user = BccUser::where('user_id',Auth::id())->first();
        $user_team_id = $bcc_user->team_id;
        $team_name = BccTeam::find($user_team_id)->team_name;

        $submission = BccSubmission::where('team_id', $user_team_id)->where('round', $request->round)->first();

        $url = $request->file('url');
        unlink(public_path(str_replace(config('app.url'),'',$submission->url)));
        $papper_path = $url->storeAs('public/bcc/team/'.str_replace(' ','_',$team_name), str_replace(' ','_',$url->getClientOriginalName()));
        $submission->update([
            'url'=>$papper_path
        ]);

        return ResponseFormatter::success(
            $submission,
            'Edit papper success'
        );
        } catch (ValidationException $error) {
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0],    
            ], 
                'Edit papper failed', 
                500,
            );
        }
    }
}
