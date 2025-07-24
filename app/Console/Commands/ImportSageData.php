<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Admin\Supplier;
use App\Models\Admin\Brand;

class ImportSageData extends Command
{
    protected $signature = 'sage:import-data';
    protected $description = 'Importa fornecedores e marcas (famílias) do Sage ERP para a base de dados local';

    protected $endpoint = 'http://nuterra.dyndns.biz:45248/api_warehouse/v1/api_sage_sql_read.php';
    protected $token = 'Qv1FC5Olt4DDFu7H6vKFUn0pvTT!n/!W8QLpY9JXBJA=p0uWVSPECJOY?Qn?MHJHZmAKscV8Syqb6QH58DV//Uelema=g0RKNFoZa?-qW3Fytp3IGfTHcs/EiGGEq/09zkIg9P=O6/qWfYyHSe/x=8g0fCVowY?!ncAlWxc-143u8ZG3qKQiAlQa6d7DUe=Rhz1HZafXnHWBhtQylHUfEC2fK9AMrj717BqX1h7WakVAD3BPK9DSq0X4LsYb/BM7';

    public function handle()
    {
        $this->info("Importando fornecedores...");
        $this->importSuppliers();

        $this->info("Importando marcas (famílias)...");
        $this->importBrands();

        $this->info("✅ Dados importados com sucesso.");
    }

    protected function importSuppliers()
    {
        $query = 'SELECT SupplierID, OrganizationName FROM dbo.Supplier ORDER BY OrganizationName';
        $response = $this->postQuery($query);

        if (!$response || !isset($response['data'])) {
            $this->error('Erro ao obter fornecedores');
            return;
        }

        foreach ($response['data'] as $row) {
            Supplier::updateOrCreate(
                ['erp_id' => $row['SupplierID']],
                ['name' => $row['OrganizationName']]
            );
        }

        $this->info("Fornecedores importados: " . count($response['data']));
    }

    protected function importBrands()
    {
        $query = 'SELECT FamilyID, Description FROM dbo.Family ORDER BY Description';
        $response = $this->postQuery($query);

        if (!$response || !isset($response['data'])) {
            $this->error('Erro ao obter marcas');
            return;
        }

        foreach ($response['data'] as $row) {
            Brand::updateOrCreate(
                ['erp_id' => $row['FamilyID']],
                ['name' => $row['Description']]
            );
        }

        $this->info("Marcas importadas: " . count($response['data']));
    }

    protected function postQuery($query)
    {
        try {
            $res = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json'
            ])
            ->withoutVerifying() // SSL desativado, como no teu template
            ->post($this->endpoint, [
                'query' => $query
            ]);

            if (!$res->successful()) {
                $this->error("Erro HTTP: " . $res->status());
                return null;
            }

            return $res->json();

        } catch (\Exception $e) {
            $this->error('Erro: ' . $e->getMessage());
            return null;
        }
    }
}
