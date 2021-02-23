<?php
namespace App\Imports;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class Drawdown implements ToCollection , WithCalculatedFormulas , WithStartRow
{
    public function collection(Collection $rows)
    {	
     $portfolio_id = session()->get('curr_portfolio_id');
     $portfolio_user = session()->get('curr_portfolio_user');
       foreach ($rows as $row) 
        {
         	if ( $row[0] != '') {
           $data=DB::table('lc_drawdown')->insert([
         ['drawdown_client_id' => $portfolio_user,'drawdown_portfolio_id' => $portfolio_id,'drawdown_date'=>\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[0])->format('Y-m'),'drawdown_cumulative'=>$row[1],'drawdown_drawdown'=>$row[2],'drawdown_msciindex'=>$row[3],'drawdown_globalequities'=>$row[4],'drawdown_addedon'=>Carbon::now(),'drawdown_addedby'=>1,'drawdown_updatedon'=>Carbon::now(),'drawdown_updatedby'=>1]
   ]);
    } 
    else
    {
        return;
    }     
    } 
    }

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