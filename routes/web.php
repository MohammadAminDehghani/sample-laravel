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
    return view('welcome');
});

Route::get('/scrape', [WebScraperController::class, 'scrape']);
Route::get('/read', [WebScraperController::class, 'readUrl']);
