<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use App\Imports\Statistics_pb;

class ReadStatistics implements WithMultipleSheets, SkipsUnknownSheets
{
public function sheets(): array
{

    return [
       
        6 => new Statistics_pb(),
       
    ];
}

public function onUnknownSheet($sheetName)
{
    // E.g. you can log that a sheet was not found.
    info("Sheet {$sheetName} was skipped");
}
}