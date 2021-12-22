<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;


/* all route used in notify */

Route::get('exceptions',function(){
    $exceptions=DB::table(config('custom-log.mysql.table'))->orderByDesc('id')->paginate(10);
    return view('custom-log::exceptions.list',compact('exceptions'));
});
