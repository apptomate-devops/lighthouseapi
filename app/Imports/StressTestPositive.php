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

class StressTestPositive implements ToCollection , WithCalculatedFormulas , WithStartRow
{
    public function collection(Collection $rows)
    {	

     $portfolio_id = session()->get('curr_portfolio_id');
     $portfolio_user = session()->get('curr_portfolio_user');
       foreach ($rows as $row) 
        {
            if ( $row[0] != '') {
           $data=DB::table('lc_stress_test_positive')->insert([
    ['stp_client_id' => $portfolio_user,'stp_portfolio_id' => $portfolio_id,'stp_rank'=>$row[0],'stp_bear_market'=>$row[1],'stp_portfolio_bear_market'=>$row[2],'stp_dates'=>$row[3],'stp_benchmark'=>$row[4],'stp_addedon'=>Carbon::now(),'stp_addedby'=>1,'stp_updatedon'=>Carbon::now(),'stp_updatedby'=>1]
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