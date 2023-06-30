<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Exhibition;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormatter;
use App\Models\DocumentationExhibition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ExhibitionController extends Controller
{
    function all(Request $request) {
        $user_id = $request->input('user_id');
        
        if ($user_id){
            $exhibition_user = Exhibition::with(['user','documentation'])->where('user_id',$user_id)->first();
            return ResponseFormatter::success(
                $exhibition_user,
                'Data peserta berhasil diambil' 
            );
        }
        $exhibition_user = Exhibition::with(['user','documentation']);
        return ResponseFormatter::success(
            $exhibition_user->get(),
            'Data peserta berhasil diambil' 
        );
    } 

    function register(Request $request) {
        try {
            $request->validate([              
                'category' => ['required', 'string'],                
                'description' => ['required', 'string'],                
                'year' => ['required', 'string'],                
                'width' => ['required', 'string'],
                'height' => ['required', 'string'],
                'url' => 'required',               
            ]);

        $photoFile = $request->file('url');
        $photoPath = $photoFile->storeAs('public/exhibition/'.str_replace(' ','_',Auth::user()->name), str_replace(' ','_',$photoFile->getClientOriginalName()));
        
        $id = Auth::id();

        $exhibition_user = Exhibition::create([
            'user_id' => $id,
            'category' => $request->category,
            'description' => $request->description,
            'year' => $request->year,
            'size' => $request->width.' x '.$request->height,
            'instagram' => $request->instagram,
            'twitter' => $request->twitter,
            'youtube' => $request->youtube,
        ]);
        $exhibition_documentation = DocumentationExhibition::create([
            'user_id' => $id,
            'url' => $photoPath,
        ]);
        return ResponseFormatter::success(
                $exhibition_user->load('documentation'),
                'Create Exhibition User successfully'
            );
        } catch (ValidationException $error) {
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0],    
            ], 
                'Create Exhibition User failed', 
                500,
            );
        }
    }

    function editFromUser(Request $request){
        try{
            $request->validate([              
                'category' => ['required', 'string'],                
                'description' => ['required', 'string'],                
                'year' => ['required', 'string'],                
                'width' => ['required', 'string'],
                'height' => ['required', 'string'], 
                'url' => 'required',                
            ]);

            $id = Auth::id();

            $photoFile = $request->file('url');
            $photoPath = $photoFile->storeAs('public/exhibition/'.str_replace(' ','_',Auth::user()->name), str_replace(' ','_',$photoFile->getClientOriginalName()));


            $edit = Exhibition::where('user_id',$id)->first();
            $editdok = DocumentationExhibition::where('user_id',$id)->first();

            if (!$edit) {
                return ResponseFormatter::error(
                    null,
                    'Data not found',
                    404
                );
            }

            $edit->update([
                'category' => $request->category,
                'description' => $request->description,
                'year' => $request->year,
                'size' => $request->width.' x '.$request->height,
                'instagram' => $request->instagram,
                'twitter' => $request->twitter,
                'youtube' => $request->youtube,
            ]);

            $editdok->update([
                'url' => $photoPath,
            ]);

            return ResponseFormatter::success(
                $edit->load('documentation'),
                'Edit Exhibition User success'
            );

        }catch(ValidationException $error){
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0],    
            ], 
                'Edit Exhibition User failed', 
                500,
            );
        }
    }
    
    function editFromAdmin(Request $request){
        try{
            $request->validate([              
                'exhibition_id' => ['required'],
                'status' => 'required|in:ACTIVE,INACTIVE'                           
            ]);

            $edit = Exhibition::where('user_id',$request->exhibition_id)->first();

            if (!$edit) {
                return ResponseFormatter::error(
                    null,
                    'Data not found',
                    404
                );
            }

            $edit->update([
                'status' => $request->status,
            ]);

            return ResponseFormatter::success(
                $edit,
                'Edit Exhibition User success'
            );

        }catch(ValidationException $error){
            return ResponseFormatter::error([
                'message' => 'Something when wrong',
                'error' => array_values($error->errors())[0][0],    
            ], 
                'Edit Exhibition User failed', 
                500,
            );
        }
    }
}
