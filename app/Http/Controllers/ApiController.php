<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getData(): \Illuminate\Http\JsonResponse
    {
        // Fetch data from the database or other sources
        $data = ['message' => 'Hello from Amin agha'];
        return response()->json($data);
    }

    public function postData(Request $request): \Illuminate\Http\JsonResponse
    {
        // Process the incoming data
        $input = $request->all();
        // Save data to the database or other processing
        return response()->json(['success' => true, 'data' => $input]);
    }
}
