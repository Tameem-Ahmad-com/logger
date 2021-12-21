<?php

use Illuminate\Support\Facades\Route;
use Notify\Http\Controllers\NotifyController;

/* all route used in notify */

Route::get('/exceptions', [NotifyController::class, 'getExceptions']);
