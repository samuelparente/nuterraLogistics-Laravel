<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OrderExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, ShouldAutoSize
{
    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function collection()
    {
        return collect($this->items)->map(function ($item) {
            // Lógica para determinar os bónus
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

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $range = 'A1:' . $highestColumn . $highestRow;

        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        return [];
    }

    public function columnWidths(): array
    {
        return []; // Deixa vazio pois ShouldAutoSize está ativado
    }
}
