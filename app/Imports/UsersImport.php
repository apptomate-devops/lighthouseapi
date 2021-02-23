<?php
namespace App\Imports;

use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class UsersImport implements ToCollection ,WithCalculatedFormulas
{
    public function collection(Collection $rows)
    {

     $portfolio_id = session()->get('curr_portfolio_id');
     $portfolio_user = session()->get('curr_portfolio_user');	
    		$i=2;
       foreach ($rows as $row) 
        {
    if ($i==1) {
    $data=DB::table('lc_pba_summary')->insert([
    ['pbasummary_clientid' => $portfolio_user,'pbasummary_portfolioid' => $portfolio_id,'pbasummary_eventid'=>$row[0],'pbasummary_timestamp'=>$row[1],'pbasummary_portfolio'=>$row[2],'pbasummary_benchmark'=>$row[3],'pbasummary_asset'=>$row[4],'pbasummary_startdate'=>$row[5],'pbasummary_enddate'=>$row[6],'pbasummary_riskfree'=>$row[7],'pbasummary_managementfee'=>$row[8],'pbasummary_addedon'=>Carbon::now(),'pbasummary_addedby'=>1,'pbasummary_updatedon'=>Carbon::now(),'pbasummary_updatedby'=>1]
     ]);
    }
	 $i=1;        
        } 
    }
}