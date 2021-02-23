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

class Asset implements ToCollection , WithCalculatedFormulas , WithStartRow
{
    public function collection(Collection $rows)
    {	
     $assetstatus = session()->get('asset_status');
      if($assetstatus==2)
        {
        DB::table('lc_asset')->truncate();
        }
       foreach ($rows as $row) 
        {    
         if($row[1] != '')
        {
        $date=Carbon::now();
        $data = DB::table('lc_asset')->insert([
           [
        'asset_fundname' => $row[1],
        'asset_manager'=> $row[3],
        'asset_class'=> $row[4],
        'asset_strategy'=> $row[5],
        'asset_geo_focus'=> $row[6],
        'asset_fund_size'=> $row[7],
        'asset_inceptiondate'=> \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[8])->format('d-m-Y'),
        'asset_currency'=> $row[9],
        'asset_status'=> $row[10],
        'asset_performance_fee'=>$row[11],
        'asset_management_fee'=>$row[12],
        'asset_minimum_investment'=>$row[13],
        'asset_auditor'=> $row[14],
        'asset_custodian'=> $row[15],
        'asset_administrator'=> $row[16],
        'asset_noticeperiod'=> $row[17],
        'asset_lockupperiod'=>$row[18],
        'asset_redemptionperiod'=>$row[19],
        'asset_expected_returns'=>$row[20],
        'asset_createdon'=>$date
        ]
          ]);
        }
       }
    }

       /**
     * @return int
     */
    public function startRow(): int
    {
        return 4;
    }
}