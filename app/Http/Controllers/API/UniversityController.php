<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\University;
use Illuminate\Http\Request;

class UniversityController extends Controller
{
    // Get All University Data
    function all(Request $request) {
        $universities = University::all();
        return ResponseFormatter::success($universities, 'Get universities data success');
    }
}
