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

class ReturnsA implements ToCollection , WithCalculatedFormulas , WithStartRow
{
    public function collection(Collection $rows)
    {	

     $portfolio_id = session()->get('curr_portfolio_id');
     $portfolio_user = session()->get('curr_portfolio_user');
       foreach ($rows as $row) 
        {
          if ( $row[0] != '') {
           $data=DB::table('lc_returns_a')->insert([
    ['returnsa_clientid' => $portfolio_user,'returnsa_portfolioid' => $portfolio_id,'returnsa_date'=>\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[0])->format('m-Y'),'returnsa_bernardfund'=>$row[1],'returnsa_creditfund'=>$row[2],'returnsa_investmentfund'=>$row[3],'returnsa_asiafund'=>$row[4],'returnsa_fundsicav'=>$row[5],'returnsa_fundsp'=>$row[6],'returnsa_equityfund'=>$row[7],'returnsa_biotechnologyetf'=>$row[8],'returnsa_goldetc'=>$row[9],'returnsa_globalfund'=>$row[10],'returnsa_igneofund'=>$row[11],'returnsa_investmentas'=>$row[12],'returnsa_hedgefund'=>$row[13],'returnsa_growthfund'=>$row[14],'returnsa_macrofund'=>$row[15],'returnsa_harbortalf'=>$row[16],'returnsa_addedon'=>Carbon::now(),'returnsa_addedby'=>1,'returnsa_updatedon'=>Carbon::now(),'returnsa_updatedby'=>1]
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