<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlternativeController;
use App\Http\Controllers\CriteriaRatingController;
use App\Http\Controllers\CriteriaWeightController;
use App\Http\Controllers\DecisionController;
use App\Http\Controllers\EstimationMatrixController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NormalizationController;
use App\Http\Controllers\RankController;
use App\Http\Controllers\WeightedMatrixController;
use App\Http\Controllers\DifferenceController;

use App\Models\CriteriaRating;
use App\Models\CriteriaWeight;
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

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', [HomeController::class, 'index']);

Route::resources([
'alternatives' => AlternativeController::class,
'criteriaratings' => CriteriaRatingController::class,
'criteriaweights' => CriteriaWeightController::class
]);

Route::get('home', [HomeController::class, 'index']);

Route::get('decision', [DecisionController::class, 'index']);

Route::get('normalization', [NormalizationController::class, 'index']);

Route::get('weighted', [WeightedMatrixController::class, 'index']);

Route::get('estimated', [EstimationMatrixController::class, 'index'])->name('estimation-matrix');

Route::get('difference', [DifferenceController::class, 'index']);

Route::get('rank', [RankController::class, 'index']);
