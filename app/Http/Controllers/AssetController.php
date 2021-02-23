<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
Use Excel;
use App\Imports\Asset;
use Illuminate\Support\Facades\Session;


class AssetController extends Controller
{
    public function create(Request $request)
    {
    	$ValidatedData = $request->validate([
        'asset_fundname' => 'required',
        'asset_manager'=> 'required',
        'asset_class'=> 'required',
        'asset_strategy'=> 'required',
        'asset_geo_focus'=> 'required',
        'asset_fund_size'=> 'required',
        'asset_inceptiondate'=> 'required',
        'asset_currency'=> 'required',
        'asset_status'=> 'required',
        'asset_performance_fee'=>'required',
        'asset_management_fee'=>'required',
        'asset_minimum_investment'=>'required',
        'asset_auditor'=> 'required',
        'asset_custodian'=> 'required',
        'asset_administrator'=> 'required',
        'asset_noticeperiod'=> 'required',
        'asset_lockupperiod'=>'required',
        'asset_redemptionperiod'=>'required',
        'asset_expected_returns'=>'required'
    	]);
	      $date=Carbon::now();

	      $data = DB::table('lc_asset')->insert([
           [
        'asset_fundname' => $request->asset_fundname,
        'asset_manager'=> $request->asset_manager,
        'asset_class'=> $request->asset_class,
        'asset_strategy'=> $request->asset_strategy,
        'asset_geo_focus'=> $request->asset_geo_focus,
        'asset_fund_size'=> $request->asset_fund_size,
        'asset_inceptiondate'=> $request->asset_inceptiondate,
        'asset_currency'=> $request->asset_currency,
        'asset_status'=> $request->asset_status,
        'asset_performance_fee'=>$request->asset_performance_fee,
        'asset_management_fee'=>$request->asset_management_fee,
        'asset_minimum_investment'=>$request->asset_minimum_investment,
        'asset_auditor'=> $request->asset_auditor,
        'asset_custodian'=> $request->asset_custodian,
        'asset_administrator'=> $request->asset_administrator,
        'asset_noticeperiod'=> $request->asset_noticeperiod,
        'asset_lockupperiod'=>$request->asset_lockupperiod,
        'asset_redemptionperiod'=>$request->asset_redemptionperiod,
        'asset_expected_returns'=>$request->asset_expected_returns,
        'asset_createdon'=>$date
        ]
	      ]);

	 if($data){
     return response()->json([
        "status"  => "Success",
        "data" => "Asset Created Successfully" 
      ],200);
      }else{
     return response()->json([
        "status"  => "Error",
        "message" => "failed to create Asset record"
    ],400);
     }

    }

    public function list(Request $request)
    {
     $skip = $request->index * $request->count;
     $data=DB::table('lc_asset')->skip($skip)->take($request->count)->get();
     $asset_count=DB::table('lc_asset')->count();
     return response()->json([
        "status"  => "Success",
        'totalrecords'=> $asset_count,
        "data" => $data
      ],200);      
    }

 public function delete($id)
   {
    DB::table('lc_asset')->where('id',$id)->delete();
     return response()->json([
             'status'  => 'success',
             'data' => 'Asset record deleted successfully'
        ], 200);
   }

    public function bulkupload(Request $request){
    $path =$request->file;
    session()->put('asset_status', $request->status);
     $data=Excel::import(new Asset, $path);
    if($data){
     return response()->json([
        "status"  => "Success",
        "data" => "Asset Inserted Successfully" 
      ],200);
      }else{
     return response()->json([
        "status"  => "Error",
        "message" => "failed to Insert Asset record"
    ],400);
     }
   }
   
}
