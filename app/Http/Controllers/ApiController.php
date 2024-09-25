<?php

namespace App\Http\Controllers;

use App\Models\Professor;
use App\Models\ProfessorDetails;
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

    public function professorsGet(): \Illuminate\Http\JsonResponse
    {
        // Fetch data from the database or other sources
        $data = Professor::all();
        $data = ProfessorDetails::all();
        return response()->json($data);
    }

    public function professorGet(string $id): \Illuminate\Http\JsonResponse
    {
        //return response()->json($id);
        //dd($request);
        // Fetch data from the database or other sources
        $professor = ProfessorDetails::where('_id', $id)->first();
        //$data = Professor::all();
        //$data = ProfessorDetails::all();
        return response()->json($professor);
    }

    public function professorsFilter(Request $request): \Illuminate\Http\JsonResponse
    {
        //return response()->json($id);
        //dd($request->first_name);
        $query = ProfessorDetails::query();

        if ($request->filled('first_name')) {
            $query->where('first_name', 'LIKE', '%' . $request->input('first_name') . '%');
        }

        if ($request->filled('last_name')) {
            $query->where('last_name', 'LIKE', '%' . $request->input('last_name') . '%');
        }

        if ($request->filled('research_fields')) {
            $query->orWhere('summary', 'LIKE', '%' . $request->input('research_fields') . '%');
            $query->orWhere('publications', 'LIKE', '%' . $request->input('research_fields') . '%');
            $query->orWhere('courses', 'LIKE', '%' . $request->input('research_fields') . '%');
            $query->orWhere('news_and_media', 'LIKE', '%' . $request->input('research_fields') . '%');
            $query->orWhere('research', 'LIKE', '%' . $request->input('research_fields') . '%');
            $query->orWhere('interests', 'LIKE', '%' . $request->input('research_fields') . '%');
        }

        $filtered_professor = $query->get();

        return response()->json($filtered_professor);
    }

    public function tagsPost(): \Illuminate\Http\JsonResponse
    {
        // Fetch data from the database or other sources
        $data = ['message' => 'Hello from Amin agha post'];
        return response()->json($data);
    }
}
