<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Notify\LaravelCustomLog\Http\Controllers\NotifyController;

/* all route used in notify */

Route::get('exceptions',[NotifyController::class,'index']);
