<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\BccUser;
use App\Models\Ceremony;
use App\Models\Exhibition;
use App\Models\GsicTeam;
use App\Models\GsicUser;
use App\Models\User;
use App\Notifications\AnnouncementEmail;
use App\Notifications\AnnouncementEmailLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class AnnouncementController extends Controller
{
    function all(Request $request){
        $announce =  Announcement::all();
        return ResponseFormatter::success(
            $announce,
            'Data announcement berhasil diambil' 
        );
    }

    function getByUser(Request $request){
        $id = Auth::id();

        $cer = Ceremony::where('user_id', $id)->first();
        $gsic = GsicUser::where('user_id', $id)->first();
        $bcc = BccUser::where('user_id', $id)->first();
        $exhi = Exhibition::where('user_id', $id)->first();

        $announce = Announcement::where('status',"SENT");

        if(!$cer){
            $announce->where('type', "!=", "Ceremony");
            // array_push($announce,$temp);
        }
        if(!$gsic){
            $announce->where('type',"!=", "GSIC");
            // array_push($announce,$temp);
        }
        if(!$bcc){
            $announce->where('type',"!=", "BCC");
            // array_push($announce,$temp);
        }
        if(!$exhi){
            $announce->where('type',"!=", "Exhibition");
            // array_push($announce,$temp);
        }

        return ResponseFormatter::success(
            $announce->orderBy('updated_at', 'DESC')->get(),
            'Data announcement berhasil diambil' 
        );
    }

    function add(Request $request){
        try{
            $request->validate([
                'title' => 'required',
                'type' => 'required |in:Ceremony,Exhibition,BCC,GSIC,All',
                'description' => 'required',
                'status'=>'required |in:DRAFT,SENT'
            ]);

            $announce = Announcement::create([
                'title' => $request->title,
                'type' => $request->type,
                'description' => $request->description,
                'status' => $request->status,
                'link' => $request->link,
            ]);

            // Kirim ke email
            if ($request->status == "SENT") {
                if ($request->type == "Ceremony") {
                    $users = Ceremony::join('users', 'ceremonies.user_id', '=', 'users.id')->get();

                    if ($request->link) {
                        Notification::send($users, new AnnouncementEmailLink($request->description, $request->link));
                    } else {
                        Notification::send($users, new AnnouncementEmail($request->description));
                    }
                } else if ($request->type == "Exhibition") {
                    $users = Exhibition::join('users', 'exhibitions.user_id', '=', 'users.id')->where('status', 'ACTIVE')->get();

                    if ($request->link) {
                        Notification::send($users, new AnnouncementEmailLink($request->description, $request->link));
                    } else {
                        Notification::send($users, new AnnouncementEmail($request->description));
                    }
                } else if ($request->type == "BCC") {
                    $users = BccUser::join('users', 'bcc_users.user_id', '=', 'users.id')->where('status', 'ACTIVE')->get();

                    if ($request->link) {
                        Notification::send($users, new AnnouncementEmailLink($request->description, $request->link));
                    } else {
                        Notification::send($users, new AnnouncementEmail($request->description));
                    }
                } else if ($request->type == "GSIC") {
                    $users = GsicTeam::join('gsic_users', 'gsic_teams.id', '=', 'gsic_users.user_id')->join('users', 'gsic_users.user_id', '=', 'users.id')->where('status', 'ACTIVE')->get();

                    if ($request->link) {
                        Notification::send($users, new AnnouncementEmailLink($request->description, $request->link));
                    } else {
                        Notification::send($users, new AnnouncementEmail($request->description));
                    }
                } else {
                    $users = User::all();
                    
                    if ($request->link) {
                        Notification::send($users, new AnnouncementEmailLink($request->description, $request->link));
                    } else {
                        Notification::send($users, new AnnouncementEmail($request->description));
                    }
                }
            }

            return ResponseFormatter::success(
                $announce,
                'Create announcement User successfully'
            );
        }catch(ValidationException $error){
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0], 
            ],
              'Create announcement user failed',
              500,  
            );
        }
    }

    function edit(Request $request){
        try{
            $request->validate([
                'id' => 'required',
                'title' => 'required',
                'type' => 'required|in:Ceremony,Exhibition,BCC,GSIC,All',
                'description' => 'required',
                'status'=>'required|in:DRAFT,SENT'
            ]);

            $edit = Announcement::find($request->id);
            if (!$edit) {
                return ResponseFormatter::error(
                    null,
                    'Data not found',
                    404
                );
            }
            
            $edit->update([
                'title' => $request->title,
                'type' => $request->type,
                'description' => $request->description,
                'status' => $request->status,
                'link' => $request->link,
            ]);


            // Kirim ke email
            if ($request->status == "SENT") {
                if ($request->type == "Ceremony") {
                    $users = Ceremony::join('users', 'ceremonies.user_id', '=', 'users.id')->get();

                    if ($request->link) {
                        Notification::send($users, new AnnouncementEmailLink($request->description, $request->link));
                    } else {
                        Notification::send($users, new AnnouncementEmail($request->description));
                    }
                } else if ($request->type == "Exhibition") {
                    $users = Exhibition::join('users', 'exhibitions.user_id', '=', 'users.id')->where('status', 'ACTIVE')->get();

                    if ($request->link) {
                        Notification::send($users, new AnnouncementEmailLink($request->description, $request->link));
                    } else {
                        Notification::send($users, new AnnouncementEmail($request->description));
                    }
                } else if ($request->type == "BCC") {
                    $users = BccUser::join('users', 'bcc_users.user_id', '=', 'users.id')->where('status', 'ACTIVE')->get();

                    if ($request->link) {
                        Notification::send($users, new AnnouncementEmailLink($request->description, $request->link));
                    } else {
                        Notification::send($users, new AnnouncementEmail($request->description));
                    }
                } else if ($request->type == "GSIC") {
                    $users = GsicTeam::join('gsic_users', 'gsic_teams.id', '=', 'gsic_users.user_id')->join('users', 'gsic_users.user_id', '=', 'users.id')->where('status', 'ACTIVE')->get();

                    if ($request->link) {
                        Notification::send($users, new AnnouncementEmailLink($request->description, $request->link));
                    } else {
                        Notification::send($users, new AnnouncementEmail($request->description));
                    }
                } else {
                    $users = User::all();
                    
                    if ($request->link) {
                        Notification::send($users, new AnnouncementEmailLink($request->description, $request->link));
                    } else {
                        Notification::send($users, new AnnouncementEmail($request->description));
                    }
                }
            }

            return ResponseFormatter::success(
                $edit,
                'Create announcement User successfully'
            );
        }catch(ValidationException $error){
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0], 
            ],
              'Create announcement user failed',
              500,  
            );
        }
    }

    function delete(Request $request) {
        $request->validate([
            'id' => 'required|integer',
        ]);

        $announce = Announcement::find($request->id);

        if (!$announce) {
            return ResponseFormatter::error(
                null,
                'Data not found',
                404
            );
        }

        $announce->forceDelete();

        return ResponseFormatter::success(
            null,
            'Delete announcement successfully'
        );
    }
}
