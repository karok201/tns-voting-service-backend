<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\VotingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api')->group(function () {
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('questions', [VotingController::class, 'list']);
        Route::get('questions/{id}', [VotingController::class, 'detail']);
        Route::post('vote', [VotingController::class, 'vote']);
        Route::get('history', [VotingController::class, 'history']);

// --- Blockchain endpoints ---
        Route::post('blockchain/create-vote', [VotingController::class, 'createVote']);
        Route::post('blockchain/vote/{voteId}', [VotingController::class, 'voteOnChain']);
        Route::post('blockchain/end-voting/{voteId}', [VotingController::class, 'endVoting']);
        Route::get('blockchain/votes', [VotingController::class, 'getAllVotes']);
        Route::get('departments', [DepartmentController::class, 'list']);
        Route::get('files/{id}', [FileController::class, 'download']);
    });
});
