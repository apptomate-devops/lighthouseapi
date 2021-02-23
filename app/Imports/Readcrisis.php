<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use App\Imports\Crisis;

class Readcrisis implements WithMultipleSheets, SkipsUnknownSheets 
{
  public function sheets(): array
  {
    return [   
       11 => new Crisis(),
    ];
  }

   public function onUnknownSheet($sheetName)
  {
    // E.g. you can log that a sheet was not found.
    info("Sheet {$sheetName} was skipped");
  }
}