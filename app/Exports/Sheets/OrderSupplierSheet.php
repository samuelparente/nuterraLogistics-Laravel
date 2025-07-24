<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;

class OrderSupplierSheet implements FromCollection, WithTitle
{
    protected $supplierName;
    protected $items;

    public function __construct(string $supplierName, Collection $items)
    {
        $this->supplierName = $supplierName;
        $this->items = $items;
    }

    public function title(): string
    {
        return substr($this->supplierName, 0, 31); // Excel sheet name limit
    }

    public function collection()
    {
        return $this->items->map(function ($item) {
            return [
                'SKU'        => $item->product_sku,
                'Nome'       => $item->product_name,
                'Quantidade' => $item->quantity,
                'Marca'      => $item->brand->name ?? '',
                'Fornecedor' => $item->supplier->name ?? '',
            ];
        });
    }
}
