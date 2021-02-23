<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Adldap\Laravel\Facades\Adldap;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class ClientController extends Controller
{

   public function create(Request $request){

      $validatedData = $request->validate([
            'client_firstname' => 'required',
            'client_countrycode' => 'required',
            'client_contactno'=>'required',
            'client_email'=>'required|unique:lc_client,client_email|email',
            'client_rm_id' =>'required',
        ]
      );

      $date=Carbon::now();
      $eventid='lc'.$date->day.$date->month.$date->year.'00001';
      
      $data=DB::table('lc_client')->insert([
    ['client_rm_id'=>$request->client_rm_id,'client_firstname' => $request->client_firstname,'client_lastname' => $request->client_lastname,'client_name'=>$request->client_firstname.' '.$request->client_lastname,'client_countrycode'=>$request->client_countrycode,'client_contactno'=>$request->client_contactno,'client_email'=>$request->client_email,'client_onboard'=>'yes','client_createdon'=>Carbon::now(),'client_updatedon'=>Carbon::now(),'client_type'=>0,'client_role'=>0,'client_status'=>0,
    'client_eventid'=>$eventid]
   ]);
   $client_id = DB::getPDO()->lastInsertId();
      if($data){
     return response()->json([
      'status' => "Success",
        'data' => [
        'message' => "Client created successfully",
        'client_id' => $client_id
      ]]);
      }else{
     return response()->json([
        "status"  => "Error",
        "message" => "failed to create client record"
    ]);
  }
  }

  public function edit($id){
      
    $client_record = DB::table('lc_client')->where('client_id',$id)->get();
      $arr = json_decode($client_record, true);
         $data = [];
        foreach($arr as $row)
        {
         $row['portfolio'] = DB::table('lc_portfolio')->where('portfolio_user',"=",$id)->get();
         $data[] = $row;    
        }   
       
     return response()->json([
             'status'=>"Success",
              'data' => $data
        ], 200);   
  }

  public function update(Request $request,$id){

       $validatedData = $request->validate([
             'client_firstname' => 'required',
             'client_lastname'=>'required',
             'client_contactno'=>'required',
             'client_email'=>'required|email|unique:lc_client,client_email,'.$id.',client_id',
         ]);
  
   $data=DB::table('lc_client')->where('client_id', $id)->update(
    ['client_firstname' => $request->client_firstname,'client_lastname' => $request->client_lastname,'client_name'=>$request->client_firstname.' '.$request->client_lastname,'client_countrycode'=>$request->client_countrycode,'client_contactno'=>$request->client_contactno,'client_email'=>$request->client_email,'client_updatedon'=>Carbon::now()]
);
      if($data){
     return response()->json([
        "status"  => "Success",
        "data" => "Client Updated Successfully" 
      ]);
      }else{
     return response()->json([
        "status"  => "Error",
        "message" => "Failed to Update Client Record"
    ]);
   }
  }

public function list(Request $request)
   {
    $azure = new User();
    $azure_token = $azure->getAzureToken();
    $rm =User::find($request->rm_id);
    $role=$rm->role;
    $skip = $request->index * $request->count;
     if($request->searchterm == '')
     {
     if($role == "admin" || $role == "superadmin")
     {
     $client_record =DB::table('lc_client')->skip($skip)->take($request->count)->get();
     $client_count =DB::table('lc_client')->count();
     $arr = json_decode($client_record, true);
     $data = [];
        foreach($arr as $row)
        {
         $client_id = $row['client_id'];
         $rm_id = $row['client_rm_id'];
         $rm_email = DB::table('users')->select('email')->where('id',$rm_id)->first();
         $res = Http::withHeaders([
            'Authorization'=>$azure_token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->get('https://graph.microsoft.com/v1.0/users/'.$rm_email->email);
        $user_res = json_decode($res,true);
        $row['rm_name'] = $user_res['displayName'];
        $row['portfolio'] = DB::table('lc_portfolio')->where('portfolio_user',"=",$client_id)->get();
        $data[] = $row;
        }
      return response()->json([
             'status'=>"Success",
              'totalrecords'=> $client_count,
              'data' => $data
        ], 200); 
     }
     else
     {
     $client_record =DB::table('lc_client')->where('client_rm_id',$request->rm_id)->skip($skip)->take($request->count)->get();
     $client_count =DB::table('lc_client')->where('client_rm_id',$request->rm_id)->count();
     $arr = json_decode($client_record, true);
         $data = [];
        foreach($arr as $row)
        {
         $client_id = $row['client_id'];
         $rm_id = $row['client_rm_id'];
         $rm_email = DB::table('users')->select('email')->where('id',$rm_id)->first();
         $res = Http::withHeaders([
            'Authorization'=>$azure_token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->get('https://graph.microsoft.com/v1.0/users/'.$rm_email->email);
        $user_res = json_decode($res,true);
        $row['rm_name'] = $user_res['displayName'];
        $row['portfolio'] = DB::table('lc_portfolio')->where('portfolio_user',"=",$client_id)->get();
        $data[] = $row;
        }
      return response()->json([
             'status'=>"Success",
              'totalrecords'=> $client_count,
              'data' => $data
        ], 200); 
     }
    }
     else
      {

        if($role == "admin" || $role == "superadmin")
        {
          $searchterm = $request->searchterm;
        $client_record = DB::table('lc_client')
      ->where('client_firstname','Like',"%{$searchterm}%")
      ->orwhere('client_email','Like',"%{$searchterm}%")
      ->orwhere('client_country','Like',"%{$searchterm}%")
      ->orwhere('client_contactno','Like',"%{$searchterm}%")
      ->orwhere('client_name','Like',"%{$searchterm}%")
      ->skip($skip)->take($request->count)
      ->get();
      $arr = json_decode($client_record, true);
       $data = [];
        foreach($arr as $row)
        {
         $client_id = $row['client_id'];
         $rm_id = $row['client_rm_id'];
         $rm_email = DB::table('users')->select('email')->where('id',$rm_id)->first();
         $res = Http::withHeaders([
            'Authorization'=>$azure_token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->get('https://graph.microsoft.com/v1.0/users/'.$rm_email->email);
        $user_res = json_decode($res,true);
        $row['rm_name'] = $user_res['displayName'];
        $row['portfolio'] = DB::table('lc_portfolio')->where('portfolio_user',"=",$client_id)->get();
        $data[] = $row;
        }
     return response()->json([
             'status'=>"Success",
              'data' => $data
        ], 200);
        }
        else
        {
        $searchterm = $request->searchterm;
        $client_record = DB::table('lc_client')
        ->where('client_rm_id','=',$request->rm_id)
        ->where(function($query) use($searchterm) {
          return $query ->where('client_firstname','Like',"%{$searchterm}%")
      ->orwhere('client_email','Like',"%{$searchterm}%")
      ->orwhere('client_country','Like',"%{$searchterm}%")
      ->orwhere('client_contactno','Like',"%{$searchterm}%")
      ->orwhere('client_name','Like',"%{$searchterm}%");
         })
        ->skip($skip)->take($request->count)
         ->get();
      $arr = json_decode($client_record, true);
       $data = [];
        foreach($arr as $row)
        {
         $client_id = $row['client_id'];
         $rm_id = $row['client_rm_id'];
         $rm_email = DB::table('users')->select('email')->where('id',$rm_id)->first();
         $res = Http::withHeaders([
            'Authorization'=>$azure_token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->get('https://graph.microsoft.com/v1.0/users/'.$rm_email->email);
        $user_res = json_decode($res,true);
        $row['rm_name'] = $user_res['displayName'];
        $row['portfolio'] = DB::table('lc_portfolio')->where('portfolio_user',"=",$client_id)->get();
        $data[] = $row;
        }
     return response()->json([
             'status'=>"Success",
              'data' => $data
        ], 200);  
        }
              
   }
 }

 public function full_list(Request $request){
  $limit=250;
  $rm =User::find($request->rm_id);
  $role=$rm->role;
  if($request->searchterm == '')
  {
       if($role == "admin" || $role == "superadmin")
        {
        $data=DB::table('lc_client')
       ->select('client_id','client_name')
       ->take($limit)->get();
       $client_count =DB::table('lc_client')->count();
        }
        else
        {
         $data=DB::table('lc_client')
       ->where('client_rm_id',$request->rm_id)
       ->select('client_id','client_name')
       ->take($limit)->get();
       $client_count =DB::table('lc_client')->where('client_rm_id',$request->rm_id)->count();
       }
   }  
  else
  {
     if($role == "admin" || $role == "superadmin")
        {
           $searchterm = $request->searchterm;
           $data = DB::table('lc_client')
          ->where('client_name','Like',"%{$searchterm}%")
          ->select('client_id','client_name')
          ->take($limit)
          ->get();

             $client_count = DB::table('lc_client')
         ->where('client_name','Like',"%{$searchterm}%")
         ->select('client_id','client_name')
         ->count();
        }
        else
        {
        $searchterm = $request->searchterm;
        $data = DB::table('lc_client')
        ->where('client_rm_id','=',$request->rm_id)
        ->where(function($query) use($searchterm) {
          return $query ->where('client_name','Like',"%{$searchterm}%");
          })
       ->select('client_id','client_name')
       ->take($limit)
       ->get();

             $client_count = DB::table('lc_client')
        ->where('client_rm_id','=',$request->rm_id)
        ->where(function($query) use($searchterm) {
          return $query ->where('client_name','Like',"%{$searchterm}%");
          })
       ->select('client_id','client_name')
       ->count();
        }
  }
 return response()->json([
             'status'=>"Success",
              'totalrecords'=> $client_count,
              'data' => $data
        ], 200); 
 }

 public function totalrecord()
 {
        $data = DB::table('lc_client')
        ->select('client_id','client_name')
        ->get();
       return response()->json([
             'status'=>"Success",
              'data' => $data
        ], 200); 
 }

public function search(Request $request)
  {
   $data=DB::table('lc_client')
   ->where('client_firstname','Like',$request->searchterm)
   ->orwhere('client_email','Like',$request->searchterm)
   ->orwhere('client_country','Like',$request->searchterm)
   ->orwhere('client_contactno','Like',$request->searchterm)
   ->get();

   return response()->json([
             'status'  => 'success',
             'data' => $data
        ], 200);
  }

   public function delete($id)
   {
    $record = DB::table('lc_client')->where('client_id',$id)->delete();
    if($record)
    {
     return response()->json([
             'status'  => 'success',
             'data' => 'record deleted successfully'
        ], 200);
    }
    else
    {
      return response()->json([
             'status'  => 'success',
             'data' => 'no record found'
        ], 200);
    }
   }

    public function risksummary(Request $request)
   {
    $port_id=$request->id;
    $portfolio_record = DB::table('lc_portfolio')->where('portfolio_id','=',$port_id)->first();
    $drawdown_analysis = DB::table('lc_drawdown')->where('drawdown_portfolio_id', $port_id)->select('drawdown_date','drawdown_drawdown','drawdown_msciindex','drawdown_globalequities','drawdown_portfolio_id')->get();
       
    $draw_ana = json_decode($drawdown_analysis, true);
         $data1 = [];
        foreach($draw_ana as $row)
        {
       if($row['drawdown_drawdown'] != "")
         {
           $lc_balanced_value = number_format($row['drawdown_drawdown'],2);
         }
         else
         {
           $lc_balanced_value = "";
         }
         $data1[] = array(
          'drawdown_date_x' => $row['drawdown_date'],
          'lc_balanced_portfolio' =>$lc_balanced_value,
          'msci_acwi_index' => $row['drawdown_msciindex'],
          '50%_globalequities_50%_ig' =>$row['drawdown_globalequities'], 
     
         );
        }
       
    $drawdown_comparision_data = DB::table('lc_statistics_pb')->where('sta_pb_portfolio_id', $port_id)->select('sta_pb_assets','sta_pb_max_drawdown','sta_pb_his_annual_return_in_time_window','sta_pb_portfolio_id')->get();
    $drawdown_count = DB::table('lc_statistics_pb')->where('sta_pb_portfolio_id', $port_id)->count();

   $drawdown_comparision1 = [];
   $drawdown_comparision2 = [];
   $drawdown_comparision3 = [];
   for($i=0;$i<=$drawdown_count-1;$i++){
    if($i==0)
    {
     $drawdown_comparision1[] = array(
    'drawdown_comparision_x' =>(preg_replace('/[^\d\-.]+/', '', $drawdown_comparision_data[$i]->sta_pb_max_drawdown)),
    'drawdown_comparision_y' => number_format(($drawdown_comparision_data[$i]->sta_pb_his_annual_return_in_time_window*100),2),
    );   
   }
     if($i==1)
    {
     $x_axis =  $drawdown_comparision_data[$i]->sta_pb_assets;
     $drawdown_comparision2[] = array(
    'drawdown_comparision_x' => (preg_replace('/[^\d\-.]+/', '', $drawdown_comparision_data[$i]->sta_pb_max_drawdown)),
    'drawdown_comparision_y' => number_format(($drawdown_comparision_data[$i]->sta_pb_his_annual_return_in_time_window*100),2),
    ); 
   }
     if($i==2)
    {
     $y_axis =  $drawdown_comparision_data[$i]->sta_pb_assets;
     $drawdown_comparision3[] = array(
    'drawdown_comparision_x' =>(preg_replace('/[^\d\-.]+/', '', $drawdown_comparision_data[$i]->sta_pb_max_drawdown)),
    'drawdown_comparision_y' =>number_format(($drawdown_comparision_data[$i]->sta_pb_his_annual_return_in_time_window*100),2),
    ); 
    }
    }   
    
   return response()->json([
             'status'  => 'success',
             'data' => array(
         'portfolio_name' => $portfolio_record->portfolio_name,
         'x_axis' => $x_axis,
         'y_axis' => $y_axis,
         'drawdown_analysis' => $data1,
         'drawdown_comparision' => array($drawdown_comparision1,$drawdown_comparision2,$drawdown_comparision3)          
           )
        ], 200);

    }

  public function performancesummary(Request $request)
   {
    $port_id=$request->id;
    $drawdown_comparision_data = DB::table('lc_statistics_pb')->where('sta_pb_portfolio_id', $port_id)->select('sta_pb_assets')->get();
    $drawdown_count = DB::table('lc_statistics_pb')->where('sta_pb_portfolio_id', $port_id)->count();
       for($i=0;$i<=$drawdown_count-1;$i++){
     if($i==1)
    {
     $x_axis =  $drawdown_comparision_data[$i]->sta_pb_assets;
     }
     if($i==2)
    {
     $y_axis =  $drawdown_comparision_data[$i]->sta_pb_assets;
    }
    }   
    
    $his_performance = DB::table('lc_cumulative_returns_pb')->where('cumpb_portfolioid', $port_id)->select('cumpb_date','cumpb_balancedportfolio','cumpb_msci_aswi','cumpb_globalequities','cumpb_portfolioid')->orderBy('cumpb_date','asc')->get();
    $portfolio_record = DB::table('lc_portfolio')->where('portfolio_id','=',$port_id)->first();
    $his_performance_data = json_decode($his_performance, true);
         $data1 = [];
        foreach($his_performance_data as $row)
        {
         $data1[] = array(
          'his_performance_date_x' =>$row['cumpb_date'] ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['cumpb_date'])->format('d-m-Y') : '',
          'his_performance_bal_port_y1' => $row['cumpb_balancedportfolio'] ? number_format(($row['cumpb_balancedportfolio']*100),2) : '',
          'his_performance_msci_y2' => $row['cumpb_msci_aswi'] ? number_format(($row['cumpb_msci_aswi']*100),2) : '',
          'his_performance_global_equ_y3' => $row['cumpb_globalequities']?number_format(($row['cumpb_globalequities']*100),2):''
         );
        }

     $his_performance_metrics = DB::table('lc_statistics_pb')->where('sta_pb_portfolio_id', $port_id)->select('sta_pb_last_12_month_return','sta_pb_year_to_date_return','sta_pb_his_annual_return_in_time_window','sta_pb_annual_volatility_in_time_window','sta_pb_annual_sharpe_ratio','sta_pb_max_drawdown','sta_pb_portfolio_id')->get();
     $his_performance_metrics_data = json_decode($his_performance_metrics, true);
         $data2 = [];
        foreach($his_performance_metrics_data as $row)
        {
           $data2[] = array(
    'his_performance_metrics_12_month_return' => $row['sta_pb_last_12_month_return'],
    'his_performance_metrics_year_return' => $row['sta_pb_year_to_date_return'],
    'his_performance_metrics_annual_return' =>$row['sta_pb_his_annual_return_in_time_window'] ? number_format(($row['sta_pb_his_annual_return_in_time_window']*100),2)."%" : '',
    'his_performance_metrics_annual_volatility' =>$row['sta_pb_annual_volatility_in_time_window'] ? number_format(($row['sta_pb_annual_volatility_in_time_window']*100),2)."%" : '',
    'his_performance_metrics_sharpe_ratio' => $row['sta_pb_annual_sharpe_ratio'] ? number_format($row['sta_pb_annual_sharpe_ratio'],2) : '',
    'his_performance_metrics_max_drawdown' => $row['sta_pb_max_drawdown']       
      );
        }

    $his_time_series = DB::table('lc_monthlyreturns')->where('monreturns_portfolioid', $port_id)->select('monreturns_year','monreturns_jan','monreturns_feb','monreturns_mar','monreturns_apr','monreturns_may','monreturns_jun','monreturns_jul','monreturns_aug','monreturns_sep','monreturns_oct','monreturns_nov','monreturns_dec','monreturns_total')->get();
    $his_time_series_data = json_decode($his_time_series, true);
         $data3 = [];
        foreach($his_time_series_data as $row)
        {
         $data3[] = array(
          'his_time_series_date' => $row['monreturns_year'],
          'his_time_series_jan' => $row['monreturns_jan'],
          'his_time_series_feb' => $row['monreturns_feb'],
          'his_time_series_mar' => $row['monreturns_mar'],
          'his_time_series_apr' => $row['monreturns_apr'],
          'his_time_series_may' => $row['monreturns_may'],
          'his_time_series_jun' => $row['monreturns_jun'],
          'his_time_series_jul' => $row['monreturns_jul'],
          'his_time_series_aug' => $row['monreturns_aug'],
          'his_time_series_sep' => $row['monreturns_sep'],
          'his_time_series_oct' => $row['monreturns_oct'],
          'his_time_series_nov' => $row['monreturns_nov'],
          'his_time_series_dec' => $row['monreturns_dec'],
          'his_time_series_total' => $row['monreturns_total']
         );
        }

      
   return response()->json([
             'status'  => 'success',
             'data' => array(
         'portfolio_name' => $portfolio_record->portfolio_name,   
         'x_axis'=>$x_axis,
         'y_axis'=>$y_axis,  
         'his_performance' => $data1,
         'his_performance_metrics' => $data2,
         'his_time_series' =>$data3
           )
        ], 200);

      }

      public function assetsummary(Request $request){
        $example = "1234567";
        $subtotal =  number_format($example, 0, '.', ',');
        $port_id=$request->id;
        $asset_bond = DB::table('lc_statistics_a')->where('sta_a_portfolio_id', $port_id)->where('sta_a_strategy','=','DIrect Holdings - Bonds')->select('sta_a_assets','sta_a_isin','sta_a_currency','sta_a_suggested_allocation_dollar','sta_a_weight','sta_a_bond_rank','sta_a_currentprice','sta_a_coupon','sta_a_yield_worst','sta_a_effective_duration','sta_a_maturity_date','sta_a_call_date','sta_a_bloomberg_composite_ranking','sta_a_investment_geography','sta_a_industry_focus','sta_a_portfolio_id')->get();
        $asset_bond_data = json_decode($asset_bond, true);
        $port_rec = DB::table('lc_portfolio')->where('portfolio_id',$port_id)->get();
        $port_data = json_decode($port_rec,true);
        foreach($port_data as $row)
        {
           $core_value=$row['portfolio_core_dollar'];
        }
        
        $data1 = [];
        foreach($asset_bond_data as $row)
         {
        if($row['sta_a_weight'] != '')
        {
          $sta_weight_val = $row['sta_a_weight'];
        }
        else
        {
          $sta_weight_val = 0;
        }
         $suggested_allocation_percentage_val= (preg_replace('/[^\d\-.]+/', '', number_format($sta_weight_val,2)));

          $sug_allocation_dollar =  floor($core_value * (preg_replace('/[^\d\-.]+/', '', number_format($sta_weight_val,10))));


          if($sug_allocation_dollar != 0)
          {
            $sug_allocation_dollar_val = "$".(number_format($sug_allocation_dollar,0, '.', ','));
          }
          else
          {
            $sug_allocation_dollar_val = ""; 
          }
          if($sug_allocation_dollar_val != '' || $row['sta_a_weight'] != '')
          {
          $data1[] = array(
          'bond_name' => $row['sta_a_assets'],
          'bond_isin' => $row['sta_a_isin'],
          'bond_currency' => $row['sta_a_currency'],
          'bond_suggested_allocation_dollar' =>$sug_allocation_dollar_val,
          'bond_suggested_allocation_percentage' => $row['sta_a_weight'],
          'bond_rank' => $row['sta_a_bond_rank'],
          'bond_current_price' => $row['sta_a_currentprice'],
          'bond_coupon' => $row['sta_a_coupon'] ? number_format($row['sta_a_coupon']*100,2)."%" : '',
          'bond_yield_worst' => $row['sta_a_yield_worst'] ? $row['sta_a_yield_worst'] : '',
          'bond_modified_duration' => $row['sta_a_effective_duration'],
          'bond_maturity_date' => $row['sta_a_maturity_date'],
          'bond_call_date' => $row['sta_a_call_date'],
          'bond_bloomberg_composite_ranking' => $row['sta_a_bloomberg_composite_ranking'],
          'bond_investment_geography' => $row['sta_a_investment_geography'],
          'bond_industry_focus' => $row['sta_a_industry_focus']    
         );
        }
        }

     $asset_stock = DB::table('lc_statistics_a')->where('sta_a_portfolio_id', $port_id)->where('sta_a_strategy','=','Direct Holdings - Stocks')->select('sta_a_assets','sta_a_bloomberg_ticker','sta_a_currency','sta_a_suggested_allocation_dollar','sta_a_weight','sta_a_currentprice','sta_a_lc_target_price','sta_a_coupon','sta_a_investment_geography','sta_a_industry_focus','sta_a_portfolio_id')->get();
     $asset_stock_data = json_decode($asset_stock, true);


        
         $data2 = [];
        foreach($asset_stock_data as $row)
        {
         if($row['sta_a_lc_target_price'] != "" && $row['sta_a_currentprice'] !="") 
          $upside_potential= ($row['sta_a_lc_target_price'] / $row['sta_a_currentprice'])-1 ;
        else
          $upside_potential="";
          if($row['sta_a_coupon'] != '')
          {
           $stock_dividend_yield_val = ($row['sta_a_coupon']*100)."%";
          }
          else
          {
          $stock_dividend_yield_val = "";
          }
          if($row['sta_a_weight'] != '')
        {
          $sta_weight_val = $row['sta_a_weight'];
        }
        else
        {
          $sta_weight_val = 0;
        }
         $suggested_allocation_percentage_val= (preg_replace('/[^\d\-.]+/', '', number_format($sta_weight_val,2)));

          $sug_allocation_dollar =  floor($core_value * (preg_replace('/[^\d\-.]+/', '', number_format($sta_weight_val,10))));


          if($sug_allocation_dollar != 0)
          {
            $sug_allocation_dollar_val = "$".(number_format($sug_allocation_dollar,0, '.', ','));
          }
          else
          {
            $sug_allocation_dollar_val = ""; 
          }
          if($sug_allocation_dollar_val != '' || $row['sta_a_weight'] != '')
          {
          $data2[] = array(
          'stock_name' => $row['sta_a_assets'],
          'stock_bloomberg_ticker' => $row['sta_a_bloomberg_ticker'],
          'stock_currency' => $row['sta_a_currency'],
          'stock_suggested_allocation_dollar' =>  $sug_allocation_dollar_val,
          'stock_suggested_allocations_percentage' =>$row['sta_a_weight'],
          'stock_current_price' => $row['sta_a_currentprice'],
          'stock_lc_current_price' => $row['sta_a_lc_target_price'],
          'stock_dividend_yield' =>$stock_dividend_yield_val,
          'stock_upside_potential' => $upside_potential ,
          'stock_investment_geography' => $row['sta_a_investment_geography'],
          'stock_industry_focus' => $row['sta_a_industry_focus']                  
         );
        }
        }

    $asset_funds = DB::table('lc_statistics_a')->where('sta_a_portfolio_id', $port_id)->where('sta_a_strategy','Like','%Fund%')->select('sta_a_assets','sta_a_asset_type','sta_a_strategy','sta_a_isin','sta_a_currency','sta_a_suggested_allocation_dollar','sta_a_weight','sta_a_historical_annual_assets_inception','sta_a_annual_volatility_assets_inception','sta_a_annual_sharpe_ratio','sta_a_management_fee','sta_a_performance_fee','sta_a_correlation_MSCI_ACWI','sta_a_coupon','sta_a_investment_geography','sta_a_industry_focus','sta_a_max_drawdown','sta_a_redemption_period','sta_a_portfolio_id','sta_a_notice_period')->get();
    $asset_funds_data = json_decode($asset_funds, true);
         $data3 = [];
        foreach($asset_funds_data as $row)
        {
          if($row['sta_a_weight'] != '')
        {
          $sta_weight_val = $row['sta_a_weight'];
        }
        else
        {
          $sta_weight_val = 0;
        }
          if($row['sta_a_coupon'] != "")
          {
           $funds_dividend_yield_val =  ($row['sta_a_coupon']*100)."%";
          }
          else
          {
          $funds_dividend_yield_val = "";
          }

          $suggested_allocation_percentage_val= (preg_replace('/[^\d\-.]+/', '', number_format($sta_weight_val,2)));

          $sug_allocation_dollar =  floor($core_value * (preg_replace('/[^\d\-.]+/', '', number_format($sta_weight_val,10))));

          
          if($sug_allocation_dollar != 0)
          {
            $sug_allocation_dollar_val = "$".(number_format($sug_allocation_dollar,0, '.', ','));
          }
          else
          {
            $sug_allocation_dollar_val = ""; 
          }
          if($sug_allocation_dollar_val != '' || $row['sta_a_weight'] != '')
          {
         $data3[] = array(
          'funds_name' => $row['sta_a_assets'],
          'funds_asset_type' => $row['sta_a_asset_type'],
          'funds_strategy' => $row['sta_a_strategy'],
          'funds_isin' => $row['sta_a_isin'],
          'funds_currency' => $row['sta_a_currency'],
          'funds_suggested_allocation_dollar' => $sug_allocation_dollar_val,
          'funds_suggested_allocations_percentage' => $row['sta_a_weight'],
          'funds_annual_return_inception' =>$row['sta_a_historical_annual_assets_inception'],
          'funds_annual_volatility_inception' =>$row['sta_a_annual_volatility_assets_inception'],
          'funds_annual_sharpe_ratio' =>$row['sta_a_annual_sharpe_ratio'] ? number_format($row['sta_a_annual_sharpe_ratio'],2) : '',
          'funds_management_fee' => $row['sta_a_management_fee'],
          'funds_performance_fee' => $row['sta_a_performance_fee'],
          'funds_correlation_MSCI_ACWI' => $row['sta_a_correlation_MSCI_ACWI'] ? number_format($row['sta_a_correlation_MSCI_ACWI'],2) : '',
          'funds_dividend_yield' =>$funds_dividend_yield_val,
           'funds_notice_period' => $row['sta_a_notice_period'],
          'funds_investment_geography' => $row['sta_a_investment_geography'],
          'funds_industry_focus' => $row['sta_a_industry_focus'],
          'funds_max_drawdown' =>$row['sta_a_max_drawdown'],
          'funds_redemption_period' => $row['sta_a_redemption_period'],

         );
        }
        }

   return response()->json([
             'status'  => 'success',
             'data' => array(
         'asset_bond' => $data1,
         'asset_stock' => $data2,
         'asset_funds' =>$data3
           )
        ], 200);
     }

      public function exportexcel($id)
      {
       $portfolio_data=DB::table('lc_altsoft_job_queue')
       ->where('portfolio_id','=',$id)
       ->orderBy('id','DESC')
       ->first();

       $portfolio_detail = DB::table('lc_portfolio')->where('portfolio_id','=',$id)->first();
        if($portfolio_data)
       {
        if($portfolio_data->job_status == "2")
        {
          return response()->json([
               'status' =>"success",
               'data' =>array(
               'filename' => $portfolio_data->output_excel,
               'filepath' => public_path().'/output/archive/'.$portfolio_data->output_excel,
           //   'filepath' => 'http://techcmantix.in/lighthouse/public/output/archive/'.$portfolio_data->output_excel,
               'portfolio_excel' => $portfolio_detail->portfolio_excel
               )
          ],200);
          
        }
        else
        {
          return response()->json([
           'status'=>"failed",
           'message'=>"portfolio is processing"
          ],200);
        }
       }
       
       else
       {
        return response()->json([
          'status'=>"failed",
          'message'=>"data not found"
        ],200);
       }
     }

    public function excelstatus($id,$status){
     DB::table('lc_portfolio')->where('portfolio_id','=',$id)->update(['portfolio_excel'=>$status]);
     return response()->json([
             'status'  => 'success',
             'data' => array()
        ], 200);
    }

     public function moveto(Request $request,$id)
     {
      $client_update=DB::table('lc_client')->where('client_id', $id)->update(
    ['client_rm_id' => $request->rm_id]);

     if($client_update){
     return response()->json([
        "status"  => "Success",
        "data" => "succesfully client moved" 
      ], 200);
      }else{
     return response()->json([
        "status"  => "Error",
        "message" => "Failed to move"
    ], 201);
    }
     }
}
