<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use App\Imports\StatisticsA;


class ReadStatisticsA implements WithMultipleSheets, SkipsUnknownSheets
{
public function sheets(): array
{

    return [
  
        
        7 => new StatisticsA(),
        
       
    ];
}

public function onUnknownSheet($sheetName)
{
    // E.g. you can log that a sheet was not found.
    info("Sheet {$sheetName} was skipped");
}
}