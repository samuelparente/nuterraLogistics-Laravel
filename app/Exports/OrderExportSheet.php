<?php

namespace App\Exports;

use App\Models\Admin\Bonus;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

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
        $bonuses = Bonus::whereNull('deleted_at')->get();

        return $this->items->map(function ($item) use ($bonuses) {
            $applicableBonuses = $bonuses->filter(function ($b) use ($item) {
                return $b->supplier_id === $item->supplier_id || $b->brand_id === $item->brand_id;
            });

            $bonusText = $applicableBonuses->map(function ($b) {
                $desc = trim($b->description ?? '');
                $notes = trim($b->notes ?? '');
                $line = "{$b->name}";
                if ($desc) $line .= ": {$desc}";
                if ($notes) $line .= " — {$notes}";
                return $line;
            })->implode("\n");

            return [
                'SKU'              => (string) $item->product_sku,
                'Código de Barras' => "'" . (string) $item->product_barcode,
                'Produto'          => (string) $item->product_name,
                'Quantidade'       => $item->quantity,
                'Bónus'            => $bonusText,
                'Marca'            => (string) optional($item->brand)->name,
                'Fornecedor'       => (string) optional($item->supplier)->name,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'SKU',
            'Código de Barras',
            'Produto',
            'Quantidade',
            'Bónus',
            'Marca',
            'Fornecedor',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Negrito no cabeçalho
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        // Bordas para todas as células
        $sheet->getStyle('A1:G' . $sheet->getHighestRow())
              ->getBorders()
              ->getAllBorders()
              ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Formatar colunas como texto (exceto D = Quantidade)
        $columnsAsText = ['A', 'B', 'C', 'E', 'F', 'G'];

        foreach ($columnsAsText as $col) {
            $sheet->getStyle("{$col}2:{$col}" . $sheet->getHighestRow())
                  ->getNumberFormat()
                  ->setFormatCode(NumberFormat::FORMAT_TEXT);
        }

        // Ativar quebra de linha automática na coluna Bónus (E)
        $sheet->getStyle('E2:E' . $sheet->getHighestRow())
              ->getAlignment()
              ->setWrapText(true);

        return [];
    }
}
