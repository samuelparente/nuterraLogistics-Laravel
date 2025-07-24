<?php
namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrderExportSheet implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    protected $items;
    protected $title;

    public function __construct(Collection $items, string $title)
    {
        $this->items = $items;
        $this->title = substr($title, 0, 31); // Limite do Excel
    }

    public function title(): string
    {
        return $this->title;
    }

    public function collection()
    {
        return $this->items->map(function ($item) {
            $bonus = null;

            if ($item->brand && $item->brand->bonuses->isNotEmpty()) {
                $bonus = $item->brand->bonuses->first()->description;
            } elseif ($item->supplier && $item->supplier->bonuses->isNotEmpty()) {
                $bonus = $item->supplier->bonuses->pluck('description')->implode(', ');
            }

            return [
                'SKU'              => (string) $item->product_sku,
                'Código de Barras' => "'" . $item->product_barcode,
                'Produto'          => $item->product_name,
                'Marca'            => $item->brand->name ?? '',
                'Fornecedor'       => $item->supplier->name ?? '',
                'Bonificações'     => $bonus ?? '',
                'Quantidade'       => $item->quantity,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'SKU',
            'Código de Barras',
            'Produto',
            'Marca',
            'Fornecedor',
            'Bonificações',
            'Quantidade',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        $sheet->getStyle('A1:G' . $sheet->getHighestRow())
              ->getBorders()
              ->getAllBorders()
              ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        return [];
    }
}
