<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\GsicTeam;
use App\Models\GsicUser;
use Illuminate\Http\Request;
use App\Models\GsicSubmission;
use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class GsicController extends Controller
{
    function all(Request $request){
        $user_id = $request->input('user_id');
        
        if ($user_id){
            $gsic_user = GsicUser::with('user')->where('user_id',$user_id)->first();
            if ($gsic_user) {
                return ResponseFormatter::success(
                    $gsic_user,
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
        $gsic_user = GsicUser::with('user');
        return ResponseFormatter::success(
            $gsic_user->get(),
            'Data peserta berhasil diambil' 
        );
    }

    function register(Request $request) {
        try {
            $request->validate([
            'team_name'=>['required', 'string', 'unique:bcc_teams,team_name'],
            'ktm_url_leader'=>'required',
            'ktm_url_1'=>'required',
            'ktm_url_2'=>'required',
            'ss_follow_url_leader'=>'required',
            'ss_follow_url_1'=>'required',
            'ss_follow_url_2'=>'required',
            'ss_poster_url_leader'=>'required',
            'ss_poster_url_1'=>'required',
            'ss_poster_url_2'=>'required',
            'email_user_1'=>'required',
            'email_user_2'=>'required',
            'payment_url'=>'required',
        ]);

        $id = Auth::id();
        $team_name = $request->team_name;

        $payment_url = $request->file('payment_url');
        $payment_path = $payment_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$payment_url->getClientOriginalName()));

        $gsic_team = GsicTeam::create([
            'team_name' => $request->team_name,
            'leader_id'=> $id,
            'payment_url' => $payment_path,
        ]);

        $ktm_url = $request->file('ktm_url_leader');
        $ktm_path = $ktm_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ktm_url->getClientOriginalName()));

        $ss_follow_url = $request->file('ss_follow_url_leader');
        $ss_follow_path = $ss_follow_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ss_follow_url->getClientOriginalName()));
        
        $ss_poster_url = $request->file('ss_poster_url_leader');
        $ss_poster_path = $ss_poster_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ss_poster_url->getClientOriginalName()));

        GsicUser::create([
            'user_id' => $id,
            'team_id' => $gsic_team->id,
            'ktm_url' => $ktm_path,
            'ss_follow_url' => $ss_follow_path,
            'ss_poster_url' => $ss_poster_path,
        ]);

        $id = User::where('email', $request->email_user_1)->first()->id;

        $ktm_url = $request->file('ktm_url_1');
        $ktm_path = $ktm_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ktm_url->getClientOriginalName()));

        $ss_follow_url = $request->file('ss_follow_url_1');
        $ss_follow_path = $ss_follow_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ss_follow_url->getClientOriginalName()));

        $ss_poster_url = $request->file('ss_poster_url_1');
        $ss_poster_path = $ss_poster_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ss_poster_url->getClientOriginalName()));

        GsicUser::create([
            'user_id' => $id,
            'team_id' => $gsic_team->id,
            'ktm_url' => $ktm_path,
            'ss_follow_url' => $ss_follow_path,
            'ss_poster_url' => $ss_poster_path,
        ]);

        $id = User::where('email', $request->email_user_2)->first()->id;

        $ktm_url = $request->file('ktm_url_2');
        $ktm_path = $ktm_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ktm_url->getClientOriginalName()));

        $ss_follow_url = $request->file('ss_follow_url_2');
        $ss_follow_path = $ss_follow_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ss_follow_url->getClientOriginalName()));

        $ss_poster_url = $request->file('ss_poster_url_2');
        $ss_poster_path = $ss_poster_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ss_poster_url->getClientOriginalName()));

        GsicUser::create([
            'user_id' => $id,
            'team_id' => $gsic_team->id,
            'ktm_url' => $ktm_path,
            'ss_follow_url' => $ss_follow_path,
            'ss_poster_url' => $ss_poster_path,
        ]);

        return ResponseFormatter::success(
            $gsic_team,
            'Create GSIC team successfully'
            );
        } catch (ValidationException $error) {
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0],    
            ], 
                'Create GSIC team failed', 
                500,
            );
        }
    }

    function editFromUser(Request $request) {
        try {
            $request->validate([
                'user_id_1'=>'required',
                'user_id_2'=>'required',
                'leader_id'=>'required',
            ]);

        $user = GsicUser::with('user')->where('user_id',Auth::user()->id)->first();
        $team_id = $user->team_id;
        
        $edit = GsicTeam::find($team_id);
        if (!$edit) {
            return ResponseFormatter::error(
                null,
                'Data not found',
                404
            );
        }
        
        $team_name = $edit->team_name;

        $payment_url = $request->file('payment_url');
        if ($payment_url) {
            unlink(public_path(str_replace(config('app.url'),'',$edit->payment_url)));
            $payment_path = $payment_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$payment_url->getClientOriginalName()));
            $edit->update([
                'payment_url' => $payment_path,
            ]);
        }

        $edit = GsicUser::with('user')->where('user_id',$request->leader_id)->first();
        if(!$edit){
            return ResponseFormatter::error(
                null,
                'Data not found',
                404
            );
        }

        $ktm_url_leader = $request->file('ktm_url_leader');
        if ($ktm_url_leader) {
            unlink(public_path(str_replace(config('app.url'),'',$edit->ktm_url)));
            $ktm_path_leader = $ktm_url_leader->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ktm_url_leader->getClientOriginalName()));
            $edit->update([
                'ktm_url' => $ktm_path_leader,
            ]);
        }

        $ss_follow_url_leader = $request->file('ss_follow_url_leader');
        if ($ss_follow_url_leader) {
            unlink(public_path(str_replace(config('app.url'),'',$edit->ss_follow_url)));
            $ss_follow_path_leader = $ss_follow_url_leader->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ss_follow_url_leader->getClientOriginalName()));
            $edit->update([
                'ss_follow_url' => $ss_follow_path_leader,
            ]);
        }
        
        $ss_poster_url_leader = $request->file('ss_poster_url_leader');
        if ($ss_poster_url_leader) {
             unlink(public_path(str_replace(config('app.url'),'',$edit->ss_poster_url)));
            $ss_poster_path_leader = $ss_poster_url_leader->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ss_poster_url_leader->getClientOriginalName()));
            $edit->update([
                'ss_poster_url' => $ss_poster_path_leader,
            ]);
        }


        $edit = GsicUser::with('user')->where('user_id',$request->user_id_1)->first();
        if(!$edit){
            return ResponseFormatter::error(
                null,
                'Data not found',
                404
            );
        }

        $ktm_url_user_1 = $request->file('ktm_url_user_1');
        if ($ktm_url_user_1) {
            unlink(public_path(str_replace(config('app.url'),'',$edit->ktm_url)));
            $ktm_path_user_1 = $ktm_url_user_1->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ktm_url_user_1->getClientOriginalName()));
            $edit->update([
                'ktm_url' => $ktm_path_user_1,
            ]);
        }

        $ss_follow_url_user_1 = $request->file('ss_follow_url_user_1');
        if ($ss_follow_url_user_1) {
            unlink(public_path(str_replace(config('app.url'),'',$edit->ss_follow_url)));
            $ss_follow_path_user_1 = $ss_follow_url_user_1->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ss_follow_url_user_1->getClientOriginalName()));
            $edit->update([
                'ss_follow_url' => $ss_follow_path_user_1,
            ]);
        }
        
        $ss_poster_url_user_1 = $request->file('ss_poster_url_user_1');
        if ($ss_poster_url_user_1) {
             unlink(public_path(str_replace(config('app.url'),'',$edit->ss_poster_url)));
            $ss_poster_path_user_1 = $ss_poster_url_user_1->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ss_poster_url_user_1->getClientOriginalName()));
            $edit->update([
                'ss_poster_url' => $ss_poster_path_user_1,
            ]);
        }

        $edit = GsicUser::with('user')->where('user_id',$request->user_id_2)->first();
        if(!$edit){
            return ResponseFormatter::error(
                null,
                'Data not found',
                404
            );
        }

        $ktm_url_user_2 = $request->file('ktm_url_user_2');
        if ($ktm_url_user_2) {
            unlink(public_path(str_replace(config('app.url'),'',$edit->ktm_url)));
            $ktm_path_user_2 = $ktm_url_user_2->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ktm_url_user_2->getClientOriginalName()));
            $edit->update([
                'ktm_url' => $ktm_path_user_2,
            ]);
        }

        $ss_follow_url_user_2 = $request->file('ss_follow_url_user_2');
        if ($ss_follow_url_user_2) {
            unlink(public_path(str_replace(config('app.url'),'',$edit->ss_follow_url)));
            $ss_follow_path_user_2 = $ss_follow_url_user_2->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ss_follow_url_user_2->getClientOriginalName()));
            $edit->update([
                'ss_follow_url' => $ss_follow_path_user_2,
            ]);
        }
        
        $ss_poster_url_user_2 = $request->file('ss_poster_url_user_2');
        if ($ss_poster_url_user_2) {
             unlink(public_path(str_replace(config('app.url'),'',$edit->ss_poster_url)));
            $ss_poster_path_user_2 = $ss_poster_url_user_2->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ss_poster_url_user_2->getClientOriginalName()));
            $edit->update([
                'ss_poster_url' => $ss_poster_path_user_2,
            ]);
        }

        return ResponseFormatter::success(
            'Edit GSIC User success'
        );

        } catch (ValidationException $error) {
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0],    
            ], 
                'Edit GSIC User failed', 
                500,
            );
        }
    }

    function editFromAdmin(Request $request) {
        try {
            $request->validate([
                'approve_ktm_leader'=>'in:WAITING,REJECTED,ACCEPTED',
                'approve_ktm_1'=>'in:WAITING,REJECTED,ACCEPTED',
                'approve_ktm_2'=>'in:WAITING,REJECTED,ACCEPTED',
                'approve_follow_leader'=>'in:WAITING,REJECTED,ACCEPTED',
                'approve_follow_1'=>'in:WAITING,REJECTED,ACCEPTED',
                'approve_follow_2'=>'in:WAITING,REJECTED,ACCEPTED',
                'approve_poster_leader'=>'in:WAITING,REJECTED,ACCEPTED',
                'approve_poster_1'=>'in:WAITING,REJECTED,ACCEPTED',
                'approve_poster_2'=>'in:WAITING,REJECTED,ACCEPTED',
                'user_id_1'=>'required',
                'user_id_2'=>'required',
                'leader_id'=>'required',
                'approve_payment'=>'required',
                'status'=>'in:ACTIVE,INACTIVE'
        ]);
        
        $team_id = GsicTeam::where('leader_id',$request->leader_id)->first();

        $edit = GsicTeam::find($team_id);
        if (!$edit) {
            return ResponseFormatter::error(
                null,
                'Data not found',
                404
            );
        }

        if ($request->approve_payment) {
            $edit->update([
                'approve_payment' => $request->approve_payment,
            ]);
        }

        $edit = GsicUser::with('user')->where('user_id',$request->leader_id)->first();
        if(!$edit){
            return ResponseFormatter::error(
                null,
                'Data not found',
                404
            );
        }
        if ($request->approve_ktm_leader) {
            $edit->update([
                'approve_ktm' => $request->approve_ktm_leader,
            ]);
        }
        if ($request->approve_follow_leader) {
            $edit->update([
                'approve_follow' => $request->approve_follow_leader,
            ]);
        }
        if ($request->approve_poster_leader) {
            $edit->update([
                'approve_poster' => $request->approve_poster_leader,
            ]);
        }
        
        $edit = GsicUser::with('user')->where('user_id',$request->user_id_1)->first();
        if(!$edit){
            return ResponseFormatter::error(
                null,
                'Data not found',
                404
            );
        }
        if ($request->approve_ktm_1) {
            $edit->update([
                'approve_ktm' => $request->approve_ktm_1,
            ]);
        }
        if ($request->approve_follow_1) {
            $edit->update([
                'approve_follow' => $request->approve_follow_1,
            ]);
        }
        if ($request->approve_poster_1) {
            $edit->update([
                'approve_poster' => $request->approve_poster_1,
            ]);
        }

        $edit = GsicUser::with('user')->where('user_id',$request->user_id_2)->first();
        if(!$edit){
            return ResponseFormatter::error(
                null,
                'Data not found',
                404
            );
        }
        if ($request->approve_ktm_2) {
            $edit->update([
                'approve_ktm' => $request->approve_ktm_2,
            ]);
        }
        if ($request->approve_follow_2) {
            $edit->update([
                'approve_follow' => $request->approve_follow_2,
            ]);
        }
        if ($request->approve_poster_2) {
            $edit->update([
                'approve_poster' => $request->approve_poster_2,
            ]);
        }

        return ResponseFormatter::success(
            'Edit GSIC User success'
        );

        } catch (ValidationException $error) {
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0],    
            ], 
                'Edit GSIC User failed', 
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

        $gsic_user = GsicUser::where('user_id',Auth::id())->first();
        $user_team_id = $gsic_user->team_id;
        $team_name = GsicTeam::find($user_team_id)->team_name;

        $url = $request->file('url');
        $url_path = $url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$url->getClientOriginalName()));
    
        $submit =  GsicSubmission::create([
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

    function editSubmitTeam(Request $request) {
        try {
            $request->validate([
                'url'=>'required',
                'round'=>'required',
        ]);
        $gsic_user = GsicUser::where('user_id',Auth::id())->first();
        $user_team_id = $gsic_user->team_id;
        $team_name = GsicTeam::find($user_team_id)->team_name;

        $submission = GsicSubmission::where('team_id', $user_team_id)->where('round', $request->round)->first();

        $url = $request->file('url');
        unlink(public_path(str_replace(config('app.url'),'',$submission->url)));
        $papper_path = $url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$url->getClientOriginalName()));
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
