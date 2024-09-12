<?php

namespace App\Exports;

use App\Models\House;
use Maatwebsite\Excel\Concerns\FromCollection;

class HouseExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return House::all();
    }
}
