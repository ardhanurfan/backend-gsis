<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\GsicUser;
use App\Models\GsicTeam;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Helpers\ResponseFormatter;
use App\Models\GsicSubmission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            'user_id_1'=>'required',
            'user_id_2'=>'required',
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

        $gsic_user_leader = GsicUser::create([
            'team_id' => $gsic_team->id,
            'user_id' => $id,
            'ktm_url' => $ktm_path,
            'ss_follow_url' => $ss_follow_path,
            'ss_poster_url' => $ss_poster_path,
        ]);

        $ktm_url = $request->file('ktm_url_1');
        $ktm_path = $ktm_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ktm_url->getClientOriginalName()));

        $ss_follow_url = $request->file('ss_follow_url_1');
        $ss_follow_path = $ss_follow_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ss_follow_url->getClientOriginalName()));

        $ss_poster_url = $request->file('ss_poster_url_1');
        $ss_poster_path = $ss_poster_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ss_poster_url->getClientOriginalName()));

        $gsic_user_1 = GsicUser::create([
            'team_id' => $gsic_team->id,
            'user_id' => $request->user_id_1,
            'ktm_url' => $ktm_path,
            'ss_follow_url' => $ss_follow_path,
            'ss_poster_url' => $ss_poster_path,
        ]);

        $ktm_url = $request->file('ktm_url_2');
        $ktm_path = $ktm_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ktm_url->getClientOriginalName()));

        $ss_follow_url = $request->file('ss_follow_url_2');
        $ss_follow_path = $ss_follow_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ss_follow_url->getClientOriginalName()));

        $ss_poster_url = $request->file('ss_poster_url_2');
        $ss_poster_path = $ss_poster_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ss_poster_url->getClientOriginalName()));

        $gsic_user_2 = GsicUser::create([
            'team_id' => $gsic_team->id,
            'user_id' => $request->user_id_2,
            'ktm_url' => $ktm_path,
            'ss_follow_url' => $ss_follow_path,
            'ss_poster_url' => $ss_poster_path,
        ]);

        $get = config('app.url').Storage::url($payment_url);
        $gsic_team->payment_url = $get;

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
                'ktm_url_leader'=>'required',
                'ktm_url_1'=>'required',
                'ktm_url_2'=>'required',
                'ss_follow_url_leader'=>'required',
                'ss_follow_url_1'=>'required',
                'ss_follow_url_2'=>'required',
                'ss_poster_url_leader'=>'required',
                'ss_poster_url_1'=>'required',
                'ss_poster_url_2'=>'required',
                'user_id_1'=>'required',
                'user_id_2'=>'required',
                'leader_id'=>'required',
                'payment_url'=>'required',
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
        $payment_path = $payment_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$payment_url->getClientOriginalName()));

        $edit->update([
            'payment_url' => $payment_path,
        ]);

        $edit = GsicUser::with('user')->where('user_id',$request->leader_id)->first();

        if(!$edit){
            return ResponseFormatter::error(
                null,
                'Data not found',
                404
            );
        }

        $ktm_url = $request->file('ktm_url_leader');
        $ktm_path = $ktm_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ktm_url->getClientOriginalName()));

        $ss_follow_url = $request->file('ss_follow_url_leader');
        $ss_follow_path = $ss_follow_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ss_follow_url->getClientOriginalName()));
        
        $ss_poster_url = $request->file('ss_poster_url_leader');
        $ss_poster_path = $ss_poster_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ss_poster_url->getClientOriginalName()));

        $edit->update([
            'ktm_url' => $ktm_path,
            'ss_follow_url' => $ss_follow_path,
            'ss_poster_url' => $ss_poster_path,
        ]);

        $edit = GsicUser::with('user')->where('user_id',$request->user_id_1)->first();

        if(!$edit){
            return ResponseFormatter::error(
                null,
                'Data not found',
                404
            );
        }

        $ktm_url = $request->file('ktm_url_1');
        $ktm_path = $ktm_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ktm_url->getClientOriginalName()));

        $ss_follow_url = $request->file('ss_follow_url_1');
        $ss_follow_path = $ss_follow_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ss_follow_url->getClientOriginalName()));

        $ss_poster_url = $request->file('ss_poster_url_1');
        $ss_poster_path = $ss_poster_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ss_poster_url->getClientOriginalName()));

        $edit->update([
            'ktm_url' => $ktm_path,
            'ss_follow_url' => $ss_follow_path,
            'ss_poster_url' => $ss_poster_path,
        ]);

        $edit = GsicUser::with('user')->where('user_id',$request->user_id_2)->first();

        if(!$edit){
            return ResponseFormatter::error(
                null,
                'Data not found',
                404
            );
        }

        $ktm_url = $request->file('ktm_url_2');
        $ktm_path = $ktm_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ktm_url->getClientOriginalName()));

        $ss_follow_url = $request->file('ss_follow_url_2');
        $ss_follow_path = $ss_follow_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ss_follow_url->getClientOriginalName()));

        $ss_poster_url = $request->file('ss_poster_url_2');
        $ss_poster_path = $ss_poster_url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$ss_poster_url->getClientOriginalName()));

        $edit->update([
            'ktm_url' => $ktm_path,
            'ss_follow_url' => $ss_follow_path,
            'ss_poster_url' => $ss_poster_path,
        ]);

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
                'approve_ktm_leader'=>'required|in:WAITING,REJECTED,ACCEPTED',
                'approve_ktm_1'=>'required|in:WAITING,REJECTED,ACCEPTED',
                'approve_ktm_2'=>'required|in:WAITING,REJECTED,ACCEPTED',
                'approve_follow_leader'=>'required|in:WAITING,REJECTED,ACCEPTED',
                'approve_follow_1'=>'required|in:WAITING,REJECTED,ACCEPTED',
                'approve_follow_2'=>'required|in:WAITING,REJECTED,ACCEPTED',
                'approve_poster_leader'=>'required|in:WAITING,REJECTED,ACCEPTED',
                'approve_poster_1'=>'required|in:WAITING,REJECTED,ACCEPTED',
                'approve_poster_2'=>'required|in:WAITING,REJECTED,ACCEPTED',
                'user_id_1'=>'required',
                'user_id_2'=>'required',
                'leader_id'=>'required',
                'approve_payment'=>'required',
                'status'=>'required|in:ACTIVE,INACTIVE'
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

        $edit->update([
            'approve_payment' => $request->approve_payment,
        ]);

        $edit = GsicUser::with('user')->where('user_id',$request->leader_id)->first();

        if(!$edit){
            return ResponseFormatter::error(
                null,
                'Data not found',
                404
            );
        }

        $edit->update([
            'approve_ktm' => $request->approve_ktm_leader,
            'approve_follow' => $request->approve_ktm_leader,
            'approve_poster' => $request->approve_poster_leader,
        ]);

        $edit = GsicUser::with('user')->where('user_id',$request->user_id_1)->first();

        if(!$edit){
            return ResponseFormatter::error(
                null,
                'Data not found',
                404
            );
        }
        
        $edit->update([
            'approve_ktm' => $request->approve_ktm_1,
            'approve_follow' => $request->approve_ktm_1,
            'approve_poster' => $request->approve_poster_1,
        ]);

        $edit = GsicUser::with('user')->where('user_id',$request->user_id_2)->first();

        if(!$edit){
            return ResponseFormatter::error(
                null,
                'Data not found',
                404
            );
        }


        $edit->update([
            'approve_ktm' => $request->approve_ktm_2,
            'approve_follow' => $request->approve_ktm_2,
            'approve_poster' => $request->approve_poster_2,
        ]);

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
                'team_id'=>'required',
                'url'=>'required',
                'round'=>'required',
        ]);

        $team_name = GsicTeam::where('id', $request->team_id)->first()->team_name;

        $url = $request->file('url');
        $url_path = $url->storeAs('public/gsic/'.str_replace(' ','_',$team_name), str_replace(' ','_',$url->getClientOriginalName()));
    
        $submit =  GsicSubmission::create([
            'team_id' => $request->team_id,
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

}
