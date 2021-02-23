<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use App\Imports\Drawdown;


class ReadDrawdown implements WithMultipleSheets, SkipsUnknownSheets
{
public function sheets(): array
{
    return [
     10 => new Drawdown(),
  ];
}

public function onUnknownSheet($sheetName)
{
    // E.g. you can log that a sheet was not found.
    info("Sheet {$sheetName} was skipped");
}
}