<?php
namespace App\Imports;

use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class MonthlyReturns implements ToCollection , WithCalculatedFormulas , WithStartRow
{
    public function collection(Collection $rows)
    {	

     $portfolio_id = session()->get('curr_portfolio_id');
     $portfolio_user = session()->get('curr_portfolio_user');
       foreach ($rows as $row) 
        {
    if ( $row[0] != '') {
           $data=DB::table('lc_monthlyreturns')->insert([
    ['monreturns_clientid' => $portfolio_user,'monreturns_portfolioid' => $portfolio_id,'monreturns_year'=>$row[0],'monreturns_jan'=>$row[1],'monreturns_feb'=>$row[2],'monreturns_mar'=>$row[3],'monreturns_apr'=>$row[4],'monreturns_may'=>$row[5],'monreturns_jun'=>$row[6],'monreturns_jul'=>$row[7],'monreturns_aug'=>$row[8],'monreturns_sep'=>$row[9],'monreturns_oct'=>$row[10],'monreturns_nov'=>$row[11],'monreturns_dec'=>$row[12],'monreturns_total'=>$row[13],'monreturns_addedon'=>Carbon::now(),'monreturns_addedby'=>1,'monreturns_updatedon'=>Carbon::now(),'monreturns_updatedby'=>1]
   ]);  
   }
   else 
   {
    return;
   }     
        } 
    }

         /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }
    public function batchSize(): int
    {
        return 1000;
    }
    public function chunkSize(): int
    {
        return 1000;
    }
}