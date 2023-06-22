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
            'team_name'=>'required',
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

        $gsic_team = GsicTeam::create([
            'team_name' => $request->team_name,
            'payment_url' => $request->payment_url,
            'team_name' => $request->team_name,
            'leader_id'=> $id,
            'payment_url' => $request->payment_url,
        ]);

        $gsic_user_leader = GsicUser::create([
            'team_id' => $gsic_team->id,
            'user_id' => $id,
            'ktm_url' => $request->ktm_url_leader,
            'ss_follow_url' => $request->ss_follow_url_leader,
            'ss_poster_url' => $request->ss_poster_url_leader,
        ]);

        $gsic_user_1 = GsicUser::create([
            'team_id' => $gsic_team->id,
            'user_id' => $request->user_id_1,
            'ktm_url' => $request->ktm_url_1,
            'ss_follow_url' => $request->ss_follow_url_1,
            'ss_poster_url' => $request->ss_poster_url_1,
        ]);

        $gsic_user_2 = GsicUser::create([
            'team_id' => $gsic_team->id,
            'user_id' => $request->user_id_2,
            'ktm_url' => $request->ktm_url_2,
            'ss_follow_url' => $request->ss_follow_url_2,
            'ss_poster_url' => $request->ss_poster_url_2,
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

    //
}
