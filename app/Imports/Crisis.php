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

class Crisis implements ToCollection , WithCalculatedFormulas , WithStartRow
{
    public function collection(Collection $rows)
    {	

     $portfolio_id = session()->get('curr_portfolio_id');
     $portfolio_user = session()->get('curr_portfolio_user'); 
       foreach ($rows as $row) 
        {
         if ( $row[0] != '') {
         if(!$row[4]){
            
            $row[4]=0;
         }
          if(!$row[5]){
            $row[5]=0;
         }
         if(!$row[3]){
            $row[3]=0;
         }

           $data=DB::table('lc_crisis')->insert([
    ['crisis_portfolio_id' => $portfolio_id,'crisis_client_id' => $portfolio_user,'crisis_startdate'=>$row[0],'crisis_lastreturn'=>$row[1],'crisis_crisis'=>$row[2],'crisis_portfolioreturn'=>$row[3],'crisis_benchmark1'=>$row[4],'crisis_benchmark2'=>$row[5],'crisis_addedon'=>Carbon::now(),'crisis_addedby'=>1,'crisis_updatedon'=>Carbon::now(),'crisis_updatedby'=>1]
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