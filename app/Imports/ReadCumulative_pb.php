<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use App\Imports\Cumulative_pb;

class ReadCumulative_pb implements WithMultipleSheets, SkipsUnknownSheets
{
public function sheets(): array
{

    return [
  
      
        4 => new Cumulative_pb(),
     
       
    ];
}

public function onUnknownSheet($sheetName)
{
    // E.g. you can log that a sheet was not found.
    info("Sheet {$sheetName} was skipped");
}
}