<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockValidadesExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize
{
    protected Collection $rows;

    /** @var array<int, array{start:int,end:int}> */
    protected array $mergeRanges = [];

    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
    }

    public function headings(): array
    {
        return [
            'SKU',        // A
            'Produto',    // B
            'Lote',       // C
            'Validade',   // D
            'Stock',      // E
        ];
    }

    public function collection()
    {
        $out = collect();
        $currentRow = 2; // linha 1 = header

        // Agrupa por SKU
        $grouped = $this->rows->groupBy(fn ($r) => (string)($r['ItemID'] ?? ''));

        foreach ($grouped as $sku => $items) {
            // Ordenar lotes por validade (e lote)
            $items = $items->sort(function ($a, $b) {
                $da = $this->normalizeDateToExcelSerial($a['ExpirationDate'] ?? null) ?? PHP_INT_MAX;
                $db = $this->normalizeDateToExcelSerial($b['ExpirationDate'] ?? null) ?? PHP_INT_MAX;

                if ($da === $db) {
                    return strcmp((string)($a['BatchNumber'] ?? ''), (string)($b['BatchNumber'] ?? ''));
                }
                return $da <=> $db;
            })->values();

            $startRow = $currentRow;

            foreach ($items as $row) {
                $produto = (string)($row['ProductName'] ?? $row['Description'] ?? '');

                $out->push([
                    'SKU'      => (string)$sku,
                    'Produto'  => $produto,
                    'Lote'     => (string)($row['BatchNumber'] ?? ''),
                    // ✅ valor REAL de data Excel (serial)
                    'Validade' => $this->normalizeDateToExcelSerial($row['ExpirationDate'] ?? null),
                    'Stock'    => (float)($row['StockQty'] ?? 0),
                ]);

                $currentRow++;
            }

            $endRow = $currentRow - 1;

            // marcar range para merge se tiver mais do que 1 linha
            if ($endRow > $startRow) {
                $this->mergeRanges[] = ['start' => $startRow, 'end' => $endRow];
            }
        }

        return $out;
    }

    /**
     * Converte o valor vindo do ERP para "Excel serial date".
     * - Pode vir como array: ['date' => '2026-01-27 00:00:00.000000', ...]
     * - Pode vir como string: '2026-01-27 ...'
     * - Pode vir null
     */
    private function normalizeDateToExcelSerial(mixed $raw): ?float
    {
        if (empty($raw)) {
            return null;
        }

        // ERP às vezes devolve array com chave 'date'
        if (is_array($raw) && isset($raw['date'])) {
            $raw = $raw['date'];
        }

        try {
            $dt = Carbon::parse((string)$raw)->startOfDay();
            return ExcelDate::PHPToExcel($dt);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        // Cabeçalho a negrito
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        // Bordas
        $sheet->getStyle('A1:E' . $highestRow)
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        // Colunas texto (SKU, Produto, Lote)
        foreach (['A', 'B', 'C'] as $col) {
            $sheet->getStyle("{$col}2:{$col}{$highestRow}")
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_TEXT);
        }

        // ✅ Validade como data real
        $sheet->getStyle("D2:D{$highestRow}")
            ->getNumberFormat()
            ->setFormatCode('yyyy-mm-dd');

        // Stock como número
        $sheet->getStyle("E2:E{$highestRow}")
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_NUMBER);

        // Merge por SKU + Produto
        foreach ($this->mergeRanges as $range) {
            $sheet->mergeCells("A{$range['start']}:A{$range['end']}");
            $sheet->mergeCells("B{$range['start']}:B{$range['end']}");
        }

        // -------------------------
        // Formatação condicional
        // -------------------------
        if ($highestRow >= 2) {
            $rangeAll = "A2:E{$highestRow}";

            // Vermelho: <= 60 dias
            $condRed = new Conditional();
            $condRed->setConditionType(Conditional::CONDITION_EXPRESSION);
            $condRed->addCondition('AND($D2<>"",$D2-TODAY()<=60)');
            $condRed->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
            $condRed->getStyle()->getFill()->getStartColor()->setARGB('FFFFC7CE');
            $condRed->getStyle()->getFont()->getColor()->setARGB('FF9C0006');

            // Amarelo: > 60 dias
            $condYellow = new Conditional();
            $condYellow->setConditionType(Conditional::CONDITION_EXPRESSION);
            $condYellow->addCondition('AND($D2<>"",$D2-TODAY()>60)');
            $condYellow->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
            $condYellow->getStyle()->getFill()->getStartColor()->setARGB('FFFFEB9C');
            $condYellow->getStyle()->getFont()->getColor()->setARGB('FF9C6500');

            $sheet->getStyle($rangeAll)->setConditionalStyles([$condRed, $condYellow]);
        }

        return [];
    }
}
