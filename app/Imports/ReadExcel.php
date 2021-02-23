<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use App\Imports\PBASummary;

class ReadExcel implements WithMultipleSheets, SkipsUnknownSheets 
{
public function sheets(): array
{

    return [   
        0 => new PBASummary(),
    ];
}

public function onUnknownSheet($sheetName)
{
    // E.g. you can log that a sheet was not found.
    info("Sheet {$sheetName} was skipped");
}
}