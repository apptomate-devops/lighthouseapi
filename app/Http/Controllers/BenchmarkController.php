<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class BenchmarkController extends Controller
{
    public function store(Request $request)
    {
    DB::beginTransaction();  
    $post = DB::table('lc_current_benchmark')->where("id","1")->update(
    [
                    'benchmark1' => $request->benchmark1?$request->benchmark1:null,
                    'benchmark2' => $request->benchmark2?$request->benchmark2:null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
    DB::commit();
     if($post)
    {
    	 return response()->json([
        "status"  => "Success",
        "data" => "Benchmark Added"
      ], 200);
    }
    else
    {
      return response()->json([
        "status"  => "Error",
        "data" => "No Benchmark Added"
      ], 200);

    }
    
    }
    	
    public function benchmark_data()
    {
      $benchmarks=DB::table('lc_benchmark')->get();

     return response()->json([
        "status"  => "Success",
        "data" => $benchmarks
      ], 200);
     }

     public function list()
     {
     $current_benchmarks=DB::table('lc_current_benchmark')->get();

     $arr = json_decode($current_benchmarks,true);

    foreach($arr as $row)
    {
      $data = $row;
    }

     return response()->json([
        "status"  => "Success",
        "data" => $data  
      ], 200);
     }

     public function status()
     {
   	 $current_benchmarks=DB::table('lc_current_benchmark')->get();
   	 $arr = json_decode($current_benchmarks,true);
   	 foreach($arr as $row)
   	 {
   	 	$benchmark1=$row['benchmark1'];
   	 	$benchmark2=$row['benchmark2'];
   	 }
   	  if($benchmark1 != '' || $benchmark2 != '')
   	  {
       return response()->json([
        "status"  => "Success",
        "data" => "valid"
      ], 200);
   	  }
   	  else
   	  {
   	  	return response()->json([
        "status"  => "Error",
        "data" => "No benchmark Available"
      ], 200);
   	  }
     }


}

