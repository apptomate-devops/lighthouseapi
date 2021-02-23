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

class ReturnsPB implements ToCollection , WithCalculatedFormulas , WithStartRow
{
    public function collection(Collection $rows)
    {	

     $portfolio_id = session()->get('curr_portfolio_id');
     $portfolio_user = session()->get('curr_portfolio_user');
       foreach ($rows as $row) 
        {
          if ( $row[0] != '') {
           $data=DB::table('lc_returns_pb')->insert([
    ['returnspb_clientid' => $portfolio_user,'returnspb_portfolioid' => $portfolio_id,'returnspb_date'=>\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[0])->format('m-Y'),'returnspb_lcbalanced'=>$row[1],'returnspb_msci_acwi_index'=>$row[2],'returnspb_globalequities'=>$row[3],'returnspb_addedon'=>Carbon::now(),'returnspb_addedby'=>1,'returnspb_updatedon'=>Carbon::now(),'returnspb_updatedby'=>1]
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