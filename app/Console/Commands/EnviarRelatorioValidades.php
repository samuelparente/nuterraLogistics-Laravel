<?php

namespace App\Console\Commands;

use App\Exports\StockValidadesExport;
use App\Http\Controllers\Admin\ErpController;
use App\Mail\StockValidadesMail;
use App\Models\Admin\AppSetting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class EnviarRelatorioValidades extends Command
{
    protected $signature = 'reports:validades';
    protected $description = 'Gera e envia relatório Excel com lotes (ERP StockProperty) com validade a aproximar e stock > 0.';

    public function handle(): int
    {
       
        $monthsAhead = 6; // meses até validade

        $query = "
        SELECT
            sp.ItemID,
            n.Description      AS ProductName,
            sp.WarehouseID,
            sp.PropertyValue1 AS BatchNumber,
            CAST(sp.ExpirationDate AS DATE) AS ExpirationDate,
            sp.PhysicalQty    AS StockQty
        FROM dbo.StockProperty sp
        LEFT JOIN dbo.ItemNames n ON n.ItemID = sp.ItemID
        WHERE sp.PropertyValue1 IS NOT NULL
        AND sp.ExpirationDate IS NOT NULL
        AND sp.PhysicalQty > 0
        AND CAST(sp.ExpirationDate AS DATE) BETWEEN CAST(GETDATE() AS DATE)
                                                AND DATEADD(MONTH, {$monthsAhead}, CAST(GETDATE() AS DATE))
        ORDER BY CAST(sp.ExpirationDate AS DATE) ASC, sp.ItemID ASC
        ";

        // Executar query 
        $erp = app(ErpController::class);

        $res = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getErpToken($erp),
            'Content-Type'  => 'application/json',
        ])
            ->withoutVerifying()
            ->post($this->getErpEndpoint($erp), ['query' => $query]);

        $rows = collect($res['data'] ?? []);

        if ($rows->isEmpty()) {
            $this->info('Sem resultados. Não foi enviado email.');
            return self::SUCCESS;
        }

        //  Gerar Excel para storage/public
        $folder = 'reports/validades/' . Carbon::now()->format('Y_m');
        if (!Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->makeDirectory($folder);
        }

        $date = Carbon::now()->format('Y-m-d');
        $filename = "relatorio_validades_{$date}_" . Str::random(6) . ".xlsx";
        $filePath = "{$folder}/{$filename}";

        Excel::store(new StockValidadesExport($rows), $filePath, 'public');

        // ✅ 4) SMTP + destinatários via AppSetting (igual ao teu ReceivingController)
        $settings = AppSetting::first();
        if ($settings) {
            config([
                'mail.mailers.smtp.host'       => $settings->smtp_host,
                'mail.mailers.smtp.port'       => $settings->smtp_port,
                'mail.mailers.smtp.username'   => $settings->smtp_user,
                'mail.mailers.smtp.password'   => $settings->smtp_password,
                'mail.mailers.smtp.encryption' => $settings->smtp_encryption,
                'mail.from.address'            => $settings->smtp_from_address,
                'mail.from.name'               => $settings->smtp_from_name,
            ]);
        }

        $to = $settings?->toList() ?? [];
        $cc = $settings?->ccList() ?? [];

        if (empty($to) && empty($cc)) {
            $this->error('Sem destinatários em AppSetting (to/cc).');
            return self::FAILURE;
        }

        if (empty($to) && !empty($cc)) {
            $to[] = array_shift($cc);
        }

        Mail::to($to)->cc($cc)->send(new StockValidadesMail(
            attachmentPath: $filePath,
            totalLinhas: $rows->count()
        ));

        $this->info("Email enviado ({$rows->count()} linhas) com anexo: {$filePath}");
        return self::SUCCESS;
    }

    /**
     * Endpoint/token estão protected no ErpController.
     * Para não mexermos no controller agora, lemos via Reflection (1x, simples).
     */
    private function getErpEndpoint(ErpController $erp): string
    {
        $ref = new \ReflectionClass($erp);
        $p = $ref->getProperty('endpoint');
        $p->setAccessible(true);
        return (string) $p->getValue($erp);
    }

    private function getErpToken(ErpController $erp): string
    {
        $ref = new \ReflectionClass($erp);
        $p = $ref->getProperty('token');
        $p->setAccessible(true);
        return (string) $p->getValue($erp);
    }
}
