<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use App\Imports\StressTestNegative;


class ReadStressTestNegative implements WithMultipleSheets, SkipsUnknownSheets 
{
public function sheets(): array
{

    return [   
       8 => new StressTestNegative(),
    ];
}

public function onUnknownSheet($sheetName)
{
    // E.g. you can log that a sheet was not found.
    info("Sheet {$sheetName} was skipped");
}
}