<?php

use App\Http\Controllers\WebScraperController;
use App\Models\ProfessorDetails;
use App\Models\University;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

//    $universities = University::all();
//    //return response()->json($universities);
//
//
//    $professorDetails = ProfessorDetails::all();
//    //return response()->json($professorDetails);
//    $json = response()->json($professorDetails);
//    dd($json->getContent());
    $professors = \App\Models\Professor::all();
    $data = [
        'professors' => $professors
    ];
    return view('welcome', $data);
});

Route::get('/scrape', [WebScraperController::class, 'read_url_and_write_html_file']);
Route::get('/read', [WebScraperController::class, 'readUrl']);


Route::get('/command1', [WebScraperController::class, 'read_and_write_all_departments_with_university_url']);
Route::get('/test', [WebScraperController::class, 'test']);
Route::get('/test2', [WebScraperController::class, 'test2']);

