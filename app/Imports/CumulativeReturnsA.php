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


class CumulativeReturnsA implements ToCollection , WithCalculatedFormulas , WithStartRow
{
    public function collection(Collection $rows)
    {	

     $portfolio_id = session()->get('curr_portfolio_id');
     $portfolio_user = session()->get('curr_portfolio_user');
       foreach ($rows as $row) 
        {

         // if(!$row[4]){
            
         //    $row[4]=0;
         // }
         //  if(!$row[5]){
         //    $row[5]=0;
         // }
         // if(!$row[3]){
         //    $row[3]=0;
         // }
         if ( $row[0] != '') {
           $data=DB::table('lc_cumulative_returns_a')->insert([
    ['cuma_portfolioid' => $portfolio_id,'cuma_clientid' => $portfolio_user,'cuma_date'=>\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[0])->format('m-Y'),'cuma_bernardfund'=>$row[1],'cuma_creditfund'=>$row[2],'cuma_investmentfund'=>$row[3],'cuma_asiafund'=>$row[4],'cuma_fundsicav'=>$row[5],'cuma_fundsp'=>$row[6],'cuma_equityfund'=>$row[7],'cuma_biotechnologyetf'=>$row[8],'cuma_goldetc'=>$row[9],'cuma_globalfund'=>$row[10],'cuma_igneofund'=>$row[11],'cuma_invesmentas'=>$row[12],'cuma_hedgefund'=>$row[13],'cuma_growthfund'=>$row[14],'cuma_macrofund'=>$row[15],'cuma_harbortalf'=>$row[16],'cuma_addedon'=>Carbon::now(),'cuma_addedby'=>1,'cuma_updatedon'=>Carbon::now(),'cuma_updatedby'=>1]
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