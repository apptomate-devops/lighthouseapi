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

class StressTestNegative implements ToCollection , WithCalculatedFormulas , WithStartRow
{
    public function collection(Collection $rows)
    {	

     $portfolio_id = session()->get('curr_portfolio_id');
     $portfolio_user = session()->get('curr_portfolio_user');
       foreach ($rows as $row) 
        {
            if ( $row[0] != '') {
           $data=DB::table('lc_stress_test_negative')->insert([
    ['stn_client_id' => $portfolio_user,'stn_portfolio_id' => $portfolio_id,'stn_rank'=>$row[0],'stn_bear_market'=>$row[1],'stn_portfolio_bear_market'=>$row[2],'stn_dates'=>$row[3],'stn_benchmark'=>$row[4],'stn_addedon'=>Carbon::now(),'stn_addedby'=>1,'stn_updatedon'=>Carbon::now(),'stn_updatedby'=>1]
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