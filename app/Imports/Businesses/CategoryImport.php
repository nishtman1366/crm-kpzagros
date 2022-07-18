<?php

namespace App\Imports\Businesses;

use App\Models\Variables\BusinessCategory;
use App\Models\Variables\BusinessSubCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class CategoryImport implements ToModel, WithStartRow
{

    public function model(array $row)
    {
        $category = BusinessCategory::firstOrCreate([
            'name' => $row[2],
            'code' => $row[1]
        ]);
        $subCategory = BusinessSubCategory::create([
            'category_id' => $category->id,
            'name' => $row[3],
            'code' => $row[0]
        ]);
    }

    public function startRow(): int
    {
        return 4;
    }
}
