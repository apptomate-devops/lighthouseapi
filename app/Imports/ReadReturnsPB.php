<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use App\Imports\ReturnsPB;


class ReadReturnsPB implements WithMultipleSheets, SkipsUnknownSheets
{
public function sheets(): array
{

    return [
  
          2 => new ReturnsPB(),
       
       
    ];
}

public function onUnknownSheet($sheetName)
{
    // E.g. you can log that a sheet was not found.
    info("Sheet {$sheetName} was skipped");
}
}