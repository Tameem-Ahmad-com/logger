<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;


/* all route used in notify */

Route::get('exceptions',function(){
    $exceptions=DB::table(config('custom-log.mysql.table'))->cursor();
    return view('custom-log::exceptions.list',compact('exceptions'));
});
