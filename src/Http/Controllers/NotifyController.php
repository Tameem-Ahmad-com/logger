<?php 
namespace Notify\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Notify\Http\Controllers\Controller;

class NotifyController extends Controller{
    
  
    public static function getExceptions()
    {
        $exceptions=DB::table(config('custom-log.mysql.table'))->get();
        return view(__DIR__.'/resources/views/exceptions/list',compact('exceptions'));
    }

}