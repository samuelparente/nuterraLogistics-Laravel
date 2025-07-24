<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Collection;

class OrderExport implements WithMultipleSheets
{
    protected $items;

    public function __construct(Collection $items)
    {
        $this->items = $items;
    }

    public function sheets(): array
    {
        $sheets = [];

        $byBrand = $this->items->groupBy(fn($item) => $item->brand->name ?? 'Sem Marca');

        foreach ($byBrand as $brandName => $items) {
            $sheets[] = new OrderExportSheet($items, $brandName);
        }

        return $sheets;
    }
}
