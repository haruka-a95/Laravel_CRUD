<?php

use App\Http\Controllers\JobController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin')->group(function(){
    Route::view('', 'admin.index')->name('.index');
    Route::prefix('jobs')->name('.jobs')->controller(JobController::class)->group(function(){
        Route::get('', 'index')->name('.index');
        Route::post('', 'store')->name('.store');
        Route::get('create', 'create')->name('.create');
        Route::get('{job}', 'show')->name('.show');
        Route::get('{job}/edit', 'edit')->name('.edit');
        Route::post('{job}/confirm', 'confirm')->name('.confirm');
        Route::patch('{job}', 'update')->name('.update');
        Route::delete('{job}', 'destroy')->name('.destroy');
        Route::post('csv', 'downloadCsv')->name('.csv');
        Route::post('tsv', 'downloadTsv')->name('.tsv');
    });
});
