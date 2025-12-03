<?php

namespace App\Exports;

use App\Models\Admin\Receiving;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;

class ReceivingExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithTitle
{
    protected Receiving $receiving;

    /**
     * Ranges de linhas pertencentes ao mesmo produto,
     * para fazer merge nas colunas de totais/cabeçalho.
     *
     * Exemplo:
     * [
     *   ['start' => 2, 'end' => 4],
     *   ['start' => 5, 'end' => 5],
     * ]
     */
    protected array $mergeRanges = [];

    public function __construct(Receiving $receiving)
    {
        // Garante que tens tudo carregado
        $this->receiving = $receiving->loadMissing(['supplier', 'items.brand', 'items.batches']);
    }

    public function title(): string
    {
        $supplierName = $this->receiving->supplier->name ?? 'Rececao';
        return substr("Receção - {$supplierName}", 0, 31);
    }

    public function headings(): array
    {
        return [
            'SKU',                // A
            'Produto',            // B
            'Total Encomendado',  // C
            'Total Recebido',     // D
            'Diferença Total',    // E
            'Quantidade lote',    // F
            'Lote',               // G
            'Validade',           // H
            'Marca',              // I
            'Fornecedor',         // J
            'Novo Produto?',      // K
        ];
    }

    public function collection()
    {
        $rows = collect();

        // Primeira linha de dados = 2 (linha 1 é o cabeçalho)
        $currentRow = 2;

        foreach ($this->receiving->items as $item) {
            $orderedTotal  = (int) ($item->ordered_qty ?? 0);
            $receivedTotal = (int) ($item->received_qty ?? 0);
            $diffTotal     = $receivedTotal - $orderedTotal;

            $startRow = $currentRow;

            // Se não tiver lotes, ainda assim meto uma linha única com totais
            if ($item->batches->isEmpty()) {
                $rows->push([
                    'SKU'               => (string) $item->product_sku,
                    'Produto'           => (string) $item->product_name,
                    'Total Encomendado' => $orderedTotal,
                    'Total Recebido'    => $receivedTotal,
                    'Diferença Total'   => $diffTotal,
                    'Quantidade lote'   => $receivedTotal, // tudo num “lote lógico”
                    'Lote'              => '',
                    'Validade'          => '',
                    'Marca'             => (string) optional($item->brand)->name,
                    'Fornecedor'        => (string) optional($this->receiving->supplier)->name,
                    'Novo Produto?'     => $item->is_new ? 'SIM' : '',
                ]);

                $currentRow++;
                $endRow = $currentRow - 1;
            } else {
                // Uma linha por lote: totais só na 1.ª linha, depois só detalhe de lote
                $firstBatch = true;

                foreach ($item->batches as $batch) {
                    $expiry = $batch->expiry_date
                        ? Carbon::parse($batch->expiry_date)->format('Y-m-d')
                        : '';

                    $rows->push([
                        'SKU'               => (string) $item->product_sku,
                        'Produto'           => (string) $item->product_name,
                        'Total Encomendado' => $firstBatch ? $orderedTotal  : '',
                        'Total Recebido'    => $firstBatch ? $receivedTotal : '',
                        'Diferença Total'   => $firstBatch ? $diffTotal     : '',
                        'Quantidade lote'   => (int) ($batch->quantity ?? 0),
                        'Lote'              => (string) $batch->batch_number,
                        'Validade'          => $expiry,
                        'Marca'             => (string) optional($item->brand)->name,
                        'Fornecedor'        => (string) optional($this->receiving->supplier)->name,
                        'Novo Produto?'     => $item->is_new ? 'SIM' : '',
                    ]);

                    $currentRow++;
                    $firstBatch = false;
                }

                $endRow = $currentRow - 1;
            }

            // Se tiver mais do que uma linha (vários lotes), marca para merge
            if ($endRow > $startRow) {
                $this->mergeRanges[] = [
                    'start' => $startRow,
                    'end'   => $endRow,
                ];
            }
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        // Cabeçalho a negrito
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);

        // Bordas em tudo
        $sheet->getStyle('A1:K' . $sheet->getHighestRow())
              ->getBorders()
              ->getAllBorders()
              ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Colunas como texto (SKU, Produto, Lote, Marca, Fornecedor, Novo Produto)
        $columnsAsText = ['A', 'B', 'G', 'I', 'J', 'K'];

        foreach ($columnsAsText as $col) {
            $sheet->getStyle("{$col}2:{$col}" . $sheet->getHighestRow())
                  ->getNumberFormat()
                  ->setFormatCode(NumberFormat::FORMAT_TEXT);
        }

        // Coluna de data (Validade) formatada como data (H)
        $sheet->getStyle('H2:H' . $sheet->getHighestRow())
              ->getNumberFormat()
              ->setFormatCode('yyyy-mm-dd');

        // ===== Merge das células por produto =====
        foreach ($this->mergeRanges as $range) {
            $start = $range['start'];
            $end   = $range['end'];

            if ($end <= $start) {
                continue;
            }

            // SKU + Produto
            $sheet->mergeCells("A{$start}:A{$end}");
            $sheet->mergeCells("B{$start}:B{$end}");

            // Totais
            $sheet->mergeCells("C{$start}:C{$end}");
            $sheet->mergeCells("D{$start}:D{$end}");
            $sheet->mergeCells("E{$start}:E{$end}");

            // Marca, Fornecedor, Novo Produto?
            $sheet->mergeCells("I{$start}:I{$end}");
            $sheet->mergeCells("J{$start}:J{$end}");
            $sheet->mergeCells("K{$start}:K{$end}");
        }

        return [];
    }
}
