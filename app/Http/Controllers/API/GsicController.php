<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\GsicUser;
use App\Models\GsicTeam;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Helpers\ResponseFormatter;
use Illuminate\Support\Facades\Auth;


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

        $payment_url = $request->file('payment_url');
        $payment_path = $payment_url->storeAs('public/payment', 'paymenturl_'.uniqid().'.'.$payment_url->extension());

        $gsic_team = GsicTeam::create([
            'team_name' => $request->team_name,
            'leader_id'=> $id,
            'payment_url' => $payment_path,
        ]);

        $ktm_url = $request->file('ktm_url_leader');
        $ktm_path = $ktm_url->storeAs('public/ktm', 'ktmurl_'.uniqid().'.'.$ktm_url->extension());

        $ss_follow_url = $request->file('ss_follow_url_leader');
        $ss_follow_path = $ss_follow_url->storeAs('public/follow', 'followurl_'.uniqid().'.'.$ss_follow_url->extension());
        
        $ss_poster_url = $request->file('ss_poster_url_leader');
        $ss_poster_path = $ss_poster_url->storeAs('public/poster', 'posterurl_'.uniqid().'.'.$ss_poster_url->extension());

        $gsic_user_leader = GsicUser::create([
            'team_id' => $gsic_team->id,
            'user_id' => $id,
            'ktm_url' => $ktm_path,
            'ss_follow_url' => $ss_follow_path,
            'ss_poster_url' => $ss_poster_path,
        ]);

        $ktm_url = $request->file('ktm_url_1');
        $ktm_path = $ktm_url->storeAs('public/ktm', 'ktmurl_'.uniqid().'.'.$ktm_url->extension());

        $ss_follow_url = $request->file('ss_follow_url_1');
        $ss_follow_path = $ss_follow_url->storeAs('public/follow', 'followurl_'.uniqid().'.'.$ss_follow_url->extension());

        $ss_poster_url = $request->file('ss_poster_url_1');
        $ss_poster_path = $ss_poster_url->storeAs('public/poster', 'posterurl_'.uniqid().'.'.$ss_poster_url->extension());

        $gsic_user_1 = GsicUser::create([
            'team_id' => $gsic_team->id,
            'user_id' => $request->user_id_1,
            'ktm_url' => $ktm_path,
            'ss_follow_url' => $ss_follow_path,
            'ss_poster_url' => $ss_poster_path,
        ]);

        $ktm_url = $request->file('ktm_url_2');
        $ktm_path = $ktm_url->storeAs('public/ktm', 'ktmurl_'.uniqid().'.'.$ktm_url->extension());

        $ss_follow_url = $request->file('ss_follow_url_2');
        $ss_follow_path = $ss_follow_url->storeAs('public/follow', 'followurl_'.uniqid().'.'.$ss_follow_url->extension());

        $ss_poster_url = $request->file('ss_poster_url_2');
        $ss_poster_path = $ss_poster_url->storeAs('public/poster', 'posterurl_'.uniqid().'.'.$ss_poster_url->extension());

        $gsic_user_2 = GsicUser::create([
            'team_id' => $gsic_team->id,
            'user_id' => $request->user_id_2,
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
            $gsic_team = GsicTeam::findOrFail($id);
            $gsic_team->update($request->all());
            return ResponseFormatter::success(
                $gsic_team,
                'Update GSIC team successfully'
            );
        } catch (ValidationException $error) {
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0],    
            ], 
                'Update GSIC team failed', 
                500,
            );
        }
    }


    //
}
