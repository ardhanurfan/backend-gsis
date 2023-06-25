<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Exhibition;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;

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
        $bcc_user = BccUser::with('user');
        return ResponseFormatter::success(
            $bcc_user->get(),
            'Data peserta berhasil diambil' 
        );
    } 

    //
}
