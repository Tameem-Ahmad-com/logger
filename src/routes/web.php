<?php

use Illuminate\Support\Facades\Route;
use Notify\LaravelCustomLog\Http\Controllers\NotifyController;

/* all route used in notify */

Route::get('exceptions',[NotifyController::class,'index']);
Route::get('exceptions/{id}/show',[NotifyController::class,'show']);
