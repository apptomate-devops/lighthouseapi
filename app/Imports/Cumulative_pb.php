<?php
namespace App\Imports;

use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Session;


class Cumulative_pb implements ToCollection,WithCalculatedFormulas , WithStartRow
{
    public function collection(Collection $rows)
    {	

     $portfolio_id = session()->get('curr_portfolio_id');
     $portfolio_user = session()->get('curr_portfolio_user');
       foreach ($rows as $row) 
        {
        if ( $row[0] != '') {
     $data=DB::table('lc_cumulative_returns_pb')->insert([
    ['cumpb_clientid' => $portfolio_user,'cumpb_portfolioid' => $portfolio_id,'cumpb_date'=>\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[0])->format('m-Y'),'cumpb_balancedportfolio'=>$row[1],'cumpb_msci_aswi'=>$row[2],'cumpb_globalequities'=>$row[3],'cumpb_addedon'=>Carbon::now(),'cumpb_addedby'=>1,'cumpb_updatedon'=>Carbon::now(),'cumpb_updatedby'=>1]
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