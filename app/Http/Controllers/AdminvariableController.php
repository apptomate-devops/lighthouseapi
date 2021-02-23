<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Rules\CheckTimeWindow;

class AdminvariableController extends Controller
{
    public function store(Request $request)
    {
      
      $validation = $request->validate([
      'performance_fee'=>'required',
      'management_fee'=>'required',
      'time_window_start'=>['required',new CheckTimeWindow],
      ]);

    
    $post = DB::table('lc_admin_variable')->where("id","1")->update(
    [
        'performance_fee' => $request->performance_fee,
        'management_fee' => $request->management_fee,
        'expected_annual_return_min' => $request->expected_annual_return_min?$request->expected_annual_return_min:null,
        'expected_annual_return_max' => $request->expected_annual_return_max?$request->expected_annual_return_max:null,
        'max_drawdown_min' => $request->max_drawdown_min?$request->max_drawdown_min:null,
        'max_drawdown_max' => $request->max_drawdown_max?$request->max_drawdown_max:null,
        'time_window_start' => $request->time_window_start,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
                ]);
     if($post)
    {
    	 return response()->json([
        "status"  => "Success",
        "data" => "Admin Variables Added"
      ], 200);
    }
    else
    {
      return response()->json([
        "status"  => "Error",
        "data" => "No Values Added"
      ], 200);
    }
    }
    public function list()
    {
    	$admin_variable=DB::table('lc_admin_variable')->get();

      $arr = json_decode($admin_variable,true);

      foreach($arr as $row)
      {
        $data =$row;
      }

     return response()->json([
        "status"  => "Success",
        "data" => $data
      ], 200);
    }
}
