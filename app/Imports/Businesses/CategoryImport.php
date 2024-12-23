<?php

namespace App\Imports\Businesses;

use App\Models\Variables\BusinessCategory;
use App\Models\Variables\BusinessSubCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;

class CategoryImport implements ToModel, WithHeadingRow
{

    public function model(array $row)
    {
        $xCategory = BusinessCategory::where('code', sprintf("%04d", $row['parentid']))->get()->first();
        $categoryId = null;
        if (!is_null($xCategory)) {
            $category = BusinessCategory::firstOrCreate([
                'name' => $xCategory->name,
                'code' => $xCategory->code
            ]);
            $categoryId = $category->id;
        } else {
            $category = BusinessCategory::firstOrCreate([
                'name' => $row['name'],
                'code' => sprintf("%04d", $row['parentid'])
            ]);
            $categoryId = $category->id;
        }

        $subCategory = BusinessSubCategory::updateOrCreate([
            'code' => sprintf("%08d", $row['shaparakguildcode'])
        ], [
            'category_id' => $categoryId,
            'name' => $row['name'],
        ]);


        return $subCategory;
    }

    public function startRow(): int
    {
        return 4;
    }
}
